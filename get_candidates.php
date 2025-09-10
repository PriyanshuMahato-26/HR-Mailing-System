
<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $stmt = $pdo->query("
        SELECT c.*, 
               (SELECT COUNT(*) FROM email_logs el WHERE el.candidate_id = c.id AND el.status = 'sent') as emails_sent
        FROM candidates c 
        ORDER BY c.created_at DESC
    ");
    
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $candidates
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to retrieve candidates'
    ]);
}
?>

<?php