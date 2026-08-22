<?php
/**
 * UpaKo - Landlord Dashboard
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';

// Require landlord or admin
requireLandlordOrAdmin();

$currentUser = getCurrentUser();
$userId = $currentUser['id'];

global $pdo;

// Get landlord statistics
try {
    // Total properties
    $propertiesStmt = $pdo->prepare('
        SELECT COUNT(*) as total FROM properties 
        WHERE landlord_id = :landlord_id AND deleted_at IS NULL
    ');
    $propertiesStmt->execute([':landlord_id' => $userId]);
    $propertyCount = $propertiesStmt->fetch()['total'];
    
    // Total units
    $unitsStmt = $pdo->prepare('
        SELECT COUNT(*) as total FROM units u
        JOIN properties p ON u.property_id = p.id
        WHERE p.landlord_id = :landlord_id AND u.deleted_at IS NULL
    ');
    $unitsStmt->execute([':landlord_id' => $userId]);
    $unitCount = $unitsStmt->fetch()['total'];
    
    // Occupied units
    $occupiedStmt = $pdo->prepare('
        SELECT COUNT(*) as total FROM units u
        JOIN properties p ON u.property_id = p.id
        WHERE p.landlord_id = :landlord_id AND u.status = :status AND u.deleted_at IS NULL
    ');
    $occupiedStmt->execute([':landlord_id' => $userId, ':status' => 'occupied']);
    $occupiedCount = $occupiedStmt->fetch()['total'];
    
    // Active leases
    $leasesStmt = $pdo->prepare('
        SELECT COUNT(*) as total FROM leases l
        JOIN properties p ON l.property_id = p.id
        WHERE p.landlord_id = :landlord_id AND l.status = :status AND l.deleted_at IS NULL
    ');
    $leasesStmt->execute([':landlord_id' => $userId, ':status' => 'active']);
    $leaseCount = $leasesStmt->fetch()['total'];
    
    // Pending bills
    $billsStmt = $pdo->prepare('
        SELECT COUNT(*) as total FROM bills b
        JOIN properties p ON b.property_id = p.id
        WHERE p.landlord_id = :landlord_id AND b.status IN (:unpaid, :overdue) AND b.deleted_at IS NULL
    ');
    $billsStmt->execute([
        ':landlord_id' => $userId,
        ':unpaid' => 'unpaid',
        ':overdue' => 'overdue'
    ]);
    $pendingBills = $billsStmt->fetch()['total'];
    
    // Total outstanding balance
    $balanceStmt = $pdo->prepare('
        SELECT SUM(remaining_balance) as total FROM bills b
        JOIN properties p ON b.property_id = p.id
        WHERE p.landlord_id = :landlord_id AND b.status IN (:unpaid, :overdue, :partially_paid)
    ');
    $balanceStmt->execute([
        ':landlord_id' => $userId,
        ':unpaid' => 'unpaid',
        ':overdue' => 'overdue',
        ':partially_paid' => 'partially_paid'
    ]);
    $balanceResult = $balanceStmt->fetch();
    $outstandingBalance = $balanceResult['total'] ?? 0;
    
    // Maintenance requests (pending)
    $maintenanceStmt = $pdo->prepare('
        SELECT COUNT(*) as total FROM maintenance_requests mr
        JOIN properties p ON mr.property_id = p.id
        WHERE p.landlord_id = :landlord_id AND mr.status = :status
    ');
    $maintenanceStmt->execute([':landlord_id' => $userId, ':status' => 'pending']);
    $maintenanceCount = $maintenanceStmt->fetch()['total'];
    
    // Recent properties
    $recentPropertiesStmt = $pdo->prepare('
        SELECT id, name, address, city, total_units 
        FROM properties
        WHERE landlord_id = :landlord_id AND deleted_at IS NULL
        ORDER BY created_at DESC
        LIMIT 5
    ');
    $recentPropertiesStmt->execute([':landlord_id' => $userId]);
    $recentProperties = $recentPropertiesStmt->fetchAll();
    
    // Recent payments
    $recentPaymentsStmt = $pdo->prepare('
        SELECT p.id, p.payment_amount, p.payment_date, p.status, u.first_name, u.last_name, pr.name
        FROM payments p
        JOIN users u ON p.tenant_id = u.id
        JOIN properties pr ON p.bill_id IN (SELECT id FROM bills WHERE property_id = pr.id)
        WHERE pr.landlord_id = :landlord_id
        ORDER BY p.payment_date DESC
        LIMIT 5
    ');
    $recentPaymentsStmt->execute([':landlord_id' => $userId]);
    $recentPayments = $recentPaymentsStmt->fetchAll();
    
} catch (Exception $e) {
    error_log('Dashboard error: ' . $e->getMessage());
    $propertyCount = 0;
    $unitCount = 0;
    $occupiedCount = 0;
    $leaseCount = 0;
    $pendingBills = 0;
    $outstandingBalance = 0;
    $maintenanceCount = 0;
    $recentProperties = [];
    $recentPayments = [];
}

$page_title = 'Dashboard - Landlord | UpaKo';
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<div class="container-fluid mt-4 mb-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-1">
                <i class="fas fa-chart-line"></i> Dashboard
            </h1>
            <p class="text-muted">Welcome back, <?php echo htmlspecialchars($currentUser['first_name']); ?>!</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?php echo SITE_URL; ?>/landlord/properties.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Property
            </a>
        </div>
    </div>
    
    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Properties</p>
                            <h3 class="mb-0"><?php echo $propertyCount; ?></h3>
                        </div>
                        <div class="text-primary" style="font-size: 2rem;">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Units</p>
                            <h3 class="mb-0"><?php echo $unitCount; ?></h3>
                            <small class="text-success"><?php echo $occupiedCount; ?> occupied</small>
                        </div>
                        <div class="text-success" style="font-size: 2rem;">
                            <i class="fas fa-door-open"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Active Leases</p>
                            <h3 class="mb-0"><?php echo $leaseCount; ?></h3>
                        </div>
                        <div class="text-info" style="font-size: 2rem;">
                            <i class="fas fa-file-contract"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Pending Bills</p>
                            <h3 class="mb-0 text-danger"><?php echo $pendingBills; ?></h3>
                            <small class="text-danger">₱<?php echo number_format($outstandingBalance, 2); ?></small>
                        </div>
                        <div class="text-danger" style="font-size: 2rem;">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alert for Pending Items -->
    <?php if ($maintenanceCount > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-tools me-2"></i>
            You have <strong><?php echo $maintenanceCount; ?></strong> pending maintenance request(s).
            <a href="<?php echo SITE_URL; ?>/landlord/maintenance.php" class="alert-link">View Now</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Recent Properties -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-building me-2"></i> Recent Properties
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentProperties)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentProperties as $property): ?>
                                <a href="<?php echo SITE_URL; ?>/landlord/properties.php?id=<?php echo $property['id']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($property['name']); ?></h6>
                                            <p class="small text-muted mb-0">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?php echo htmlspecialchars($property['city']); ?>
                                            </p>
                                        </div>
                                        <span class="badge bg-primary"><?php echo $property['total_units']; ?> units</span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <p><i class="fas fa-inbox" style="font-size: 2rem;"></i></p>
                            <p>No properties yet</p>
                            <a href="<?php echo SITE_URL; ?>/landlord/properties.php" class="btn btn-sm btn-primary">
                                Add Your First Property
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Payments -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-credit-card me-2"></i> Recent Payments
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentPayments)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentPayments as $payment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?>
                                            </h6>
                                            <p class="small text-muted mb-0">
                                                <?php echo date('M d, Y', strtotime($payment['payment_date'])); ?>
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <p class="mb-0 fw-bold">₱<?php echo number_format($payment['payment_amount'], 2); ?></p>
                                            <span class="badge bg-<?php echo $payment['status'] == 'approved' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($payment['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <p><i class="fas fa-inbox" style="font-size: 2rem;"></i></p>
                            <p>No payments yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-lightning-bolt me-2"></i> Quick Actions
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo SITE_URL; ?>/landlord/properties.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-building"></i> Manage Properties
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo SITE_URL; ?>/landlord/bills.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-receipt"></i> View Bills
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo SITE_URL; ?>/landlord/tenants.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-users"></i> View Tenants
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo SITE_URL; ?>/landlord/reports.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
