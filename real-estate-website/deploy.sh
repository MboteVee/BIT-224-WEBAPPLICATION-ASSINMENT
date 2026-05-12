#!/bin/bash
# deploy.sh - Complete deployment script

echo "========================================="
echo "Real Estate Website Deployment"
echo "========================================="

# Variables
PROJECT_DIR="/var/www/html/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website"
BACKUP_DIR="/tmp/real-estate-backup-$(date +%Y%m%d_%H%M%S)"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print status
print_status() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    print_error "Please run as root (use sudo)"
    exit 1
fi

# Step 1: Backup existing installation
if [ -d "$PROJECT_DIR" ]; then
    print_status "Creating backup of existing installation..."
    mkdir -p "$BACKUP_DIR"
    cp -r "$PROJECT_DIR" "$BACKUP_DIR/"
    print_status "Backup created at: $BACKUP_DIR"
fi

# Step 2: Set correct permissions
print_status "Setting file permissions..."
chown -R www-data:www-data "$PROJECT_DIR"
chmod -R 755 "$PROJECT_DIR"
chmod -R 777 "$PROJECT_DIR/uploads"
chmod 644 "$PROJECT_DIR/.htaccess"
print_status "Permissions set correctly"

# Step 3: Check PHP extensions
print_status "Checking PHP extensions..."
php -m | grep -q pdo_mysql
if [ $? -eq 0 ]; then
    print_status "PDO MySQL extension installed"
else
    print_warning "Installing PDO MySQL..."
    apt-get install -y php-pdo-mysql
fi

php -m | grep -q gd
if [ $? -eq 0 ]; then
    print_status "GD extension installed"
else
    print_warning "Installing GD extension..."
    apt-get install -y php-gd
fi

# Step 4: Configure PHP
print_status "Configuring PHP..."
PHP_INI=$(php -i | grep "Loaded Configuration File" | awk '{print $5}')
if [ -f "$PHP_INI" ]; then
    sed -i 's/upload_max_filesize = .*/upload_max_filesize = 10M/' "$PHP_INI"
    sed -i 's/post_max_size = .*/post_max_size = 10M/' "$PHP_INI"
    sed -i 's/max_execution_time = .*/max_execution_time = 300/' "$PHP_INI"
    sed -i 's/memory_limit = .*/memory_limit = 256M/' "$PHP_INI"
    print_status "PHP configuration updated"
fi

# Step 5: Setup database
print_status "Setting up database..."
mysql -u root -p <<EOF
CREATE DATABASE IF NOT EXISTS real_estate_db;
USE real_estate_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    location VARCHAR(200) NOT NULL,
    bedrooms INT DEFAULT 0,
    bathrooms INT DEFAULT 0,
    area_size INT DEFAULT 0,
    property_type VARCHAR(50) DEFAULT 'house',
    image_url VARCHAR(500),
    status VARCHAR(20) DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    buyer_name VARCHAR(100) NOT NULL,
    buyer_email VARCHAR(100) NOT NULL,
    buyer_phone VARCHAR(20),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

SHOW TABLES;
EOF

if [ $? -eq 0 ]; then
    print_status "Database setup complete"
else
    print_error "Database setup failed"
    exit 1
fi

# Step 6: Create uploads directory
print_status "Creating uploads directory..."
mkdir -p "$PROJECT_DIR/uploads"
chmod 777 "$PROJECT_DIR/uploads"
print_status "Uploads directory ready"

# Step 7: Restart Apache
print_status "Restarting Apache..."
systemctl restart apache2
print_status "Apache restarted"

# Step 8: Test configuration
print_status "Testing configuration..."

# Test PHP
echo "<?php phpinfo(); ?>" > "$PROJECT_DIR/test.php"
if curl -s "http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/test.php" | grep -q "PHP Version"; then
    print_status "PHP working correctly"
else
    print_warning "PHP test failed"
fi
rm "$PROJECT_DIR/test.php"

# Final message
echo ""
echo "========================================="
echo -e "${GREEN}Deployment Complete!${NC}"
echo "========================================="
echo ""
echo "Access your website at:"
echo "http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/"
echo ""
echo "Default login (if created):"
echo "Register at: http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/frontend/js/register.php"
echo ""
echo "Backup location: $BACKUP_DIR"
echo ""
echo "To test if everything works:"
echo "1. Register a new seller account"
echo "2. Login to the dashboard"
echo "3. Add a property with an image"
echo "4. View properties on the landing page"
echo ""
echo "========================================="