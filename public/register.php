<?php
/**
 * UpaKo - Registration Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if already logged in
if (isLoggedIn()) {
    $user = getCurrentUser();
    if (isAdmin()) {
        header('Location: ' . SITE_URL . '/admin/dashboard.php');
    } elseif (isLandlord()) {
        header('Location: ' . SITE_URL . '/landlord/dashboard.php');
    } else {
        header('Location: ' . SITE_URL . '/tenant/dashboard.php');
    }
    exit;
}

$errors = [];
$success_message = '';
$formData = [];

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token mismatch. Please try again.';
    } else {
        // Collect form data
        $formData = [
            'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
            'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
            'email' => sanitizeInput($_POST['email'] ?? ''),
            'phone' => sanitizeInput($_POST['phone'] ?? ''),
            'address' => sanitizeInput($_POST['address'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'role_id' => isset($_POST['role_id']) ? intval($_POST['role_id']) : null
        ];
        
        $result = registerUser($formData);
        
        if ($result['success']) {
            $success_message = $result['message'];
            $formData = []; // Clear form
            // Redirect to login after 2 seconds
            echo '<meta http-equiv="refresh" content="2;url=' . SITE_URL . '/public/login.php">';
        } else {
            $errors = $result['errors'];
        }
    }
}

// Helper function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

$page_title = 'Register - UpaKo';
include __DIR__ . '/../includes/header.php';
?>

<style>
    .register-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        padding: 20px;
    }
    
    .register-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 550px;
        padding: 40px;
    }
    
    .register-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .register-header .logo-icon {
        font-size: 2.5rem;
        color: #1e3a8a;
        margin-bottom: 10px;
    }
    
    .register-header h1 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1e3a8a;
        margin: 0;
    }
    
    .register-header p {
        color: #666;
        font-size: 0.9rem;
        margin-top: 5px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    
    .form-row.full {
        grid-template-columns: 1fr;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
    }
    
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        font-family: 'Poppins', sans-serif;
    }
    
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .form-check {
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
    }
    
    .form-check input {
        margin-top: 3px;
        margin-right: 8px;
        width: auto;
        cursor: pointer;
    }
    
    .form-check label {
        margin-bottom: 0;
        font-weight: 500;
        color: #666;
        font-size: 0.9rem;
        cursor: pointer;
    }
    
    .btn-register {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30, 58, 138, 0.3);
    }
    
    .register-footer {
        text-align: center;
        margin-top: 20px;
        border-top: 1px solid #eee;
        padding-top: 20px;
    }
    
    .register-footer p {
        color: #666;
        margin: 0;
        font-size: 0.9rem;
    }
    
    .register-footer a {
        color: #1e3a8a;
        text-decoration: none;
        font-weight: 600;
    }
    
    .register-footer a:hover {
        text-decoration: underline;
    }
    
    .alert {
        margin-bottom: 20px;
        border-radius: 6px;
        border: none;
        padding: 12px 16px;
        animation: slideDown 0.3s ease;
    }
    
    .alert ul {
        margin: 8px 0 0 20px;
        padding: 0;
    }
    
    .alert li {
        margin-bottom: 4px;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 600px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <div class="logo-icon">
                <i class="fas fa-building"></i>
            </div>
            <h1>Join UpaKo</h1>
            <p>Create your account in seconds</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <strong>Registration Error:</strong>
                <ul style="margin-bottom: 0;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($success_message); ?>
                <p style="margin: 8px 0 0 0; font-size: 0.85rem;">Redirecting to login...</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php else: ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input 
                        type="text" 
                        id="first_name" 
                        name="first_name" 
                        placeholder="John" 
                        required
                        value="<?php echo $formData['first_name'] ?? ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input 
                        type="text" 
                        id="last_name" 
                        name="last_name" 
                        placeholder="Doe" 
                        required
                        value="<?php echo $formData['last_name'] ?? ''; ?>"
                    >
                </div>
            </div>
            
            <div class="form-group full">
                <label for="email">Email Address *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="your@email.com" 
                    required
                    autocomplete="email"
                    value="<?php echo $formData['email'] ?? ''; ?>"
                >
            </div>
            
            <div class="form-group full">
                <label for="phone">Phone Number *</label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    placeholder="+63900000000 or 09000000000" 
                    required
                    value="<?php echo $formData['phone'] ?? ''; ?>"
                >
                <small class="text-muted">Format: +63900000000 or 09000000000</small>
            </div>
            
            <div class="form-group full">
                <label for="address">Address</label>
                <input 
                    type="text" 
                    id="address" 
                    name="address" 
                    placeholder="Your residential address"
                    value="<?php echo $formData['address'] ?? ''; ?>"
                >
            </div>
            
            <div class="form-group full">
                <label for="role_id">Account Type *</label>
                <select id="role_id" name="role_id" required>
                    <option value="">-- Select Account Type --</option>
                    <option value="<?php echo ROLE_LANDLORD; ?>" <?php echo (($formData['role_id'] ?? null) == ROLE_LANDLORD) ? 'selected' : ''; ?>>
                        <i class="fas fa-home"></i> Landlord / Property Owner
                    </option>
                    <option value="<?php echo ROLE_TENANT; ?>" <?php echo (($formData['role_id'] ?? null) == ROLE_TENANT) ? 'selected' : ''; ?>>
                        <i class="fas fa-user"></i> Tenant / Renter
                    </option>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Min 8 characters" 
                        required
                        autocomplete="new-password"
                    >
                    <small class="text-muted d-block mt-2">
                        Must contain: uppercase, lowercase, number, special character
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Confirm password" 
                        required
                        autocomplete="new-password"
                    >
                </div>
            </div>
            
            <div class="form-check">
                <input 
                    type="checkbox" 
                    id="terms" 
                    name="terms" 
                    required
                    class="form-check-input"
                >
                <label class="form-check-label" for="terms">
                    I agree to the Terms and Conditions and Privacy Policy
                </label>
            </div>
            
            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus me-2"></i> Create Account
            </button>
        </form>
        
        <?php endif; ?>
        
        <div class="register-footer">
            <p>Already have an account? <a href="<?php echo SITE_URL; ?>/public/login.php">Login here</a></p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
