# UpaKo - Property Management System

A comprehensive PHP-based property management system designed for landlords and property managers to efficiently manage properties, tenants, leases, and payments.

## 🎯 Features

### Core Functionality
- **Property Management** - Add, edit, and manage multiple properties with detailed information
- **Unit Management** - Track individual units within properties with occupancy status
- **Tenant Management** - Manage tenant information, documents, and tenant portal access
- **Lease Management** - Create, track, and manage lease agreements with automatic reminders
- **Billing & Payments** - Automated billing system with payment tracking and reminders
- **Maintenance Requests** - Tenant-submitted maintenance requests with tracking and assignment
- **Financial Reports** - Comprehensive reports on income, expenses, and occupancy rates
- **Admin Panel** - System administration and user management

### User Roles
- **Admin** - Full system access, user management, and system settings
- **Landlord** - Manage properties, tenants, bills, and view reports
- **Tenant** - View lease details, pay bills online, and submit maintenance requests

### Security Features
- CSRF token protection
- Password hashing with bcrypt
- SQL injection prevention (prepared statements)
- Input sanitization and validation
- Role-based access control (RBAC)
- Session management

## 📋 System Requirements

- **PHP 7.4+** (8.0+ recommended)
- **MySQL 5.7+** or **MariaDB 10.3+**
- **Apache** or **Nginx** with `.htaccess` support
- **Composer** (for dependency management)
- **OpenSSL** enabled for secure connections

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/karl-sketch/upako-property-management.git
cd upako-property-management
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE upako_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Import database schema
mysql -u root -p upako_db < database/schema.sql
mysql -u root -p upako_db < database/seeds.sql
```

### 4. Configure Application
```bash
# Copy configuration template
cp config/config.example.php config/config.php

# Edit configuration file with your settings
nano config/config.php
```

Update the following in `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'upako_db');
define('SITE_URL', 'http://localhost/upako-property-management');
define('SITE_NAME', 'UpaKo');
```

### 5. File Permissions
```bash
# Set proper permissions
chmod 755 config
chmod 644 config/config.php
chmod 755 uploads
chmod 755 logs
```

### 6. Web Server Configuration

**Apache (.htaccess)**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /upako-property-management/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?/$1 [L]
</IfModule>
```

**Nginx**
```nginx
location /upako-property-management/ {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 📁 Project Structure

```
upako-property-management/
├── config/
│   ├── config.php              # Main configuration
│   └── database.php            # Database connection
├── database/
│   ├── schema.sql              # Database schema
│   └── seeds.sql               # Sample data
├── includes/
│   ├── auth.php                # Authentication functions
│   ├── header.php              # Page header template
│   ├── navbar.php              # Navigation bar
│   ├── footer.php              # Page footer
│   └── helpers.php             # Utility functions
├── public/
│   ├── index.php               # Landing page
│   ├── login.php               # Login page
│   ├── register.php            # Registration page
│   └── logout.php              # Logout handler
├── landlord/
│   ├── dashboard.php           # Landlord dashboard
│   ├── properties.php          # Property management
│   ├── tenants.php             # Tenant management
│   ├── bills.php               # Billing management
│   ├── payments.php            # Payment tracking
│   ├── maintenance.php         # Maintenance requests
│   └── reports.php             # Financial reports
├── tenant/
│   ├── dashboard.php           # Tenant dashboard
│   ├── lease.php               # View lease details
│   ├── bills.php               # View bills
│   ├── payments.php            # Make payments
│   └── maintenance.php         # Submit requests
├── admin/
│   ├── dashboard.php           # Admin dashboard
│   ├── users.php               # User management
│   ├── system-settings.php     # System configuration
│   └── activity-logs.php       # System activity logs
├── api/
│   ├── notifications/
│   │   └── count.php           # Notification count endpoint
│   └── ... (other API endpoints)
├── uploads/                    # File uploads directory
├── logs/                       # Application logs
└── assets/
    ├── css/
    │   └── style.css           # Custom styles
    ├── js/
    │   └── script.js           # Custom scripts
    └── images/                 # Images and icons
```

## 🔐 Default Credentials

After seeding the database, use these credentials to log in:

**Admin Account**
```
Email: admin@upako.com
Password: Admin123456
```

**Sample Landlord Account**
```
Email: landlord@upako.com
Password: Landlord123456
```

**Sample Tenant Account**
```
Email: tenant@upako.com
Password: Tenant123456
```

⚠️ **IMPORTANT**: Change these passwords immediately in production!

## 📖 Usage Guide

### For Landlords

1. **Add Property**
   - Navigate to Dashboard → Add Property
   - Enter property details, address, and number of units
   - Set up unit information (type, size, rental rate)

2. **Manage Tenants**
   - Add tenants through the Tenants section
   - Upload tenant documents (ID, employment letter, etc.)
   - Manage lease agreements

3. **Create Billing**
   - Go to Bills section
   - Create monthly/custom bills for units
   - Set payment terms and due dates
   - System sends automatic reminders

4. **Track Payments**
   - Monitor payment status in real-time
   - View payment history
   - Generate financial reports

5. **Maintenance Requests**
   - View tenant-submitted maintenance requests
   - Assign tasks to maintenance staff
   - Track completion status

### For Tenants

1. **View Lease**
   - Access lease details from dashboard
   - Download lease agreement documents
   - Check lease end date and renewal status

2. **Pay Bills**
   - View all outstanding bills
   - Make secure online payments
   - Download payment receipts

3. **Submit Maintenance Requests**
   - Report maintenance issues
   - Track request status
   - Communicate with landlord

## 🔑 Key Functions

### Authentication
```php
// Check if user is logged in
if (isLoggedIn()) {
    $user = getCurrentUser();
}

// Log in user
loginUser($email, $password);

// Log out
logoutUser();

// Check role
if (isLandlord()) { /* ... */ }
if (isTenant()) { /* ... */ }
if (isAdmin()) { /* ... */ }
```

### Utilities
```php
// Format currency
echo formatCurrency(1500); // ₱1,500.00

// Format date
echo formatDate('2026-12-31', 'M d, Y'); // Dec 31, 2026

// Validate email
isValidEmail('email@example.com');

// Validate phone (PH format)
isValidPhone('+639123456789');

// Validate password strength
isValidPassword('SecurePass123!');
```

## 🛠️ API Endpoints

### Notifications
```
GET /api/notifications/count.php
Response: { "success": true, "count": 5 }
```

## 📊 Database Schema

### Key Tables

**users** - User accounts
**properties** - Property listings
**units** - Individual units in properties
**tenants** - Tenant information
**leases** - Lease agreements
**bills** - Bill/Invoice records
**payments** - Payment records
**maintenance_requests** - Maintenance requests
**notifications** - User notifications
**activity_logs** - System activity logs

See `database/schema.sql` for complete schema.

## 🔄 Regular Tasks (Cron Jobs)

Set up these cron jobs for automated operations:

```bash
# Send payment reminders (daily at 8 AM)
0 8 * * * /usr/bin/php /var/www/upako/cron/send-payment-reminders.php

# Generate monthly bills (1st of month at midnight)
0 0 1 * * /usr/bin/php /var/www/upako/cron/generate-bills.php

# Process overdue bills (daily at 9 AM)
0 9 * * * /usr/bin/php /var/www/upako/cron/process-overdue.php
```

## 📝 Configuration Options

Key configuration settings in `config/config.php`:

```php
// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'upako_db');

// Application
define('SITE_URL', 'http://localhost/upako');
define('SITE_NAME', 'UpaKo');
define('TIMEZONE', 'Asia/Manila');

// Security
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_TIMEOUT', 3600);

// Email
define('MAIL_HOST', 'smtp.mailtrap.io');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your_username');
define('MAIL_PASSWORD', 'your_password');
define('MAIL_FROM', 'noreply@upako.com');
```

## 🐛 Troubleshooting

### Database Connection Error
- Verify database credentials in `config/config.php`
- Ensure MySQL server is running
- Check database user has proper privileges

### 404 Errors
- Verify `.htaccess` is in root directory
- Enable `mod_rewrite` in Apache
- Check file permissions

### Session Issues
- Verify `sessions` table exists in database
- Check file permissions on `config` directory
- Clear browser cookies and restart

### Email Not Sending
- Verify mail server credentials
- Check firewall allows outgoing mail
- Review application logs

## 📞 Support & Contribution

For issues, feature requests, or contributions:
- Open an issue on GitHub
- Submit pull requests
- Contact development team

## 📄 License

This project is licensed under the MIT License - see LICENSE file for details.

## 👥 Team

- **Lead Developer**: Karl
- **Database Design**: UpaKo Team
- **Testing**: QA Team

## 🗺️ Roadmap

### v2.0 (Planned)
- Mobile app (iOS/Android)
- Advanced analytics dashboard
- Integration with payment gateways
- Email marketing module

### v2.5 (Future)
- SMS notifications
- Document e-signature
- Tenant credit scoring
- Automated rent collection

## ⚠️ Security Notes

1. Always keep PHP and dependencies updated
2. Use HTTPS in production
3. Regularly backup your database
4. Monitor activity logs for suspicious behavior
5. Use strong, unique passwords
6. Keep configuration files outside web root
7. Use environment variables for sensitive data

## 🎓 Learning Resources

- [PHP Official Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [OWASP Security Guidelines](https://owasp.org/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)

---

**Last Updated**: August 2026
**Version**: 1.0.0
**Status**: Production Ready

For more information, visit the project repository or documentation wiki.
