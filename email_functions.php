<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// If you don't have PHPMailer installed via Composer, you can download it manually
// require_once 'vendor/autoload.php'; // If using Composer
// OR download PHPMailer files manually and include them:
// require_once 'PHPMailer/src/Exception.php';
// require_once 'PHPMailer/src/PHPMailer.php';
// require_once 'PHPMailer/src/SMTP.php';

function sendEmail($to, $toName, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to, $toName);
        $mail->addReplyTo(FROM_EMAIL, FROM_NAME);

        // Content
        $mail->isHTML(false); // Set to true if you want HTML emails
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

// Alternative function using PHP's mail() function (simpler but less reliable)
function sendEmailSimple($to, $toName, $subject, $body) {
    $headers = [
        'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>',
        'Reply-To: ' . FROM_EMAIL,
        'X-Mailer: PHP/' . phpversion(),
        'Content-Type: text/plain; charset=UTF-8'
    ];

    $headerString = implode("\r\n", $headers);
    
    return mail($to, $subject, $body, $headerString);
}

// Function to get email templates
function getEmailTemplate($status, $candidateName, $position) {
    $templates = [
        'selected' => [
            'subject' => "Congratulations! You've been selected for $position",
            'body' => "Dear $candidateName,\n\nWe are pleased to inform you that you have been selected for the position of $position.\n\nPlease reply to this email to confirm your acceptance.\n\nBest regards,\nHR Team"
        ],
        'rejected' => [
            'subject' => "Application Status Update for $position",
            'body' => "Dear $candidateName,\n\nThank you for applying for the position of $position.\n\nWe regret to inform you that we have decided to move forward with other candidates.\n\nBest regards,\nHR Team"
        ]
    ];

    return $templates[$status] ?? null;
}
?>
