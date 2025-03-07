<?php
session_start();
include('db_connection.php');

// Validate admin session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch bookings with user details, safari name from safaris table, and payment status
$query = "
    SELECT 
        b.id AS booking_id, 
        u.name AS user_name, 
        u.email AS user_email, 
        s.name AS safari_name,  -- Fetching safari name from safaris table
        b.num_people, 
        p.total_amount, 
        b.safari_date, 
        p.payment_status, 
        b.created_at, 
        s.duration_days,  -- Getting duration from the safaris table
        u.phone AS user_phone, 
        b.identity_type 
    FROM bookings b
    INNER JOIN users u ON b.user_id = u.id
    LEFT JOIN payments p ON b.id = p.booking_id
    LEFT JOIN safaris s ON b.tour_id = s.id  -- Joining the safaris table to get safari name and duration_days
    ORDER BY b.safari_date DESC
";

$result = $conn->query($query);

// Check for errors in the query execution
if (!$result) {
    die("Error in query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Booking List</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            width: 220px;
            position: fixed;
            transition: all 0.3s;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            font-size: 1.3rem; 
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .container {
            margin-left: 240px; /* Add space for sidebar */
            padding-top: 40px;
        }
        .table-container {
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
            border-radius: 8px;
            background-color: #ffffff;
        }
        .table {
            padding-top: 30px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
        }
        thead {
            background-color: #343a40;
            color: white;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    
        .btn-view {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btn-view:hover {
            background-color: #218838;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="available_tours.php"><i class="fas fa-tree"></i> Tours</a>
        <a href="admin_booking_list.php"><i class="fas fa-calendar-alt"></i> Bookings</a>
        <a href="guides.php"><i class="fas fa-user-tie"></i> Guides</a>
        <a href="payment_list.php"><i class="fas fa-money-bill-alt"></i> Payments</a>
        <a href="reviews.php"><i class="fas fa-comments"></i> Reviews</a>
        <a href="profile.php"><i class="fas fa-cogs"></i> Settings</a>
    </div>

    <div class="container">
        <h1 class="text-center">List of Bookings</h1>
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">User Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Safari Name</th>  <!-- Changed from 'Tour ID' to 'Safari Name' -->
                    <th scope="col">No. of People</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Safari Date</th>
                    <th scope="col">Payment Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <th scope="row"><?= htmlspecialchars($row['booking_id']) ?></th>
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['user_email']) ?></td>
                            <td><?= htmlspecialchars($row['safari_name']) ?></td>  <!-- Displaying Safari Name -->
                            <td><?= htmlspecialchars($row['num_people']) ?></td>
                            <td><?= htmlspecialchars($row['total_amount']) ?></td>
                            <td><?= htmlspecialchars($row['safari_date']) ?></td>
                            <td><?= htmlspecialchars($row['payment_status']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-view" data-bs-toggle="modal" data-bs-target="#viewModal-<?= $row['booking_id'] ?>">View</button>
                            </td>
                        </tr>

                        <!-- Modal for viewing details -->
                        <div class="modal fade" id="viewModal-<?= $row['booking_id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewModalLabel">Booking Details for #<?= $row['booking_id'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>User Name:</strong> <?= htmlspecialchars($row['user_name']) ?></p>
                                        <p><strong>Email:</strong> <?= htmlspecialchars($row['user_email']) ?></p>
                                        <p><strong>Safari Name:</strong> <?= htmlspecialchars($row['safari_name']) ?></p>
                                        <p><strong>Number of People:</strong> <?= htmlspecialchars($row['num_people']) ?></p>
                                        <p><strong>Total Amount:</strong> <?= htmlspecialchars($row['total_amount']) ?></p>
                                        <p><strong>Safari Date:</strong> <?= htmlspecialchars($row['safari_date']) ?></p>
                                        <p><strong>Booking Date:</strong> <?= htmlspecialchars($row['created_at']) ?></p>
                                        <p><strong>Duration (Days):</strong> <?= htmlspecialchars($row['duration_days']) ?> days</p>
                                        <p><strong>User Mobile:</strong> <?= htmlspecialchars($row['user_phone']) ?></p>
                                        <p><strong>Identity Type:</strong> <?= htmlspecialchars($row['identity_type']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
