CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE safaris (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    duration_days INT NOT NULL
);
ALTER TABLE safaris
ADD COLUMN cost_per_person DECIMAL(10, 2);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,         
    user_id INT NOT NULL,                       
    name VARCHAR(100) NOT NULL,                  
    email VARCHAR(100) NOT NULL,                
    tour_id INT NOT NULL,                       
    identity_type VARCHAR(50) NOT NULL,         
    identity_number VARCHAR(50) NOT NULL,        
    safari_date DATE NOT NULL,                   
    num_people INT NOT NULL,                     
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),  
    FOREIGN KEY (tour_id) REFERENCES safaris(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    card_type VARCHAR(50) NOT NULL,
    card_number VARCHAR(20) NOT NULL,
    cvv VARCHAR(4) NOT NULL,
    expiry_date DATE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    safari_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (safari_id) REFERENCES safari(id)
);
