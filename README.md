# Zooro-js-lab
The file contains the interactivity elements for Zooro House Hunting App
# Zooro — Nairobi House-Hunting Platform

Zooro is a modern real estate web application designed to help users search, filter, and request verified rental listings across Nairobi estates. Landlords can post available properties, while prospective tenants can request custom house hunts.

## Features
- **Interactive Listings & Filtering:** Filter properties by estate, price, and property type.
- **Dark Mode & Favorites:** Built-in client-side theme toggling and saved listings via local storage.
- **Interactive Property Gallery:** 3D card flips with property detail highlights.
- **Landlord & Tenant Portals:** Form submission for listing a house or requesting a house hunt scout.
- **Backend & Database:** PHP handlers (`process_listing.php`, `process_hunt.php`) and MySQL integration for property management.

## Tech Stack
- **Frontend:** HTML5, CSS3 (Flexbox/Grid), JavaScript (ES6+), Font Awesome 6
- **Backend:** PHP 8.x
- **Database:** MySQL / MariaDB (XAMPP / phpMyAdmin)

## Local Setup Instructions
1. Clone this repository to your local web server root (e.g., `C:/xampp/htdocs/Zooro-Web-App`).
2. Start **Apache** and **MySQL** in XAMPP.
3. Open phpMyAdmin (`http://localhost/phpmyadmin/`).
4. Create a new database named `zooro_db` and import `zooro.sql`.
5. Open `http://localhost/Zooro-Web-App/index.html` in your browser.
