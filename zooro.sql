-- Zooro Real Estate Database Schema
CREATE DATABASE IF NOT EXISTS zooro_db;
USE zooro_db;

-- 1. Table for Property Listings (Landlord submissions)
CREATE TABLE IF NOT EXISTS property_listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    landlord_name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    location VARCHAR(255) NOT NULL,
    house_type VARCHAR(50) NOT NULL,
    rent DECIMAL(10, 2) NOT NULL,
    available_from DATE NOT NULL,
    description TEXT,
    photo_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Table for House Hunt Requests (Tenant requests)
CREATE TABLE IF NOT EXISTS house_hunts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hunt_name VARCHAR(255) NOT NULL,
    hunt_phone VARCHAR(50) NOT NULL,
    hunt_location VARCHAR(255) NOT NULL,
    hunt_house_type VARCHAR(50) NOT NULL,
    hunt_budget VARCHAR(50) NOT NULL,
    hunt_timeframe VARCHAR(50) NOT NULL,
    hunt_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Initial Data for Demo / Testing
INSERT INTO property_listings (landlord_name, phone, location, house_type, rent, available_from, description) VALUES
('John Kamau', '+254712345678', 'Westlands', '2 Bedroom', 25000.00, '2026-09-01', 'Spacious apartment near Sarit Centre with 24/7 water and security.'),
('Mary Wanjiku', '+254722987654', 'Kilimani', 'Studio', 12000.00, '2026-08-15', 'Modern studio apartment near Yaya Centre. WiFi ready.'),
('David Ochieng', '+254733112233', 'Kahawa West', '3 Bedroom', 45000.00, '2026-08-20', 'Family home with garden, borehole, and DSQ.');