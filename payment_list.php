<?php
session_start();
include('db_connection.php');

// Validate session variables
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null; // Optional check for admin_id

// Query based on role
if ($role === 'admin') {
    // Admin: View all users' payment details
    $query = "SELECT p.id, u.name AS user_name, s.name AS safari_name, p.total_amount, p.payment_status
              FROM payments p
              JOIN bookings b ON p.booking_id = b.id
              JOIN users u ON b.user_id = u.id
              JOIN safaris s ON b.tour_id = s.id";
    $stmt = $conn->prepare($query);
} elseif ($role === 'user') {
    // User: View only their own payments
    $query = "SELECT p.id, u.name AS user_name, s.name AS safari_name, p.total_amount, p.payment_status
              FROM payments p
              JOIN bookings b ON p.booking_id = b.id
              JOIN users u ON b.user_id = u.id
              JOIN safaris s ON b.tour_id = s.id
              WHERE u.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Payment List</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            height: 100%;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            width: 210px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            left: 0;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px ;
            display: block;
            font-size: 1.3rem;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .content {
            margin-left: 220px;
            padding: 20px;
            flex-grow: 1;
            overflow-y: auto;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .btn {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-warning {
            background-color: #ff9900;
        }

        .btn-warning:hover,
        .btn:hover {
            opacity: 0.8;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                width: 100px;
            }

            .sidebar a {
                font-size: 0.9rem;
            }

            .content {
                margin-left: 100px;
            }

            table th, table td {
                font-size: 0.9rem;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="sidebar">
        <?php if ($_SESSION['user_role'] === 'admin') { ?>
            <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="available_tours.php"><i class="fas fa-tree"></i> Tours</a>
            <a href="admin_booking_list.php"><i class="fas fa-calendar-alt"></i> Bookings</a>
            <a href="guides.php"><i class="fas fa-user-tie"></i> Guides</a>
            <a href="payment_list.php"><i class="fas fa-money-bill-alt"></i> Payments</a>
            <a href="reviews.php"><i class="fas fa-comments"></i> Reviews</a>
            <a href="profile.php"><i class="fas fa-cogs"></i> Settings</a>
        <?php } else { ?>
            <a href="user_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="available_tours.php"><i class="fas fa-tree"></i> Available Tours</a>
            <a href="bookings.php"><i class="fas fa-calendar-alt"></i> My Bookings</a>
            <a href="guides.php"><i class="fas fa-user-tie"></i> Guides</a>
            <a href="payment_list.php"><i class="fas fa-money-bill-alt"></i> Payments</a>
            <a href="reviews.php"><i class="fas fa-comments"></i> Reviews</a>
            <a href="profile.php"><i class="fas fa-cogs"></i> Settings</a>
        <?php } ?>
    </div>

    <div class="content">
        <h1>Payment Records</h1>
        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Safari</th>
                    <th>Total Amount</th>
                    <th>Payment Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['safari_name']) . "</td>";
                    echo "<td>Rs " . number_format($row['total_amount'], 2) . "</td>";
                    echo "<td>" . ucfirst($row['payment_status']) . "</td>";

                    if ($row['payment_status'] !== 'completed') {
                        if ($role === 'admin' || $row['user_name'] === $_SESSION['user_name']) {
                            echo "<td><a href='payment.php?booking_id=" . $row['id'] . "' class='btn btn-warning'>Make Payment</a></td>";
                        } else {
                            echo "<td>-</td>";
                        }
                    } else {
                        echo "<td><a href='bill.php?booking_id=" . $row['id'] . "' class='btn'>View Bill</a></td>";
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
