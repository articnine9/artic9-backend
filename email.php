<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['name'], $data['email'], $data['phone'], $data['subject'], $data['message'])) {
    $name = sanitize_input($data['name']);
    $email = sanitize_input($data['email']);
    $phone = sanitize_input($data['phone']);
    $subject = sanitize_input($data['subject']);
    $message = sanitize_input($data['message']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit();
    }

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                       // Set mailer to use SMTP
        $mail->Host = 'smtp.example.com';                       // Set the SMTP server (replace with your SMTP server)
        $mail->SMTPAuth = true;                                 // Enable SMTP authentication
        $mail->Username = 'articnine9@gmail.com';             // SMTP username (replace with your email)
        $mail->Password = 'dbbpzqehmihgcusl';                // SMTP password (replace with your email password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Enable TLS encryption
        $mail->Port = 587;                                     // TCP port to connect to (usually 587 for TLS)

        //Recipients
        $mail->setFrom($email, $name);                          // Sender's email address and name
        $mail->addAddress('articnine9@gmail.com');             // Replace with your email address
        $mail->addReplyTo($email, $name);                       // Add reply-to email (same as sender)

        // Content
        $mail->isHTML(true);                                    // Set email format to HTML
        $mail->Subject = "New Contact Form Submission: $subject";
        $mail->Body    = "<h3>Contact Form Submission</h3>
                         <p><strong>Name:</strong> $name</p>
                         <p><strong>Email:</strong> $email</p>
                         <p><strong>Phone:</strong> $phone</p>
                         <p><strong>Subject:</strong> $subject</p>
                         <p><strong>Message:</strong> $message</p>";

        // Send email
        if ($mail->send()) {
            echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'There was an error sending your message. Please try again later.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
}

// Function to sanitize the input
function sanitize_input($input)
{
    return htmlspecialchars(stripslashes(trim($input)));
}
