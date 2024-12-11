<?php

// Include the PHPMailer autoloader
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Define constants(company  mail address)
define("RECIPIENT_NAME", "shakithyan");
define("RECIPIENT_EMAIL", "jeyakumarshakithyan03@gmail.com");

// Handle favicon request (to avoid unnecessary error logs)
if ($_SERVER['REQUEST_URI'] === '/favicon.ico') {
    header('Content-Type: image/x-icon');
    readfile('assets/images/favicons/favicon.ico');
    exit();
}

// Read the form values
$name = isset($_POST['name']) ? preg_replace("/[^\.\-\' a-zA-Z0-9]/", "", $_POST['name']) : "";
$senderEmail = isset($_POST['email']) ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST['email']) : "";
$phone = isset($_POST['phone']) ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST['phone']) : "";
$services = isset($_POST['services']) ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST['services']) : "";
$subject = isset($_POST['subject']) ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST['subject']) : "";
$address = isset($_POST['address']) ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST['address']) : "";
$website = isset($_POST['website']) ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST['website']) : "";
$message = isset($_POST['message']) ? preg_replace("/(From:|To:|BCC:|CC:|Subject:|Content-Type:)/", "", $_POST['message']) : "";

// Construct email subject and body
$mail_subject = 'A contact request sent by ' . $name;
$body = "Name: $name\r\n";
$body .= "Email: $senderEmail\r\n";
if ($phone) $body .= "Phone: $phone\r\n";
if ($services) $body .= "Services: $services\r\n";
if ($subject) $body .= "Subject: $subject\r\n";
if ($address) $body .= "Address: $address\r\n";
if ($website) $body .= "Website: $website\r\n";
$body .= "Message:\r\n$message";

// Send email if required fields are provided
if ($name && $senderEmail && $message) {
    try {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set the SMTP server (use the correct one for your provider)
        $mail->SMTPAuth = true;
        $mail->Username = 'jeyakumarshakithyan03@gmail.com'; // Your email
        $mail->Password = 'your app password';   // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender (shop) email
        $mail->setFrom('jeyakumarshakithyan03@gmail.com', 'Kunam Dry Fish Shop');
        
        // Recipient (admin) email (optional, can still send to a specific admin if needed)
        $mail->addAddress(RECIPIENT_EMAIL, RECIPIENT_NAME); // Optional - send to admin as well

        // User email (to send confirmation to the user)
        $mail->addReplyTo($senderEmail, $name);

        // Subject and body for the admin
        $mail->Subject = $mail_subject;
        $mail->Body    = $body;

        // Send email to the admin (optional)
        $mail->send();

        // Now send the confirmation to the user
        $mail->clearAddresses();
        $mail->addAddress($senderEmail, $name); // Send confirmation to the user
        $mail->Subject = 'Thank you for contacting us!';
        $mail->Body = "Dear $name,\n\nThank you for reaching out to us. We have received your message and will get back to you shortly.\n\nBest regards,\nKunam Dry Fish";
        
        // Send the email to the user
        $mail->send();

        echo "<div class='inner success'><p class='success'>Thanks for contacting us. We will contact you ASAP!</p></div>";
    } catch (Exception $e) {
        echo "<div class='inner error'><p class='error'>Something went wrong while sending the email. Please try again.</p></div>";
    }
} else {
    echo "<div class='inner error'><p class='error'>Please fill in all the required fields.</p></div>";
}
?>
