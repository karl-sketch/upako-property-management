<?php
/**
 * UpaKo - Main Configuration File
 */

require_once __DIR__ . '/database.php';

// Application settings
define('APP_NAME', 'UpaKo');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development or production

// Error reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Role definitions
define('ROLE_ADMIN', 1);
define('ROLE_LANDLORD', 2);
define('ROLE_TENANT', 3);

// Role names
$ROLES = [
    ROLE_ADMIN => 'Administrator',
    ROLE_LANDLORD => 'Landlord',
    ROLE_TENANT => 'Tenant'
];

// User status
define('USER_ACTIVE', 'active');
define('USER_INACTIVE', 'inactive');
define('USER_PENDING', 'pending');

// Property status
define('PROPERTY_ACTIVE', 'active');
define('PROPERTY_INACTIVE', 'inactive');

// Unit status
define('UNIT_AVAILABLE', 'available');
define('UNIT_OCCUPIED', 'occupied');
define('UNIT_RESERVED', 'reserved');
define('UNIT_MAINTENANCE', 'maintenance');
define('UNIT_INACTIVE', 'inactive');

// Lease status
define('LEASE_ACTIVE', 'active');
define('LEASE_PENDING', 'pending');
define('LEASE_EXPIRED', 'expired');
define('LEASE_TERMINATED', 'terminated');

// Bill status
define('BILL_UNPAID', 'unpaid');
define('BILL_PARTIALLY_PAID', 'partially_paid');
define('BILL_PAID', 'paid');
define('BILL_OVERDUE', 'overdue');

// Payment status
define('PAYMENT_PENDING', 'pending');
define('PAYMENT_APPROVED', 'approved');
define('PAYMENT_REJECTED', 'rejected');

// Maintenance status
define('MAINTENANCE_PENDING', 'pending');
define('MAINTENANCE_APPROVED', 'approved');
define('MAINTENANCE_IN_PROGRESS', 'in_progress');
define('MAINTENANCE_COMPLETED', 'completed');
define('MAINTENANCE_REJECTED', 'rejected');

// Payment methods
$PAYMENT_METHODS = ['cash', 'gcash', 'bank_transfer', 'check', 'other'];

// Priority levels
$PRIORITY_LEVELS = ['low', 'medium', 'high', 'emergency'];

// Maintenance categories
$MAINTENANCE_CATEGORIES = [
    'plumbing',
    'electrical',
    'internet',
    'air_conditioning',
    'furniture',
    'security',
    'cleaning',
    'other'
];

// Property types
$PROPERTY_TYPES = [
    'apartment',
    'boarding_house',
    'dormitory',
    'house',
    'condominium',
    'commercial_space',
    'room',
    'other'
];

// Unit types
$UNIT_TYPES = [
    'studio',
    'one_bedroom',
    'two_bedroom',
    'three_bedroom',
    'commercial',
    'other'
];

// Date and time formats
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'M d, Y');
define('DISPLAY_DATETIME_FORMAT', 'M d, Y h:i A');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Helper function to get role name
function getRoleName($roleId) {
    global $ROLES;
    return $ROLES[$roleId] ?? 'Unknown';
}

// Helper function to convert array to option elements
function arrayToOptions($array, $selected = null) {
    $options = '';
    foreach ($array as $value => $label) {
        $isSelected = ($value === $selected) ? 'selected' : '';
        $options .= "<option value='{$value}' {$isSelected}>{$label}</option>";
    }
    return $options;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
