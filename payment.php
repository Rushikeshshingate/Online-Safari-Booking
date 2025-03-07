<?php
// Start session and include the database connection
session_start();
include('db_connection.php');

// Fetch the booking details from the database using booking ID passed through URL
$booking_id = $_GET['booking_id'] ?? null; // Using null coalescing operator for safety

if ($booking_id) {
    $query = "SELECT b.id, u.name, b.num_people, b.safari_date, s.cost_per_person 
              FROM bookings b 
              JOIN users u ON b.user_id = u.id
              JOIN safaris s ON b.tour_id = s.id
              WHERE b.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->bind_result($user_id, $user_name, $num_people, $safari_date, $cost_per_person);
    
    if ($stmt->fetch()) {
        // Calculating the total amount
        $total_amount = $cost_per_person * $num_people;
    } else {
        // Handle case when no booking is found for the given booking ID
        echo "No booking found with the given ID.";
        exit;
    }
    $stmt->close();
} else {
    echo "No booking ID provided.";
    exit;
}

// Handle the form submission for payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form values
    $card_type = $_POST['card_type'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';

    // Sanitize and validate form inputs
    if (empty($card_type) || empty($card_number) || empty($cvv) || empty($expiry_date)) {
        echo "<p class='error'>All fields are required!</p>";
    } else {
        // Store payment details in the database and update payment status
        $conn->begin_transaction();

        try {
            // Insert payment details
            $stmt = $conn->prepare("INSERT INTO payments (booking_id, card_type, card_number, cvv, expiry_date, total_amount, payment_status) 
                                    VALUES (?, ?, ?, ?, ?, ?, 'completed')");
            $stmt->bind_param("issssi", $booking_id, $card_type, $card_number, $cvv, $expiry_date, $total_amount);
            
            if ($stmt->execute()) {
                // Commit the transaction and redirect to the bill page
                $conn->commit();
                echo "<p>Payment successful!</p>";
                header("Location: bill.php?booking_id=$booking_id");
                exit;
            } else {
                throw new Exception("Error inserting payment: " . $stmt->error);
            }
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $conn->rollback();
            echo "<p class='error'>Transaction failed: " . $e->getMessage() . "</p>";
        } finally {
            $stmt->close();
        }
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }

        .payment-form {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .payment-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .payment-form input, .payment-form select, .payment-form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .payment-form button {
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .payment-form button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Payment Page</h1>

    <div class="payment-form">
        <form method="POST" action="payment.php?booking_id=<?php echo $booking_id; ?>">
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>" readonly>

            <label for="card_type">Card Type:</label>
            <select id="card_type" name="card_type" required>
                <option value="" disabled selected>Select Card Type</option>
                <option value="Debit Card">Debit Card</option>
                <option value="Credit Card">Credit Card</option>
            </select>

            <label for="card_number">Card Number:</label>
            <input type="text" id="card_number" name="card_number" required placeholder="Enter card number">

            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" required placeholder="Enter CVV">

            <label for="expiry_date">Expiry Date:</label>
            <input type="month" id="expiry_date" name="expiry_date" required>

            <label for="total_amount">Total Amount:</label>
            <input type="text" id="total_amount" name="total_amount" value="<?php echo $total_amount; ?>" readonly>

            <button type="submit">Submit Payment</button>
        </form>
    </div>
</body>
</html>
