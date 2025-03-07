    <?php
    // Start the session
    session_start();
    include('db_connection.php'); 

    // Fetch user information from the database
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in the session during login
    $user_query = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $user_query->bind_result($user_name, $user_email);
    $user_query->fetch();
    $user_query->close();

    // Fetch safari options from the database
    $safaris = [];
    $safari_query = $conn->query("SELECT id, name FROM safaris");
    if ($safari_query->num_rows > 0) {
        while ($row = $safari_query->fetch_assoc()) {
            $safaris[$row['id']] = $row['name'];
        }
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tour_id = $_POST['tour_id'];
        $identity_type = $_POST['identity_type'];
        $identity_number = $_POST['identity_number'];
        $safari_date = $_POST['safari_date'];
        $num_people = $_POST['num_people'];

        // Insert booking information into the database
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, name, email, tour_id, identity_type, identity_number, safari_date, num_people) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssisi", $user_id, $user_name, $user_email, $tour_id, $identity_type, $identity_number, $safari_date, $num_people);

        if ($stmt->execute()) {
            $booking_id = $stmt->insert_id; // Get the last inserted booking ID
            header("Location: payment.php?booking_id=$booking_id");
            exit; // Stop further script execution
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <title>Safari Booking</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(to right, #f8f9fa, #e9ecef);
                color: #333;
            }

            h1 {
                text-align: center;
                margin-bottom: 20px;
                color: #343a40;
            }

            .d-flex {
                display: flex;
                min-height: 100vh;
            }

            .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            width: 210px;
            top: 0;
            left: 0;
            position: fixed; 
            transition: all 0.3s;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px;
            display: block;
            font-size: 1.3rem; 
        }
        .sidebar a:hover {
            background-color: #495057;
        }

            .content {
                margin-left: 210px; /* Add margin to the left to make space for the sidebar */
                padding: 20px;
                flex-grow: 1;
            }

            .booking-form {
                max-width: 600px;
                margin: 0 auto;
                background: #ffffff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .booking-form label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }

            .booking-form input, .booking-form select, .booking-form button {
                width: 100%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }

            .booking-form button {
                background-color: #007bff;
                color: #fff;
                font-size: 16px;
                border: none;
                cursor: pointer;
            }

            .booking-form button:hover {
                background-color: #0056b3;
            }

            .error {
                color: red;
                text-align: center;
            }
        </style>
    </head>
    <body>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <a href="user_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="available_tours.php"><i class="fas fa-tree"></i> Available Tours</a>
            <a href="bookings.php"><i class="fas fa-calendar-alt"></i> My Bookings</a>
            <a href="guides.php"><i class="fas fa-user-tie"></i> Guides</a>
            <a href="payment_list.php"><i class="fas fa-money-bill-alt"></i> Payments</a>
            <a href="reviews.php"><i class="fas fa-comments"></i> Reviews</a>
            <a href="profile.php"><i class="fas fa-cogs"></i> Settings</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <h1>Safari Booking</h1>

            <div class="booking-form">
                <?php if (!empty($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <label for="name">Your Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>" readonly>

                    <label for="email">Your Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>

                    <label for="tour_id">Select Safari:</label>
                    <select id="tour_id" name="tour_id" required>
                        <option value="" disabled selected>Select a safari</option>
                        <?php foreach ($safaris as $id => $name): ?>
                            <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="identity_type">Identity Type:</label>
                    <select id="identity_type" name="identity_type" required>
                        <option value="" disabled selected>Select identity type</option>
                        <option value="Aadhar Card">Aadhar Card</option>
                        <option value="PAN Card">PAN Card</option>
                        <option value="Passport">Passport</option>
                        <option value="Driving License">Driving License</option>
                        <option value="Voter ID">Voter ID</option>
                    </select>

                    <label for="identity_number">Identity Number:</label>
                    <input type="text" id="identity_number" name="identity_number" required placeholder="Enter identity number">

                    <label for="safari_date">Safari Date:</label>
                    <input type="date" id="safari_date" name="safari_date" required>

                    <label for="num_people">Number of People:</label>
                    <input type="number" id="num_people" name="num_people" min="1" required placeholder="Enter number of people">

                    <button type="submit">Book Safari</button>
                </form>
            </div>
        </div>
    </div>

    </body>
    </html>
