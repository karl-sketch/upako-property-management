<?php
/**
 * UpaKo - Login Page
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

$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Security token mismatch. Please try again.';
    } else {
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error_message = 'Please enter your email and password.';
        } else {
            $result = loginUser($email, $password);
            
            if ($result['success']) {
                $user = $result['user'];
                
                // Redirect based on role
                if ($user['role_id'] == ROLE_ADMIN) {
                    header('Location: ' . SITE_URL . '/admin/dashboard.php');
                } elseif ($user['role_id'] == ROLE_LANDLORD) {
                    header('Location: ' . SITE_URL . '/landlord/dashboard.php');
                } else {
                    header('Location: ' . SITE_URL . '/tenant/dashboard.php');
                }
                exit;
            } else {
                $error_message = $result['message'];
            }
        }
    }
}

// Helper function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

$page_title = 'Login - UpaKo';
include __DIR__ . '/../includes/header.php';
?>

<style>
    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        padding: 20px;
    }
    
    .login-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 450px;
        padding: 40px;
    }
    
    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .login-header .logo-icon {
        font-size: 3rem;
        color: #1e3a8a;
        margin-bottom: 10px;
    }
    
    .login-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e3a8a;
        margin: 0;
    }
    
    .login-header p {
        color: #666;
        font-size: 0.9rem;
        margin-top: 5px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    
    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .form-check {
        margin-bottom: 20px;
    }
    
    .form-check input {
        margin-right: 8px;
    }
    
    .form-check label {
        margin-bottom: 0;
        font-weight: 500;
        color: #666;
    }
    
    .btn-login {
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
    
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30, 58, 138, 0.3);
    }
    
    .login-footer {
        text-align: center;
        margin-top: 20px;
        border-top: 1px solid #eee;
        padding-top: 20px;
    }
    
    .login-footer p {
        color: #666;
        margin: 0;
        font-size: 0.9rem;
    }
    
    .login-footer a {
        color: #1e3a8a;
        text-decoration: none;
        font-weight: 600;
    }
    
    .login-footer a:hover {
        text-decoration: underline;
    }
    
    .alert {
        margin-bottom: 20px;
        border-radius: 6px;
        border: none;
        padding: 12px 16px;
        animation: slideDown 0.3s ease;
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
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-icon">
                <i class="fas fa-building"></i>
            </div>
            <h1>UpaKo</h1>
            <p>Property Management System</p>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Enter your email" 
                    required
                    autocomplete="email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required
                    autocomplete="current-password"
                >
            </div>
            
            <div class="form-check">
                <input type="checkbox" id="remember" name="remember" class="form-check-input">
                <label class="form-check-label" for="remember">
                    Remember me
                </label>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i> Login
            </button>
        </form>
        
        <div class="login-footer">
            <p>Don't have an account? <a href="<?php echo SITE_URL; ?>/public/register.php">Register here</a></p>
            <p style="margin-top: 10px; color: #999; font-size: 0.85rem;">
                Demo: admin@upako.com / Admin123456
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
