<?php
require_once 'db_connect.php';
require_once 'landlord_auth.php'; // Enforces login check before rendering page!

// Extract logged-in landlord's session details
$landlordId   = $_SESSION['landlord_id'] ?? '';
$landlordName = $_SESSION['landlord_name'] ?? '';
$jurisdiction = $_SESSION['jurisdiction_location'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Post a House &amp; House Hunt Requests — Zooro Kenya</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <nav>
    <div class="nav-container">
      <a href="index.php" class="nav-logo">
        <svg class="logo-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="zooroGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#2563EB" /> <!-- Modern Indigo Blue -->
              <stop offset="100%" stop-color="#10B981" /> <!-- Fresh Emerald Green -->
            </linearGradient>
          </defs>
          <!-- Roof + Z-letter combined mark -->
          <path d="M 15 42 L 50 15 L 85 42 L 72 42 L 35 72 L 85 72 L 85 85 L 15 85 L 15 72 L 58 42 Z" fill="url(#zooroGradient)" />
          <!-- Accent Door/Location Pin element -->
          <circle cx="50" cy="55" r="7" fill="#2563EB" />
        </svg>
        <span class="brand-name">ooro<span class="brand-dot">.</span></span>
      </a>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="listings.html">Listings</a></li>
        <li><a href="gallery.html">Gallery</a></li>
        <li><a href="post.php" class="nav-cta active">Post a House</a></li>
        <li><a href="landlord_dashboard.php">Dashboard</a></li>
        <li><a href="landlord_logout.php" style="color: #ef4444; font-weight: bold;">Logout</a></li>
      </ul>
    </div>
  </nav>

  <div class="page-header">
    <span class="page-eyebrow">List &amp; Request</span>
    <h1>List a Property or Start a House Hunt</h1>
    <p>Logged in as: <strong><?php echo htmlspecialchars($landlordName); ?></strong> (Jurisdiction: <?php echo htmlspecialchars($jurisdiction); ?>)</p>
  </div>

  <div class="form-section">

    <!-- TOGGLE TABS -->
    <div class="form-toggle" role="tablist">
      <button type="button" id="tab-landlord" class="toggle-tab active" role="tab" aria-selected="true">
        🏠 List a Property
      </button>
      <button type="button" id="tab-hunt" class="toggle-tab" role="tab" aria-selected="false">
        🔍 Request a House Hunt
      </button>
    </div>

    <!-- PANEL 1: LANDLORD — LIST A PROPERTY -->
    <div id="panel-landlord" class="form-panel">
      <div class="form-card">
        <div class="form-header">
          <h2>Property Details</h2>
          <p>Fill in the details below and we'll publish your listing directly under your account.</p>
        </div>

        <!-- Action connects to process_listings.php with multipart encoding for image upload -->
        <form action="process_listings.php" method="POST" enctype="multipart/form-data">

          <div class="form-row">
            <div class="form-group">
              <label for="landlord-name">Landlord Name</label>
              <input type="text" id="landlord-name" name="landlord_name"
                     value="<?php echo htmlspecialchars($landlordName); ?>" readonly required />
            </div>
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone"
                     placeholder="e.g. +254 701 111 110" required />
            </div>
          </div>

          <div class="form-group">
            <label for="location">Location / Estate</label>
            <input type="text" id="location" name="location"
                   value="<?php echo htmlspecialchars($jurisdiction); ?>" required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="house-type">House Type</label>
              <select id="house-type" name="house_type" required>
                <option value="" disabled selected>Select type...</option>
                <option value="Bedsitter">Bedsitter</option>
                <option value="Studio">Studio</option>
                <option value="1 Bedroom">1 Bedroom</option>
                <option value="2 Bedroom">2 Bedroom</option>
                <option value="3 Bedroom">3 Bedroom</option>
                <option value="4 Bedroom+">4 Bedroom+</option>
                <option value="Maisonette">Maisonette</option>
              </select>
            </div>
            <div class="form-group">
              <label for="rent">Monthly Rent (KSh)</label>
              <input type="number" id="rent" name="rent"
                     placeholder="e.g. 25000" min="1000" required />
            </div>
          </div>

          <div class="form-group">
            <label for="available-from">Available From</label>
            <input type="date" id="available-from" name="available_from" value="<?php echo date('Y-m-d'); ?>" required />
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"
                      placeholder="Describe your property — amenities, nearby places, transport links..."></textarea>
          </div>

          <div class="form-group">
            <label>Upload Photo</label>
            <label class="file-upload-area" for="photo-upload">
              <input type="file" id="photo-upload" name="photos"
                     accept="image/*" />
              <div class="upload-icon">📷</div>
              <p>Choose file or drag and drop here</p>
              <span>PNG, JPG up to 5MB</span>
            </label>
          </div>

          <button type="submit" class="btn-submit">Submit Listing →</button>

        </form>
      </div>
    </div>

    <!-- PANEL 2: TENANT — REQUEST A HOUSE HUNT -->
    <div id="panel-hunt" class="form-panel hidden">
      <div class="form-card">
        <div class="form-header">
          <span class="hunt-badge">Relax — we'll find it for you</span>
          <h2>Tell Us What You're Looking For</h2>
          <p>Can't find the right house on Listings? Submit a request and a Zooro scout will search for it and get back to you within 24 hours.</p>
        </div>

        <!-- Action connects to process_hunt.php -->
        <form action="process_hunt.php" method="POST">

          <div class="form-row">
            <div class="form-group">
              <label for="hunt-name">Your Name</label>
              <input type="text" id="hunt-name" name="hunt_name"
                     placeholder="Enter your full name..." required />
            </div>
            <div class="form-group">
              <label for="hunt-phone">Phone Number</label>
              <input type="tel" id="hunt-phone" name="hunt_phone"
                     placeholder="e.g. +254 701 111 110" required />
            </div>
          </div>

          <div class="form-group">
            <label for="hunt-location">Preferred Location(s)</label>
            <input type="text" id="hunt-location" name="hunt_location"
                   placeholder="e.g. Kilimani, Lavington, or Westlands..." required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="hunt-house-type">House Type Needed</label>
              <select id="hunt-house-type" name="hunt_house_type" required>
                <option value="" disabled selected>Select type...</option>
                <option value="Bedsitter">Bedsitter</option>
                <option value="Studio">Studio</option>
                <option value="1 Bedroom">1 Bedroom</option>
                <option value="2 Bedroom">2 Bedroom</option>
                <option value="3 Bedroom">3 Bedroom</option>
                <option value="4 Bedroom+">4 Bedroom+</option>
                <option value="Maisonette">Maisonette</option>
              </select>
            </div>
            <div class="form-group">
              <label for="hunt-budget">Budget Range</label>
              <select id="hunt-budget" name="hunt_budget" required>
                <option value="" disabled selected>Select budget...</option>
                <option value="Under KSh 10,000">Under KSh 10,000</option>
                <option value="KSh 10,000 – 20,000">KSh 10,000 – 20,000</option>
                <option value="KSh 20,000 – 40,000">KSh 20,000 – 40,000</option>
                <option value="KSh 40,000+">KSh 40,000+</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="hunt-timeframe">Move-in Timeframe</label>
            <select id="hunt-timeframe" name="hunt_timeframe" required>
              <option value="" disabled selected>Select timeframe...</option>
              <option value="As soon as possible">As soon as possible</option>
              <option value="Within 2 weeks">Within 2 weeks</option>
              <option value="Within a month">Within a month</option>
              <option value="Flexible">Flexible</option>
            </select>
          </div>

          <div class="form-group">
            <label for="hunt-notes">Must-Have Features <span class="optional-tag">(optional)</span></label>
            <textarea id="hunt-notes" name="hunt_notes"
                      placeholder="e.g. parking, backup water, near a bus stage, pet-friendly..."></textarea>
          </div>

          <button type="submit" class="btn-submit">Submit Request →</button>

        </form>
      </div>
    </div>

  </div>

  <footer>
    <div class="footer-inner">
      <div class="footer-brand">
        <div class="nav-logo">🏠 Zooro<span style="color:var(--gold)">Kenya</span></div>
        <p>Your trusted platform for finding verified rental homes across Nairobi's finest estates.</p>
      </div>
      <div class="footer-col">
        <h5>Navigate</h5>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="listings.html">Listings</a></li>
          <li><a href="gallery.html">Gallery</a></li>
          <li><a href="post.php">Post a House</a></li>
          <li><a href="contact.html">Contact Us</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Contact</h5>
        <ul>
          <li><a href="mailto:hello@zooro.co.ke">hello@zooro.co.ke</a></li>
          <li><a href="tel:+254700111000">+254 700 111 000</a></li>
          <li><span>Nairobi, Kenya</span></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2026 Zooro Kenya. All rights reserved.</span>
    </div>
  </footer>

  <script src="script.js" defer></script>
</body>
</html>