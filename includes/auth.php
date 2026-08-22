<?php
/**
 * UpaKo - Authentication Handler
 * Manages user login, logout, session validation, and role-based access
 */

require_once __DIR__ . '/../config/config.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    global $pdo;
    try {
        $stmt = $pdo->prepare('
            SELECT u.*, r.name as role_name 
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.id = :id AND u.deleted_at IS NULL
        ');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    } catch (Exception $e) {
        error_log('Error fetching current user: ' . $e->getMessage());
        return null;
    }
}

// Check if user has specific role
function userHasRole($roleId) {
    $user = getCurrentUser();
    return $user && $user['role_id'] == $roleId;
}

// Check if user is admin
function isAdmin() {
    return userHasRole(ROLE_ADMIN);
}

// Check if user is landlord
function isLandlord() {
    return userHasRole(ROLE_LANDLORD);
}

// Check if user is tenant
function isTenant() {
    return userHasRole(ROLE_TENANT);
}

// Require login - redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . SITE_URL . '/public/login.php');
        exit;
    }
}

// Require specific role - redirect if not authorized
function requireRole($roleId) {
    requireLogin();
    
    if (!userHasRole($roleId)) {
        header('HTTP/1.0 403 Forbidden');
        include __DIR__ . '/../public/403.php';
        exit;
    }
}

// Require admin role
function requireAdmin() {
    requireRole(ROLE_ADMIN);
}

// Require landlord or admin
function requireLandlordOrAdmin() {
    requireLogin();
    $user = getCurrentUser();
    
    if (!($user && ($user['role_id'] == ROLE_LANDLORD || $user['role_id'] == ROLE_ADMIN))) {
        header('HTTP/1.0 403 Forbidden');
        include __DIR__ . '/../public/403.php';
        exit;
    }
}

// Require tenant or admin
function requireTenantOrAdmin() {
    requireLogin();
    $user = getCurrentUser();
    
    if (!($user && ($user['role_id'] == ROLE_TENANT || $user['role_id'] == ROLE_ADMIN))) {
        header('HTTP/1.0 403 Forbidden');
        include __DIR__ . '/../public/403.php';
        exit;
    }
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// User login
function loginUser($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('
            SELECT id, email, password_hash, role_id, status, first_name, last_name
            FROM users 
            WHERE email = :email AND deleted_at IS NULL
            LIMIT 1
        ');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }
        
        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Your account is not active. Please contact support.'];
        }
        
        if (!verifyPassword($password, $user['password_hash'])) {
            // Log failed attempt
            logAuditAction('login_failed', 'auth', null, 'user', 
                           json_encode(['email' => $email, 'ip' => $_SERVER['REMOTE_ADDR']]));
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['login_time'] = time();
        
        // Update last login
        $updateStmt = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = :id');
        $updateStmt->execute([':id' => $user['id']]);
        
        // Log successful login
        logAuditAction('login', 'auth', $user['id'], 'user', 
                       json_encode(['email' => $email, 'ip' => $_SERVER['REMOTE_ADDR']]));
        
        return ['success' => true, 'message' => 'Login successful.', 'user' => $user];
        
    } catch (Exception $e) {
        error_log('Login error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'An error occurred. Please try again.'];
    }
}

// User logout
function logoutUser() {
    if (isLoggedIn()) {
        $userId = $_SESSION['user_id'];
        logAuditAction('logout', 'auth', $userId, 'user');
    }
    
    // Clear session
    session_destroy();
    
    // Clear session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
}

// Check session timeout
function checkSessionTimeout() {
    if (isLoggedIn() && isset($_SESSION['login_time'])) {
        if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
            logoutUser();
            return false;
        }
        // Refresh login time
        $_SESSION['login_time'] = time();
    }
    return true;
}

// Validate password strength
function validatePasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors[] = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters long.";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    }
    
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        $errors[] = "Password must contain at least one special character.";
    }
    
    return $errors;
}

// Validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate phone number (basic format)
function validatePhoneNumber($phone) {
    return preg_match('/^(\+63|0)[0-9]{9,10}$/', preg_replace('/[^0-9+]/', '', $phone));
}

// Log audit action
function logAuditAction($action, $module = null, $recordId = null, $recordType = null, $newValues = null, $oldValues = null) {
    global $pdo;
    
    try {
        $userId = isLoggedIn() ? getCurrentUser()['id'] : null;
        
        $stmt = $pdo->prepare('
            INSERT INTO audit_logs 
            (user_id, action, module, record_id, record_type, old_values, new_values, ip_address, user_agent)
            VALUES 
            (:user_id, :action, :module, :record_id, :record_type, :old_values, :new_values, :ip_address, :user_agent)
        ');
        
        $stmt->execute([
            ':user_id' => $userId,
            ':action' => $action,
            ':module' => $module,
            ':record_id' => $recordId,
            ':record_type' => $recordType,
            ':old_values' => $oldValues,
            ':new_values' => $newValues,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
        return true;
    } catch (Exception $e) {
        error_log('Audit log error: ' . $e->getMessage());
        return false;
    }
}

// Register user
function registerUser($data) {
    global $pdo;
    
    // Validate input
    $errors = [];
    
    if (empty($data['first_name'])) {
        $errors[] = "First name is required.";
    }
    
    if (empty($data['last_name'])) {
        $errors[] = "Last name is required.";
    }
    
    if (empty($data['email']) || !validateEmail($data['email'])) {
        $errors[] = "Valid email is required.";
    }
    
    if (empty($data['phone']) || !validatePhoneNumber($data['phone'])) {
        $errors[] = "Valid phone number is required.";
    }
    
    if (empty($data['password'])) {
        $errors[] = "Password is required.";
    } else {
        $passwordErrors = validatePasswordStrength($data['password']);
        $errors = array_merge($errors, $passwordErrors);
    }
    
    if ($data['password'] !== $data['confirm_password']) {
        $errors[] = "Passwords do not match.";
    }
    
    if (empty($data['role_id']) || !in_array($data['role_id'], [ROLE_LANDLORD, ROLE_TENANT])) {
        $errors[] = "Valid role selection is required.";
    }
    
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Check if email already exists
    try {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute([':email' => $data['email']]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'errors' => ['Email address is already registered.']];
        }
    } catch (Exception $e) {
        return ['success' => false, 'errors' => ['An error occurred during registration.']];
    }
    
    // Insert user
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare('
            INSERT INTO users 
            (role_id, email, password_hash, first_name, last_name, phone, address, status)
            VALUES 
            (:role_id, :email, :password_hash, :first_name, :last_name, :phone, :address, :status)
        ');
        
        $stmt->execute([
            ':role_id' => $data['role_id'],
            ':email' => $data['email'],
            ':password_hash' => hashPassword($data['password']),
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':phone' => $data['phone'],
            ':address' => $data['address'] ?? null,
            ':status' => 'active'
        ]);
        
        $userId = $pdo->lastInsertId();
        
        // If tenant, create tenant record
        if ($data['role_id'] == ROLE_TENANT) {
            $tenantStmt = $pdo->prepare('
                INSERT INTO tenants 
                (user_id, date_registered, status)
                VALUES 
                (:user_id, CURDATE(), :status)
            ');
            
            $tenantStmt->execute([
                ':user_id' => $userId,
                ':status' => 'active'
            ]);
        }
        
        logAuditAction('register', 'auth', $userId, 'user', 
                       json_encode(['email' => $data['email'], 'role_id' => $data['role_id']]));
        
        $pdo->commit();
        
        return ['success' => true, 'message' => 'Registration successful. Please log in.', 'user_id' => $userId];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Registration error: ' . $e->getMessage());
        return ['success' => false, 'errors' => ['An error occurred during registration.']];
    }
}
?>
