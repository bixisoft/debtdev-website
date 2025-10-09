<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 in production

// Include PHPMailer autoloader
require 'vendor/autoload.php'; // If using Composer
// Or manually include files if not using Composer:
// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form data validation and collection
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $leadVolume = isset($_POST['leadVolume']) ? filter_var($_POST['leadVolume'], FILTER_SANITIZE_STRING) : 'Not specified';
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    
    // Basic validation
    if (empty($name) || empty($email) || empty($phone)) {
        http_response_code(400);
        echo json_encode(array("message" => "Please fill in all required fields"));
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid email address"));
        exit;
    }

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'mnaeem02825@gmail.com'; // Your Gmail address
        $mail->Password = 'CHN@eem$38937'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('noreply@debtdev.com', 'DebtDev Website');
        $mail->addAddress('mnaeem02825@gmail.com', 'DebtDev Team'); // Primary recipient
        $mail->addReplyTo($email, $name); // Allow replying to the submitter
        
        // Optional: Add CC or BCC
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission - DebtDev";
        
        // HTML email body
        $email_body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0B1B2B; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .field { margin-bottom: 15px; padding: 10px; background: white; border-left: 4px solid #28a99c; }
                .field-label { font-weight: bold; color: #0B1B2B; }
                .footer { background: #eee; padding: 15px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>New Contact Form Submission</h2>
                    <p>DebtDev Website</p>
                </div>
                <div class='content'>
                    <div class='field'>
                        <span class='field-label'>Name:</span> $name
                    </div>
                    <div class='field'>
                        <span class='field-label'>Company:</span> $company
                    </div>
                    <div class='field'>
                        <span class='field-label'>Email:</span> <a href='mailto:$email'>$email</a>
                    </div>
                    <div class='field'>
                        <span class='field-label'>Phone:</span> <a href='tel:$phone'>$phone</a>
                    </div>
                    <div class='field'>
                        <span class='field-label'>Monthly Lead Volume:</span> $leadVolume
                    </div>
                    <div class='field'>
                        <span class='field-label'>Message:</span><br>
                        " . nl2br($message) . "
                    </div>
                </div>
                <div class='footer'>
                    <p>This email was sent from the DebtDev contact form on " . date('F j, Y \a\t g:i A') . "</p>
                    <p>DebtDev - Fintech Engineering for Debt & Credit</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Plain text version for non-HTML email clients
        $text_body = "
        New Contact Form Submission - DebtDev
        
        Name: $name
        Company: $company
        Email: $email
        Phone: $phone
        Monthly Lead Volume: $leadVolume
        
        Message:
        $message
        
        Submitted on: " . date('Y-m-d H:i:s') . "
        ";
        
        $mail->Body = $email_body;
        $mail->AltBody = $text_body;

        // Send email
        if ($mail->send()) {
            http_response_code(200);
            echo json_encode(array("message" => "Email sent successfully"));
        } else {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Message could not be sent. Mailer Error: " . $e->getMessage()));
    }
} else {
    http_response_code(403);
    echo json_encode(array("message" => "There was a problem with your submission, please try again."));
}
?>