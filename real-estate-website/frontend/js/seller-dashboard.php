<?php
// frontend/js/seller-dashboard.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - Real Estate</title>
    <link rel="stylesheet" href="../css/seller.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Seller Dashboard</h1>
            <button onclick="logout()" class="btn-logout">Logout</button>
        </header>
        
        <div class="dashboard-content">
            <!-- Add Property Section -->
            <div class="add-property-section">
                <h2>Add New Property</h2>
                <form id="addPropertyForm" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Property Title</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price ($)</label>
                            <input type="number" id="price" name="price" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="property_type">Property Type</label>
                            <select id="property_type" name="property_type">
                                <option value="house">House</option>
                                <option value="apartment">Apartment</option>
                                <option value="land">Land</option>
                                <option value="commercial">Commercial</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bedrooms">Bedrooms</label>
                            <input type="number" id="bedrooms" name="bedrooms" value="0">
                        </div>
                        <div class="form-group">
                            <label for="bathrooms">Bathrooms</label>
                            <input type="number" id="bathrooms" name="bathrooms" value="0">
                        </div>
                        <div class="form-group">
                            <label for="area_size">Area (sq ft)</label>
                            <input type="number" id="area_size" name="area_size" value="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Property Image</label>
                        <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/gif, image/webp, image/bmp">
                        <small>Accepted formats: JPG, PNG, GIF, WEBP, BMP (Max 5MB)</small>
                    </div>
                    
                    <button type="submit" class="btn-submit">Add Property</button>
                </form>
                <div id="addMessage" class="message"></div>
            </div>
            
            <!-- My Properties Section -->
            <div class="my-properties-section">
                <h2>My Properties</h2>
                <div id="myPropertiesList" class="properties-grid"></div>
            </div>
        </div>
    </div>

    <script>
        // Check authentication
        async function checkAuth() {
            try {
                const response = await fetch('http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/auth/check-session.php');
                const data = await response.json();
                
                if (!data.logged_in) {
                    window.location.href = 'login.php';
                }
            } catch (error) {
                console.error('Error checking auth:', error);
                window.location.href = 'login.php';
            }
        }
        
        // Load seller's properties
        async function loadMyProperties() {
            try {
                const response = await fetch('http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/properties/get-properties.php');
                const data = await response.json();
                
                if (data.success) {
                    displayMyProperties(data.properties);
                }
            } catch (error) {
                console.error('Error loading properties:', error);
            }
        }
        
        // Display seller's properties
        function displayMyProperties(properties) {
            const container = document.getElementById('myPropertiesList');
            
            if (properties.length === 0) {
                container.innerHTML = '<p class="no-properties">You haven\'t added any properties yet.</p>';
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
                        <div class="property-status">
                            Status: 
                            <select onchange="updatePropertyStatus(${property.id}, this.value)" class="status-select">
                                <option value="available" ${property.status === 'available' ? 'selected' : ''}>Available</option>
                                <option value="pending" ${property.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="sold" ${property.status === 'sold' ? 'selected' : ''}>Sold</option>
                            </select>
                        </div>
                        <button onclick="deleteProperty(${property.id})" class="btn-delete">Delete Property</button>
                    </div>
                </div>
            `).join('');
        }
        
        // ✅ IMAGE VALIDATION FUNCTION
        function validateImage(file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
            const maxSize = 20 * 1024 * 1024; // 5MB
            
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid image type. Please upload JPG, PNG, GIF, WEBP, or BMP files only.');
                return false;
            }
            
            if (file.size > maxSize) {
                alert('Image too large. Maximum size is 5MB.');
                return false;
            }
            
            return true;
        }

        // ✅ CORRECTED FORM SUBMISSION WITH VALIDATION
        document.getElementById('addPropertyForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const imageFile = document.getElementById('image').files[0];
            
            // Validate image if one is selected
            if (imageFile && !validateImage(imageFile)) {
                return; // Stop here if validation fails
            }
            
            const formData = new FormData();
            formData.append('title', document.getElementById('title').value);
            formData.append('price', document.getElementById('price').value);
            formData.append('location', document.getElementById('location').value);
            formData.append('property_type', document.getElementById('property_type').value);
            formData.append('bedrooms', document.getElementById('bedrooms').value);
            formData.append('bathrooms', document.getElementById('bathrooms').value);
            formData.append('area_size', document.getElementById('area_size').value);
            formData.append('description', document.getElementById('description').value);
            
            if (imageFile) {
                formData.append('image', imageFile);
            }
            
            try {
                const response = await fetch('http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/properties/add-property.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('addMessage').textContent = 'Property added successfully!';
                    document.getElementById('addMessage').className = 'message success';
                    document.getElementById('addPropertyForm').reset();
                    loadMyProperties();
                    
                    setTimeout(() => {
                        document.getElementById('addMessage').textContent = '';
                    }, 3000);
                } else {
                    document.getElementById('addMessage').textContent = data.message;
                    document.getElementById('addMessage').className = 'message error';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('addMessage').textContent = 'Failed to add property: ' + error.message;
                document.getElementById('addMessage').className = 'message error';
            }
        });
        
        // Update property status
        async function updatePropertyStatus(propertyId, status) {
            const formData = new FormData();
            formData.append('id', propertyId);
            formData.append('status', status);
            
            try {
                const response = await fetch('http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/properties/update-property.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Property status updated successfully!', 'success');
                    loadMyProperties(); // Refresh the list
                } else {
                    showNotification('Failed to update status', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error updating status', 'error');
            }
        }
        
        // Delete property
        async function deleteProperty(propertyId) {
            if (confirm('Are you sure you want to delete this property?')) {
                try {
                    const response = await fetch(`http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/properties/delete-property.php?id=${propertyId}`, {
                        method: 'DELETE'
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showNotification('Property deleted successfully!', 'success');
                        loadMyProperties();
                    } else {
                        showNotification('Failed to delete property', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Error deleting property', 'error');
                }
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
                    window.location.href = 'landing.php';
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
        
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Initialize
        checkAuth();
        loadMyProperties();
    </script>
</body>
</html>