<?php
session_start();
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_role'])) {
    header("Location: index.php");
    exit();
}
// Array to store all tour details
$tours = [
    1 => [
        'name' => 'Jungle Safari',
        'description' => 'Experience the thrill of exploring dense forests and witnessing wildlife in their natural habitat.',
        'highlights' => [
            'Explore dense forests',
            'Spot tigers, elephants, and other wildlife',
            'Visit scenic waterfalls',
        ],
        'duration' => '3 days',
        'price' => '₹6000',
        'image' => 'jungle.gif',
    ],
    2 => [
        'name' => 'River Safari',
        'description' => 'Enjoy a serene boat ride through the river while spotting aquatic wildlife and scenic views.',
        'highlights' => [
            'Relaxing boat ride',
            'Spot crocodiles and exotic fish',
            'Enjoy serene views of nature',
        ],
        'duration' => '2 day',
        'price' => '₹4000',
        'image' => 'river safari.webp',
    ],
    3 => [
        'name' => 'Bird Watching Safari',
        'description' => 'A paradise for bird lovers. Spot rare and exotic birds in their natural habitat.',
        'highlights' => [
            'Spot exotic and rare birds',
            'Perfect for photographers',
            'Learn from expert guides',
        ],
        'duration' => '1 days',
        'price' => '₹2000',
        'image' => 'chiu.jpg',
    ],
];

// Get the tour ID from the query parameter if available
$tour_id = $_GET['id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Safari Tours</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            color: #333;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            color: #343a40;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            width: 210px;
            top: 0;
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
        .tour-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .tour-card {
            width: 300px;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .tour-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .tour-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .tour-card .tour-info {
            padding: 15px;
        }

        .tour-card .tour-info h2 {
            margin-bottom: 10px;
            font-size: 20px;
            color: #007bff;
        }

        .tour-card .tour-info p {
            margin-bottom: 10px;
            color: #555;
            font-size: 14px;
        }

        .tour-card .tour-info .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .tour-card .tour-info .btn:hover {
            background-color: #218838;
        }

        .details-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }

        .details-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .details-container ul {
            list-style: disc;
            padding-left: 20px;
        }

        .details-container ul li {
            margin-bottom: 10px;
        }

        .details-container .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .details-container .btn:hover {
            background-color: #0056b3;
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
    <?php if (!$tour_id): ?>
        <!-- Tour Listing -->
        <h1>Available Safari Tours</h1>
        <div class="tour-container">
            <?php foreach ($tours as $id => $tour): ?>
                <div class="tour-card">
                    <img src="<?php echo $tour['image']; ?>" alt="<?php echo $tour['name']; ?>">
                    <div class="tour-info">
                        <h2><?php echo $tour['name']; ?></h2>
                        <p><?php echo $tour['description']; ?></p>
                        <p><strong>Price:</strong> <?php echo $tour['price']; ?></p>
                        <p><strong>Duration:</strong> <?php echo $tour['duration']; ?></p>
                        <a href="?id=<?php echo $id; ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Safari Details -->
        <?php if (isset($tours[$tour_id])): 
            $tour = $tours[$tour_id]; ?>
            <div class="details-container">
                <h1><?php echo $tour['name']; ?></h1>
                <p><strong>Price:</strong> <?php echo $tour['price']; ?></p>
                <p><strong>Duration:</strong> <?php echo $tour['duration']; ?></p>
                <p><strong>Description:</strong> <?php echo $tour['description']; ?></p>
                <p><strong>Highlights:</strong></p>
                <ul>
                    <?php foreach ($tour['highlights'] as $highlight): ?>
                        <li><?php echo $highlight; ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="?" class="btn">Back to Tours</a>
            </div>
        <?php else: ?>
            <p>Tour not found. <a href="?">Back to Tours</a></p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
