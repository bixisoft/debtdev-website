<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form data collect karein
    $name = htmlspecialchars($_POST['name']);
    $company = htmlspecialchars($_POST['company']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $leadVolume = htmlspecialchars($_POST['leadVolume']);
    $message = htmlspecialchars($_POST['message']);
    
    // Email settings
    $to = "mnaeem02825@gmail.com";
    $subject = "New Contact Form Submission - DebtDev";
    
    // Email body
    $email_body = "
    New Contact Form Submission from DebtDev Website:
    
    Name: $name
    Company: $company
    Email: $email
    Phone: $phone
    Lead Volume: $leadVolume
    Message: $message
    
    Submitted on: " . date('Y-m-d H:i:s') . "
    ";
    
    // Email headers
    $headers = "From: noreply@debtdev.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Send email
    if (mail($to, $subject, $email_body, $headers)) {
        http_response_code(200);
        echo json_encode(array("message" => "Email sent successfully"));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Failed to send email"));
    }
} else {
    http_response_code(403);
    echo json_encode(array("message" => "There was a problem with your submission, please try again."));
}
?>