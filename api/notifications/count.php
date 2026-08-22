<?php
/**
 * UpaKo - Notification Count API
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$currentUser = getCurrentUser();
$userId = $currentUser['id'];

try {
    global $pdo;
    
    // Get unread notifications count
    $stmt = $pdo->prepare('
        SELECT COUNT(*) as count FROM notifications 
        WHERE user_id = :user_id AND read_at IS NULL
    ');
    $stmt->execute([':user_id' => $userId]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'count' => $result['count'] ?? 0
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}
?>
