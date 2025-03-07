<?php
// Include database connection here (if needed)
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Safari Booking</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            scroll-behavior: smooth;
        }

        /* Header */
        header {
            position: relative;
            height: 100vh;
            background-color: #000;
        }

        nav {
            display: flex;
            justify-content: flex-end;
            padding: 15px 50px;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 10;
            position: relative;
        }

        nav .nav-links {
            list-style: none;
            display: flex;
            align-items: center;
        }

        nav .nav-links li {
            margin-left: 30px;
            position: relative;
        }

        nav .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        nav .nav-links a:hover {
            background-color: #ff7f00;
            border-radius: 5px;
        }

        /* Login Dropdown */
        .dropdown {
            position: relative;
        }

        .dropdown-btn {
            background-color: #ff7f00; /* Orange button */
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 5px;
            display: inline-block;
            cursor: pointer;
        }

        .dropdown-content {
            display: none; /* Initially hidden */
            position: absolute;
            right: 0;
            background-color: rgba(0, 0, 0, 0.8); /* Match nav color */
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            min-width: 150px;
            border-radius: 5px;
            z-index: 20;
        }

        .dropdown-content a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .dropdown-content a:hover {
            background-color: #ff7f00;
        }

        /* CSS to Show Dropdown on Click */
        .dropdown:focus-within .dropdown-content {
            display: block;
        }

        .hero-section {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .hero-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.6;
            z-index: 1;
            position: relative;
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 10;
        }

        .hero-content h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .hero-content p {
            font-size: 1.5em;
        }

        .btn-book-now {
            background-color: #ff7f00;
            padding: 10px 30px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1.2em;
        }

        /* Safari Packages Section */
        .safari-packages {
            padding: 50px 20px;
            text-align: center;
            background-color: #fff;
        }

        .safari-packages h2 {
            font-size: 2.5em;
            margin-bottom: 40px;
        }

        .package-card-container {
            display: flex;
            justify-content: space-around;
            gap: 20px;
        }

        .package-card {
            background-color: #fff;
            width: 30%;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .package-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .package-card h3 {
            font-size: 1.8em;
            margin-top: 15px;
        }

        .package-card p {
            font-size: 1.2em;
            margin: 10px 0;
        }

        .contact {
            padding: 50px 20px;
            background: #f9f9f9;
            text-align: center;
        }

        .contact h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .contact p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .contact-info {
            font-size: 18px;
            line-height: 1.6;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }

        footer .social-media-links a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!-- Header Section with Navigation -->
    <header>
        <nav>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="user_login.php">Book Safari</a></li>
                <li><a href="#safari-packages">About Us</a></li>
                <li><a href="#contact">Contact Us</a></li> 

                <!-- Dropdown Login Button -->
                <li class="dropdown" tabindex="0">
                    <a href="#" class="dropdown-btn">Login</a>
                    <div class="dropdown-content">
                        <a href="admin_login.php">Admin Login</a>
                        <a href="user_login.php">User Login</a>
                    </div>
                </li>
            </ul>
        </nav>

        <div class="hero-section">
            <img src="home.jpg" alt="Safari Image">
            <div class="hero-content">
                <h1>Explore the Wilderness</h1>
                <p>Book your dream safari adventure today!</p>
                <a href="user_login.php" class="btn-book-now">Book Now</a>
            </div>
        </div>
    </header>

    <!-- Safari Packages Section -->
    <section id="safari-packages" class="safari-packages">
        <h2>Popular Safari Packages</h2>
        <div class="package-card-container">
            <div class="package-card">
                <img src="jungle.gif" alt="Safari Package 1">
                <h3>Jungle Safari</h3>
                <p>Explore the deep jungles and spot exotic wildlife.</p>
                <a href="user_login.php" class="btn-book-now">Book Now</a>
            </div>
            <div class="package-card">
                <img src="river safari.webp" alt="Safari Package 2">
                <h3>River Safari</h3>
                <p>Enjoy a peaceful safari along the river, watching nature unfold.</p>
                <a href="user_login.php" class="btn-book-now">Book Now</a>
            </div>
            <div class="package-card">
                <img src="chiu.jpg" alt="Safari Package 3">
                <h3>Bird Watching Safari</h3>
                <p>Observe the most stunning bird species in their natural habitat.</p>
                <a href="user_login.php" class="btn-book-now">Book Now</a>
            </div>
        </div>
    </section>

    <!-- Contact Us Section -->
    <section id="contact" class="contact">
        <h2>Contact Us</h2>
        <p>If you have any questions or need assistance, feel free to reach out to us.</p>
        <div class="contact-info">
            <p>Email: contact@safaribooking.com</p>
            <p>Phone: +123 456 7890</p>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2025 Online Safari Booking | All Rights Reserved</p>
    </footer>

</body>

</html>
