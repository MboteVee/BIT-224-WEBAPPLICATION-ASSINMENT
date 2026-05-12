<?php
// frontend/js/landing.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate - Find Your Dream Property</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h2>🏠 RealEstate</h2>
            </div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#properties">Properties</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <div class="nav-buttons" id="navButtons">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            </div>
            <div class="user-info" id="userInfo" style="display: none;">
                <span id="userName"></span>
                <a href="seller-dashboard.php" class="btn-dashboard">Dashboard</a>
                <button onclick="logout()" class="btn-logout">Logout</button>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section id="home" class="hero">
            <div class="hero-content">
                <h1>Find Your Dream Property</h1>
                <p>Discover the best properties for sale and rent in prime locations</p>
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search by location, property type..." class="search-input">
                    <button onclick="searchProperties()" class="btn-search">Search</button>
                </div>
            </div>
        </section>

        <!-- Properties Section -->
        <section id="properties" class="properties-section">
            <div class="container">
                <h2>Available Properties</h2>
                <div class="filters">
                    <select id="priceFilter" onchange="filterProperties()">
                        <option value="">All Prices</option>
                        <option value="0-100000">Under $100,000</option>
                        <option value="100000-300000">$100,000 - $300,000</option>
                        <option value="300000-500000">$300,000 - $500,000</option>
                        <option value="500000-1000000">$500,000 - $1,000,000</option>
                    </select>
                    <select id="bedroomsFilter" onchange="filterProperties()">
                        <option value="">Any Bedrooms</option>
                        <option value="1">1+ Bedroom</option>
                        <option value="2">2+ Bedrooms</option>
                        <option value="3">3+ Bedrooms</option>
                        <option value="4">4+ Bedrooms</option>
                    </select>
                </div>
                <div id="propertiesList" class="properties-grid"></div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="about-section">
            <div class="container">
                <h2>About Us</h2>
                <p>We are a leading real estate platform connecting sellers with buyers. Our mission is to make property transactions easy, transparent, and secure.</p>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact-section">
            <div class="container">
                <h2>Contact Us</h2>
                <p>Email: info@realestate.com | Phone: +1 (555) 123-4567</p>
            </div>
        </section>
    </main>

    <!-- Modal for Seller Details -->
    <div id="sellerModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Seller Information</h3>
            <div id="sellerDetails"></div>
        </div>
    </div>

    <script>
        // Check login status
        async function checkLoginStatus() {
            try {
                const response = await fetch('http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/auth/check-session.php');
                const data = await response.json();
                
                if (data.logged_in) {
                    document.getElementById('navButtons').style.display = 'none';
                    document.getElementById('userInfo').style.display = 'flex';
                    document.getElementById('userName').textContent = `Welcome, ${data.user_name}`;
                } else {
                    document.getElementById('navButtons').style.display = 'flex';
                    document.getElementById('userInfo').style.display = 'none';
                }
            } catch (error) {
                console.error('Error checking login status:', error);
            }
        }

        // Load properties
        async function loadProperties() {
            try {
                const response = await fetch('http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/properties/get-properties.php?status=available');
                const data = await response.json();
                
                if (data.success) {
                    displayProperties(data.properties);
                }
            } catch (error) {
                console.error('Error loading properties:', error);
            }
        }

        // Display properties
        function displayProperties(properties) {
            const container = document.getElementById('propertiesList');
            
            if (properties.length === 0) {
                container.innerHTML = '<p class="no-properties">No properties available at the moment.</p>';
                return;
            }
            
            container.innerHTML = properties.map(property => `
                <div class="property-card">
                    <div class="property-image">
                        ${property.image_url ? 
                            `<img src="http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/${property.image_url}" alt="${property.title}">` : 
                            '<div class="no-image">No Image</div>'}
                    </div>
                    <div class="property-details">
                        <h3>${escapeHtml(property.title)}</h3>
                        <p class="price">$${formatPrice(property.price)}</p>
                        <p class="location">📍 ${escapeHtml(property.location)}</p>
                        <div class="features">
                            <span>🛏️ ${property.bedrooms} beds</span>
                            <span>🚽 ${property.bathrooms} baths</span>
                            <span>📐 ${property.area_size} sq ft</span>
                        </div>
                        <p class="description">${escapeHtml(property.description.substring(0, 100))}...</p>
                        <button onclick="showSellerDetails(${property.id})" class="btn-contact">Contact Seller</button>
                    </div>
                </div>
            `).join('');
        }

        // Show seller details in modal
        async function showSellerDetails(propertyId) {
            try {
                const response = await fetch('http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/properties/get-properties.php?status=available');
                const data = await response.json();
                const property = data.properties.find(p => p.id === propertyId);
                
                if (property) {
                    const modal = document.getElementById('sellerModal');
                    const sellerDetails = document.getElementById('sellerDetails');
                    
                    sellerDetails.innerHTML = `
                        <div class="seller-info">
                            <p><strong>Name:</strong> ${escapeHtml(property.seller_name)}</p>
                            <p><strong>Email:</strong> ${escapeHtml(property.seller_email)}</p>
                            <p><strong>Phone:</strong> ${escapeHtml(property.seller_phone)}</p>
                            <p><strong>Property:</strong> ${escapeHtml(property.title)}</p>
                            <button onclick="sendInquiry(${property.id})" class="btn-inquiry">Send Inquiry</button>
                        </div>
                    `;
                    
                    modal.style.display = 'block';
                }
            } catch (error) {
                console.error('Error showing seller details:', error);
                alert('Error loading seller details');
            }
        }

        // Send inquiry
        async function sendInquiry(propertyId) {
            // You can implement inquiry form here
            alert('Inquiry feature coming soon! Please contact seller directly via phone or email.');
        }

        // Search properties
        async function searchProperties() {
            const query = document.getElementById('searchInput').value;
            try {
                const response = await fetch(`http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/properties/search-properties.php?query=${encodeURIComponent(query)}`);
                const data = await response.json();
                if (data.success) {
                    displayProperties(data.properties);
                }
            } catch (error) {
                console.error('Error searching properties:', error);
            }
        }

        // Filter properties
        async function filterProperties() {
            const price = document.getElementById('priceFilter').value;
            const bedrooms = document.getElementById('bedroomsFilter').value;
            
            let url = 'http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/properties/search-properties.php?';
            
            if (price) {
                const [min, max] = price.split('-');
                url += `min_price=${min}&max_price=${max}`;
            }
            if (bedrooms) {
                url += `&bedrooms=${bedrooms}`;
            }
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    displayProperties(data.properties);
                }
            } catch (error) {
                console.error('Error filtering properties:', error);
            }
        }

        // Logout
        async function logout() {
            try {
                const response = await fetch('http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/auth/logout.php', {
                    method: 'POST'
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error logging out:', error);
            }
        }

        // Helper functions
        function formatPrice(price) {
            return new Intl.NumberFormat('en-US').format(price);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Modal close functionality
        const modal = document.getElementById('sellerModal');
        const closeBtn = document.getElementsByClassName('close')[0];
        
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Initialize
        checkLoginStatus();
        loadProperties();
    </script>
</body>
</html>