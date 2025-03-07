<?php
// Start session and include the database connection
session_start();
include('db_connection.php');

// Get booking ID from URL
$booking_id = $_GET['booking_id'];

// Fetch booking and payment details from the database
$query = "SELECT b.name, u.phone, b.num_people, b.safari_date, s.cost_per_person, p.total_amount, p.payment_date 
          FROM payments p
          JOIN bookings b ON p.booking_id = b.id
          JOIN safaris s ON b.tour_id = s.id
          JOIN users u ON b.user_id = u.id
          WHERE p.booking_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->bind_result($user_name, $user_phone, $num_people, $safari_date, $cost_per_person, $total_amount, $payment_date);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Bill</title>
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

        .bill {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .bill table {
            width: 100%;
            margin-bottom: 20px;
        }

        .bill table, .bill th, .bill td {
            border: 1px solid #ddd;
            border-collapse: collapse;
        }

        .bill th, .bill td {
            padding: 8px;
            text-align: left;
        }

        .bill th {
            background-color: #007bff;
            color: white;
        }

        .buttons {
            text-align: center;
        }

        .buttons button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
        }

        .buttons button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Payment Bill</h1>

    <div class="bill">
        <h3>Payment Successful</h3>
        <p><strong>Booking ID:</strong> <?php echo $booking_id; ?></p>

        <table>
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($user_name); ?></td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td><?php echo htmlspecialchars($user_phone); ?></td>
            </tr>
            <tr>
                <th>Safari Date</th>
                <td><?php echo htmlspecialchars($safari_date); ?></td>
            </tr>
            <tr>
                <th>Number of People</th>
                <td><?php echo $num_people; ?></td>
            </tr>
            <tr>
                <th>Cost per Person</th>
                <td><?php echo "Rs " . number_format($cost_per_person, 2); ?></td>
            </tr>
            <tr>
                <th>Total Amount</th>
                <td><?php echo "Rs " . number_format($total_amount, 2); ?></td>
            </tr>
            <tr>
                <th>Payment Date</th>
                <td><?php echo $payment_date; ?></td>
            </tr>
        </table>

        <div class="buttons">
            <button onclick="window.print()">Print Bill</button>
            <a href="index.php"><button>Back to Home</button></a>
        </div>
    </div>
</body>
</html>
