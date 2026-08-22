<?php
/**
 * UpaKo - Utility Helper Functions
 */

/**
 * Format currency value
 */
function formatCurrency($amount, $currency = '₱') {
    return $currency . number_format($amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

/**
 * Get status badge color
 */
function getStatusBadgeColor($status) {
    $colors = [
        'active' => 'success',
        'inactive' => 'secondary',
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'paid' => 'success',
        'unpaid' => 'danger',
        'overdue' => 'danger',
        'partially_paid' => 'warning',
        'occupied' => 'success',
        'vacant' => 'secondary',
        'maintenance' => 'warning',
        'completed' => 'success',
        'in_progress' => 'info'
    ];
    
    return $colors[$status] ?? 'secondary';
}

/**
 * Calculate occupancy rate
 */
function calculateOccupancyRate($occupied, $total) {
    if ($total == 0) return 0;
    return round(($occupied / $total) * 100, 2);
}

/**
 * Calculate days until date
 */
function daysUntil($date) {
    $targetDate = strtotime($date);
    $today = strtotime('today');
    $daysLeft = ($targetDate - $today) / (60 * 60 * 24);
    return ceil($daysLeft);
}

/**
 * Truncate text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Check if email is valid
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if phone number is valid (Philippine format)
 */
function isValidPhone($phone) {
    // Accept +639XXXXXXXXX or 09XXXXXXXXX format
    return preg_match('/^(\+63|0)9\d{9}$/', preg_replace('/\s+/', '', $phone));
}

/**
 * Validate password strength
 */
function isValidPassword($password) {
    // At least 8 characters
    // At least one uppercase letter
    // At least one lowercase letter
    // At least one digit
    // At least one special character
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

/**
 * Get password strength indicator
 */
function getPasswordStrength($password) {
    $strength = 0;
    
    if (strlen($password) >= 8) $strength++;
    if (preg_match('/[a-z]/', $password)) $strength++;
    if (preg_match('/[A-Z]/', $password)) $strength++;
    if (preg_match('/\d/', $password)) $strength++;
    if (preg_match('/[@$!%*?&]/', $password)) $strength++;
    
    $levels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
    return $levels[$strength] ?? 'Very Weak';
}

/**
 * Convert byte size to human readable format
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Generate random string
 */
function generateRandomString($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * Check if string is JSON
 */
function isJSON($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Get tenant full name
 */
function getTenantFullName($firstName, $lastName) {
    return trim($firstName . ' ' . $lastName);
}

/**
 * Format lease term
 */
function formatLeaseTerm($startDate, $endDate) {
    $start = date('M d, Y', strtotime($startDate));
    $end = date('M d, Y', strtotime($endDate));
    return "$start - $end";
}

/**
 * Calculate lease remaining days
 */
function getLeaseRemainingDays($endDate) {
    return daysUntil($endDate);
}

/**
 * Get payment status badge
 */
function getPaymentStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning">Pending</span>',
        'approved' => '<span class="badge bg-success">Approved</span>',
        'rejected' => '<span class="badge bg-danger">Rejected</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}

/**
 * Get bill status badge
 */
function getBillStatusBadge($status) {
    $badges = [
        'paid' => '<span class="badge bg-success">Paid</span>',
        'unpaid' => '<span class="badge bg-danger">Unpaid</span>',
        'overdue' => '<span class="badge bg-danger">Overdue</span>',
        'partially_paid' => '<span class="badge bg-warning">Partially Paid</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}

/**
 * Calculate age from birthdate
 */
function calculateAge($birthDate) {
    $today = new DateTime();
    $birth = new DateTime($birthDate);
    $age = $today->diff($birth);
    return $age->y;
}

/**
 * Format Philippine address
 */
function formatAddress($street, $city, $province = '', $zipCode = '') {
    $address = $street;
    if (!empty($city)) $address .= ', ' . $city;
    if (!empty($province)) $address .= ', ' . $province;
    if (!empty($zipCode)) $address .= ' ' . $zipCode;
    return $address;
}

/**
 * Parse address into components
 */
function parseAddress($address) {
    $parts = array_map('trim', explode(',', $address));
    return [
        'street' => $parts[0] ?? '',
        'city' => $parts[1] ?? '',
        'province' => $parts[2] ?? '',
        'zip_code' => $parts[3] ?? ''
    ];
}

/**
 * Get month name
 */
function getMonthName($month) {
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
    return $months[$month] ?? '';
}

/**
 * Check if date is in past
 */
function isDatePast($date) {
    return strtotime($date) < strtotime('today');
}

/**
 * Check if date is today
 */
function isDateToday($date) {
    return date('Y-m-d', strtotime($date)) === date('Y-m-d');
}

/**
 * Check if date is in future
 */
function isDateFuture($date) {
    return strtotime($date) > strtotime('today');
}

/**
 * Send email notification
 */
function sendEmailNotification($to, $subject, $message, $htmlMessage = '') {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . SITE_NAME . " <noreply@upako.com>" . "\r\n";
    
    $body = $htmlMessage ?: nl2br($message);
    
    return mail($to, $subject, $body, $headers);
}

/**
 * Create log entry
 */
function createLogEntry($action, $description, $userId = null, $targetId = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('
            INSERT INTO activity_logs (user_id, action, description, target_id, created_at)
            VALUES (:user_id, :action, :description, :target_id, NOW())
        ');
        
        $stmt->execute([
            ':user_id' => $userId,
            ':action' => $action,
            ':description' => $description,
            ':target_id' => $targetId
        ]);
        
        return true;
    } catch (Exception $e) {
        error_log('Log entry error: ' . $e->getMessage());
        return false;
    }
}

?>
