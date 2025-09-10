
<?php

require_once 'config.php';
require_once 'email_functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Validate input
    $required_fields = ['candidateName', 'candidateEmail', 'position', 'status', 'subject', 'body'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }

    $candidateName = trim($_POST['candidateName']);
    $candidateEmail = trim($_POST['candidateEmail']);
    $position = trim($_POST['position']);
    $status = $_POST['status'];
    $subject = $_POST['subject'];
    $body = $_POST['body'];

    // Validate email
    if (!filter_var($candidateEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address");
    }

    // Validate status
    if (!in_array($status, ['selected', 'rejected'])) {
        throw new Exception("Invalid status");
    }

    // Check if candidate already exists
    $stmt = $pdo->prepare("SELECT id FROM candidates WHERE email = ? AND position = ?");
    $stmt->execute([$candidateEmail, $position]);
    $existingCandidate = $stmt->fetch();

    if ($existingCandidate) {
        // Update existing candidate
        $stmt = $pdo->prepare("UPDATE candidates SET name = ?, status = ?, email_sent = FALSE, updated_at = NOW() WHERE email = ? AND position = ?");
        $stmt->execute([$candidateName, $status, $candidateEmail, $position]);
        $candidateId = $existingCandidate['id'];
    } else {
        // Insert new candidate
        $stmt = $pdo->prepare("INSERT INTO candidates (name, email, position, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$candidateName, $candidateEmail, $position, $status]);
        $candidateId = $pdo->lastInsertId();
    }

    // Send email
    $emailSent = sendEmail($candidateEmail, $candidateName, $subject, $body);

    if ($emailSent) {
        // Update candidate as email sent
        $stmt = $pdo->prepare("UPDATE candidates SET email_sent = TRUE WHERE id = ?");
        $stmt->execute([$candidateId]);

        // Log email
        $stmt = $pdo->prepare("INSERT INTO email_logs (candidate_id, recipient_email, subject, body, status) VALUES (?, ?, ?, ?, 'sent')");
        $stmt->execute([$candidateId, $candidateEmail, $subject, $body]);

        echo json_encode([
            'success' => true,
            'message' => "Email sent successfully to $candidateName"
        ]);
    } else {
        // Log failed email
        $stmt = $pdo->prepare("INSERT INTO email_logs (candidate_id, recipient_email, subject, body, status) VALUES (?, ?, ?, ?, 'failed')");
        $stmt->execute([$candidateId, $candidateEmail, $subject, $body]);

        throw new Exception("Failed to send email");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
