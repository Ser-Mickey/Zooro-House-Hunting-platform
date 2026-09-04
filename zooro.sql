-- Zooro Real Estate Database Schema
CREATE DATABASE IF NOT EXISTS zooro;
USE zooro;

-- 1. Landlords Table
CREATE TABLE IF NOT EXISTS landlords (
    landlord_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    national_id VARCHAR(50) UNIQUE NOT NULL,
    jurisdiction_location VARCHAR(100) NOT NULL,
    is_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Property Listings Table
CREATE TABLE IF NOT EXISTS property_listings (
    property_id INT AUTO_INCREMENT PRIMARY KEY,
    landlord_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    location VARCHAR(100) NOT NULL,
    house_type VARCHAR(50) NOT NULL,
    rent DECIMAL(10,2) NOT NULL,
    status ENUM('available', 'booked', 'occupied') DEFAULT 'available',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (landlord_id) REFERENCES landlords(landlord_id) ON DELETE CASCADE
);

-- 3. House Hunt Requests Table
CREATE TABLE IF NOT EXISTS house_hunt_requests (
    hunt_id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    client_phone VARCHAR(20) NOT NULL,
    hunt_location VARCHAR(100) NOT NULL,
    house_type VARCHAR(50) NOT NULL,
    max_budget DECIMAL(10,2) NOT NULL,
    timeframe VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    user_type VARCHAR(20),
    subject VARCHAR(150),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Initial Data for Demo / Testing
INSERT INTO property_listings (landlord_name, phone, location, house_type, rent, available_from, description) VALUES
('John Kamau', '+254712345678', 'Westlands', '2 Bedroom', 25000.00, '2026-09-01', 'Spacious apartment near Sarit Centre with 24/7 water and security.'),
('Mary Wanjiku', '+254722987654', 'Kilimani', 'Studio', 12000.00, '2026-08-15', 'Modern studio apartment near Yaya Centre. WiFi ready.'),
('David Ochieng', '+254733112233', 'Kahawa West', '3 Bedroom', 45000.00, '2026-08-20', 'Family home with garden, borehole, and DSQ.');
