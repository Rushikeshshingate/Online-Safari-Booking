<?php
include('db_connection.php');  // Your database connection file
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Safari Reviews</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            margin: 0;
            padding: 0;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            width: 210px;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
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
        .container {
            margin-left: 230px;
            padding: 30px;
        }
        h2, h1 {
            text-align: center;
            color: #333;
        }
        form {
            background: white;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        label {
            font-weight: bold;
        }
        select, input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <?php if ($_SESSION['user_role'] === 'admin') { ?>
        <div class="sidebar">
            <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="available_tours.php"><i class="fas fa-tree"></i> Tours</a>
            <a href="admin_booking_list.php"><i class="fas fa-calendar-alt"></i> Bookings</a>
            <a href="guides.php"><i class="fas fa-user-tie"></i> Guides</a>
            <a href="payment_list.php"><i class="fas fa-money-bill-alt"></i> Payments</a>
            <a href="reviews.php"><i class="fas fa-comments"></i> Reviews</a>
            <a href="profile.php"><i class="fas fa-cogs"></i> Settings</a>
        </div>
    <?php } else { ?>
        <div class="sidebar">
            <a href="user_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="available_tours.php"><i class="fas fa-tree"></i> Available Tours</a>
            <a href="bookings.php"><i class="fas fa-calendar-alt"></i> My Bookings</a>
            <a href="guides.php"><i class="fas fa-user-tie"></i> Guides</a>
            <a href="payment_list.php"><i class="fas fa-money-bill-alt"></i> Payments</a>
            <a href="reviews.php"><i class="fas fa-comments"></i> Reviews</a>
            <a href="profile.php"><i class="fas fa-cogs"></i> Settings</a>
        </div>
    <?php } ?>

    <div class="container">
        <h1>Safari Reviews</h1>

        <?php
        if (isset($_SESSION['user_role'])) {
            $user_role = $_SESSION['user_role'];

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_role != 'admin') {
                $safari_id = $_POST['safari_id'];
                $user_id = $_SESSION['user_id'];
                $review = $_POST['review'];
                $rating = $_POST['rating'];

                $sql = "INSERT INTO safari_reviews (safari_id, user_id, review, rating) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iisi", $safari_id, $user_id, $review, $rating);

                if ($stmt->execute()) {
                    echo "<p style='color: green;'>Review submitted successfully!</p>";
                } else {
                    echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
                }
            }

            if ($user_role != 'admin') {
        ?>
                <form method="POST">
                    <label for="safari_id">Select Safari:</label>
                    <select name="safari_id" required>
                        <option value="">-- Select Safari --</option>
                        <?php
                        $result = $conn->query("SELECT id, name FROM safaris");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        }
                        ?>
                    </select>

                    <label for="rating">Rating (1-5):</label>
                    <input type="number" name="rating" min="1" max="5" required>

                    <label for="review">Your Review:</label>
                    <textarea name="review" rows="5" required></textarea>

                    <button type="submit">Submit Review</button>
                </form>
        <?php
            }

            if ($user_role == 'admin') {
                $sql = "SELECT sr.id, u.name AS user_name, s.name, sr.review, sr.rating, sr.review_date 
                        FROM safari_reviews sr
                        JOIN users u ON sr.user_id = u.id
                        JOIN safaris s ON sr.safari_id = s.id
                        ORDER BY sr.review_date DESC";

                $result = $conn->query($sql);

                echo "<table>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Safari</th>
                            <th>Review</th>
                            <th>Rating</th>
                            <th>Date</th>
                        </tr>";

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['user_name']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['review']}</td>
                                <td>{$row['rating']}</td>
                                <td>{$row['review_date']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No reviews found.</td></tr>";
                }

                echo "</table>";
            }
        } else {
            echo "<p>Please log in to view or submit reviews.</p>";
        }
        ?>
    </div>
</body>
</html>
