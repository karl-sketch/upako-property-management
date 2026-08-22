<?php
/**
 * UpaKo - Navigation Bar Include
 */

$currentUser = getCurrentUser();
?>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>/">
            <i class="fas fa-building brand-icon"></i>UpaKo
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/public/login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/public/register.php">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Admin Navigation -->
                    <?php if (isAdmin()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-shield-alt"></i> Admin
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/users.php">Users</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/properties.php">Properties</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/audit_logs.php">Audit Logs</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/settings.php">Settings</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Landlord Navigation -->
                    <?php if (isLandlord()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="landlordDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-home"></i> Properties
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/landlord/dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/landlord/properties.php">My Properties</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/landlord/units.php">Units</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/landlord/tenants.php">Tenants</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/landlord/leases.php">Leases</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/landlord/bills.php">Bills</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/landlord/payments.php">Payments</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Tenant Navigation -->
                    <?php if (isTenant()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/tenant/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/tenant/rentals.php">
                                <i class="fas fa-file-invoice-dollar"></i> My Rentals
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Notifications -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/notifications.php">
                            <i class="fas fa-bell"></i> <span class="badge badge-danger" id="notification-count"></span>
                        </a>
                    </li>
                    
                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($currentUser['first_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/profile.php">
                                <i class="fas fa-user"></i> Profile
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script>
// Load notification count via AJAX
$(document).ready(function() {
    function loadNotifications() {
        $.ajax({
            url: '<?php echo SITE_URL; ?>/api/notifications/count.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.count > 0) {
                    $('#notification-count').text(response.count).show();
                } else {
                    $('#notification-count').hide();
                }
            }
        });
    }
    
    loadNotifications();
    setInterval(loadNotifications, 30000); // Refresh every 30 seconds
});
</script>
