<?php
/**
 * UpaKo - Landing Page / Index
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
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

$page_title = 'Home - UpaKo Property Management System';
include __DIR__ . '/includes/header.php';
?>

<style>
    .hero-section {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: white;
        padding: 100px 20px;
        text-align: center;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .hero-content h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .hero-content p {
        font-size: 1.3rem;
        margin-bottom: 30px;
        opacity: 0.95;
    }
    
    .hero-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn-hero {
        padding: 12px 30px;
        font-size: 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .btn-login-hero {
        background: white;
        color: #1e3a8a;
    }
    
    .btn-login-hero:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    
    .btn-register-hero {
        background: transparent;
        color: white;
        border: 2px solid white;
    }
    
    .btn-register-hero:hover {
        background: white;
        color: #1e3a8a;
    }
    
    .features-section {
        padding: 80px 20px;
        background: white;
    }
    
    .features-title {
        text-align: center;
        margin-bottom: 50px;
    }
    
    .features-title h2 {
        font-size: 2.5rem;
        color: #1e3a8a;
        margin-bottom: 15px;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .feature-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .feature-icon {
        font-size: 3rem;
        color: #10b981;
        margin-bottom: 15px;
    }
    
    .feature-card h3 {
        color: #1e3a8a;
        margin-bottom: 10px;
        font-size: 1.3rem;
    }
    
    .feature-card p {
        color: #666;
        line-height: 1.6;
    }
    
    .cta-section {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: white;
        padding: 60px 20px;
        text-align: center;
    }
    
    .cta-section h2 {
        font-size: 2rem;
        margin-bottom: 20px;
    }
    
    .cta-section p {
        font-size: 1.1rem;
        margin-bottom: 30px;
        opacity: 0.9;
    }
    
    .stats-section {
        background: var(--light-bg);
        padding: 60px 20px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 40px;
        max-width: 1000px;
        margin: 0 auto;
        text-align: center;
    }
    
    .stat-box h3 {
        font-size: 2.5rem;
        color: #1e3a8a;
        font-weight: 700;
    }
    
    .stat-box p {
        color: #666;
        margin-top: 10px;
    }
</style>

<div class="hero-section">
    <div class="hero-content">
        <h1><i class="fas fa-building"></i> UpaKo</h1>
        <p>Your Property Management Solution</p>
        <p style="font-size: 1rem; opacity: 0.9;">Manage properties, tenants, leases, and payments all in one place</p>
        <div class="hero-buttons">
            <a href="<?php echo SITE_URL; ?>/public/login.php" class="btn-hero btn-login-hero">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="<?php echo SITE_URL; ?>/public/register.php" class="btn-hero btn-register-hero">
                <i class="fas fa-user-plus"></i> Get Started
            </a>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="features-title">
        <h2>Why Choose UpaKo?</h2>
        <p style="color: #666; font-size: 1.1rem;">Complete property management platform built for landlords and tenants</p>
    </div>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-building"></i>
            </div>
            <h3>Property Management</h3>
            <p>Easily manage multiple properties, units, and amenities all from one dashboard</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>Tenant Portal</h3>
            <p>Give tenants access to view their lease, bills, and submit maintenance requests online</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <h3>Billing & Payments</h3>
            <p>Automate billing, track payments, and manage overdue accounts effortlessly</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h3>Maintenance Tracking</h3>
            <p>Track maintenance requests, assign tasks, and keep a complete history</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-file-contract"></i>
            </div>
            <h3>Lease Management</h3>
            <p>Store and manage lease agreements with automatic renewal reminders</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h3>Reports & Analytics</h3>
            <p>Generate detailed reports on income, occupancy, and property performance</p>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="stats-grid">
        <div class="stat-box">
            <h3>500+</h3>
            <p>Active Users</p>
        </div>
        <div class="stat-box">
            <h3>1000+</h3>
            <p>Properties Managed</p>
        </div>
        <div class="stat-box">
            <h3>10K+</h3>
            <p>Tenants</p>
        </div>
        <div class="stat-box">
            <h3>99.9%</h3>
            <p>Uptime</p>
        </div>
    </div>
</div>

<div class="cta-section">
    <h2>Ready to streamline your property management?</h2>
    <p>Join thousands of landlords and property managers already using UpaKo</p>
    <a href="<?php echo SITE_URL; ?>/public/register.php" class="btn-hero btn-register-hero">
        <i class="fas fa-user-plus"></i> Create Account Now
    </a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
