<?php
/**
 * UpaKo - Sidebar Include (for admin and landlord dashboards)
 */

$currentUser = getCurrentUser();
?>

<div class="sidebar bg-white border-end">
    <div class="sidebar-header p-3 border-bottom">
        <h5 class="mb-0">
            <i class="fas fa-bars me-2"></i>Menu
        </h5>
    </div>
    
    <nav class="nav flex-column">
        <?php if (isAdmin()): ?>
            <!-- Admin Menu -->
            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                <i class="fas fa-chart-line"></i> <span>Dashboard</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/users.php">
                <i class="fas fa-users"></i> <span>Users</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/properties.php">
                <i class="fas fa-building"></i> <span>Properties</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/reports.php">
                <i class="fas fa-chart-bar"></i> <span>Reports</span>
            </a>
            <div class="nav-divider"></div>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/audit_logs.php">
                <i class="fas fa-history"></i> <span>Audit Logs</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/settings.php">
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a>
            
        <?php elseif (isLandlord()): ?>
            <!-- Landlord Menu -->
            <a class="nav-link" href="<?php echo SITE_URL; ?>/landlord/dashboard.php">
                <i class="fas fa-chart-line"></i> <span>Dashboard</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/landlord/properties.php">
                <i class="fas fa-building"></i> <span>Properties</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/landlord/units.php">
                <i class="fas fa-door-open"></i> <span>Units</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/landlord/tenants.php">
                <i class="fas fa-users"></i> <span>Tenants</span>
            </a>
            <div class="nav-divider"></div>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/landlord/leases.php">
                <i class="fas fa-file-contract"></i> <span>Leases</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/landlord/bills.php">
                <i class="fas fa-receipt"></i> <span>Bills</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/landlord/payments.php">
                <i class="fas fa-credit-card"></i> <span>Payments</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/landlord/maintenance.php">
                <i class="fas fa-tools"></i> <span>Maintenance</span>
            </a>
            <div class="nav-divider"></div>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/landlord/reports.php">
                <i class="fas fa-chart-bar"></i> <span>Reports</span>
            </a>
            
        <?php elseif (isTenant()): ?>
            <!-- Tenant Menu -->
            <a class="nav-link" href="<?php echo SITE_URL; ?>/tenant/dashboard.php">
                <i class="fas fa-chart-line"></i> <span>Dashboard</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/tenant/rentals.php">
                <i class="fas fa-file-invoice-dollar"></i> <span>My Rentals</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/tenant/bills.php">
                <i class="fas fa-receipt"></i> <span>Bills</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/tenant/payments.php">
                <i class="fas fa-credit-card"></i> <span>Payments</span>
            </a>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/tenant/maintenance.php">
                <i class="fas fa-tools"></i> <span>Maintenance Requests</span>
            </a>
        <?php endif; ?>
    </nav>
</div>

<style>
    .sidebar {
        height: 100vh;
        overflow-y: auto;
        position: fixed;
        left: 0;
        top: 0;
        width: 250px;
        z-index: 100;
    }
    
    .sidebar-header {
        background-color: var(--primary-color);
        color: white;
        padding-top: 80px !important;
    }
    
    .sidebar .nav {
        padding: 1rem 0;
    }
    
    .sidebar .nav-link {
        color: #666;
        border-radius: 0;
        margin: 0;
        padding: 0.75rem 1.5rem;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .sidebar .nav-link:hover {
        background-color: var(--light-bg);
        color: var(--primary-color);
        border-left-color: var(--primary-color);
    }
    
    .sidebar .nav-link.active {
        background-color: var(--light-bg);
        color: var(--primary-color);
        border-left-color: var(--primary-color);
        font-weight: 600;
    }
    
    .sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
    }
    
    .nav-divider {
        height: 1px;
        background-color: var(--border-color);
        margin: 0.5rem 0;
    }
    
    .main-content {
        margin-left: 250px;
        padding-top: 80px;
    }
    
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar.active {
            transform: translateX(0);
        }
        
        .main-content {
            margin-left: 0;
        }
    }
</style>
