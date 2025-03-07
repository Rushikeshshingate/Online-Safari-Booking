<?php
session_start();
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_role'])) {
    header("Location: index.php");
    exit();
}

// Variables to store form data and messages
$guide_name = $safari_name = $guide_contact_info = $safari_duration = '';
$error_message = $success_message = '';

// Handle form submission for adding guides (only for admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_role'] === 'admin') {
    // Sanitize and get form data
    $guide_name = $_POST['guide_name'];
    $safari_name = $_POST['safari_name'];
    $guide_contact_info = $_POST['guide_contact_info'];
    $safari_duration = $_POST['safari_duration'];

    // Validate form inputs
    if (empty($guide_name) || empty($safari_name) || empty($guide_contact_info) || empty($safari_duration)) {
        $error_message = "All fields are required.";
    } else {
        // Insert the new guide into the database
        $stmt = $conn->prepare("INSERT INTO guides (guide_name, safari_name, guide_contact_info, safari_duration) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $guide_name, $safari_name, $guide_contact_info, $safari_duration);

        if ($stmt->execute()) {
            $success_message = "Guide added successfully!";
            $guide_name = $safari_name = $guide_contact_info = $safari_duration = ''; // Clear the form
        } else {
            $error_message = "Error adding the guide. Please try again.";
        }
    }
}

// Fetch all guides from the database
$sql = "SELECT * FROM guides";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Safari Guides</title>
    <style>
        /* General styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            width: 210px;
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
        .content {
            margin-left: 220px;
            padding: 20px;
        }
        h2, h3 {
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-primary {
            width: 100%;
        }
        .table {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color:rgb(242, 242, 242);
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
</div>

<div class="content">
    <div class="container">
        <h2>Safari Guides</h2>

        <!-- Display success or error message -->
        <?php if ($error_message) { echo "<div class='error'>$error_message</div>"; } ?>
        <?php if ($success_message) { echo "<div class='success'>$success_message</div>"; } ?>

        <!-- Form for adding guides (only for admin) -->
        <?php if ($_SESSION['user_role'] === 'admin') { ?>
        <form method="POST">
            <div class="form-group">
                <label for="guide_name">Guide Name</label>
                <input type="text" name="guide_name" id="guide_name" value="<?= htmlspecialchars($guide_name) ?>" required>
            </div>
            <div class="form-group">
                <label for="safari_name">Safari Name</label>
                <select name="safari_name" id="safari_name" required>
                    <option value="">Select Safari</option>
                    <option value="Jungle Safari" <?= ($safari_name === 'Jungle Safari') ? 'selected' : '' ?>>Jungle Safari</option>
                    <option value="River Safari" <?= ($safari_name === 'River Safari') ? 'selected' : '' ?>>River Safari</option>
                    <option value="Bird Watching Safari" <?= ($safari_name === 'Bird Watching Safari') ? 'selected' : '' ?>>Bird Watching Safari</option>
                </select>
            </div>
            <div class="form-group">
                <label for="guide_contact_info">Guide Contact</label>
                <input type="text" name="guide_contact_info" id="guide_contact_info" value="<?= htmlspecialchars($guide_contact_info) ?>" required>
            </div>
            <div class="form-group">
                <label for="safari_duration">Safari Duration (in days)</label>
                <input type="number" name="safari_duration" id="safari_duration" value="<?= htmlspecialchars($safari_duration) ?>" required>
            </div>
            <button type="submit" class="btn">Add Guide</button>
        </form>
        <?php } ?>

        <!-- Guide List -->
       <br><h3>List of Guides</h3>
        <table>
            <thead>
                <tr>
                    <th>Guide Name</th>
                    <th>Safari Name</th>
                    <th>Contact</th>
                    <th>Safari Duration (Days)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['guide_name']}</td>
                            <td>{$row['safari_name']}</td>
                            <td>{$row['guide_contact_info']}</td>
                            <td>{$row['safari_duration']}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No guides added yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
