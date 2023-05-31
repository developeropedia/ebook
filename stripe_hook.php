<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "config.php";

function generateEbook() {
    // Assuming the ebooks are stored in an uploads directory
    $uploadsDirectory = '';

    // Construct the filename of the ebook PDF based on the payment ID
    $ebookFilename = EBOOK_FILENAME;

    // Construct the full path to the ebook PDF file
    $ebookFilePath = $uploadsDirectory . $ebookFilename;

    // Check if the ebook PDF file exists
    if (file_exists($ebookFilePath)) {
        return $ebookFilePath;
    } else {
        // Handle the case when the ebook PDF file is not found
        // Add your logic here to handle the situation
        // For example, you can generate an error message or throw an exception
        // You can also generate or fetch the ebook from a different source if needed
        // In this example, we'll return null to indicate the ebook file was not found
        return null;
    }
}

//Load Composer's autoloader
require 'vendor/autoload.php';

function sendEmailWithAttachment($email, $ebookFile) {
    // Instantiating PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom(SMTP_USERNAME, SENDER);
        $mail->addAddress($email);

        $body = file_get_contents("email_template.html");

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Ebook Delivery';
        $mail->Body    = $body;
        $mail->AltBody = 'Thank you for your purchase. Please find the ebook attached.';

        // Attachment
        $mail->addAttachment($ebookFile, EBOOK_FILENAME);

        // Send the email
        $mail->send();

        // Return true if the email was sent successfully
        return true;
    } catch (Exception $e) {
        // Return the error message if the email could not be sent
        return 'Email could not be sent. Error: ' . $mail->ErrorInfo;
    }
}

$stripe = new \Stripe\StripeClient(STRIPE_KEY);

$endpoint_secret = ENDPOINT_SECRET;

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sig_header,
        $endpoint_secret
    );
} catch (\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}


// Handle the event
switch ($event->type) {
    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object;
        $email = $paymentIntent->charges->data[0]->billing_details->email;

        $ebookFile = generateEbook();

        // Send the ebook as an email attachment to the customer's email
        sendEmailWithAttachment($email, $ebookFile);

        // Respond to the webhook event with a 200 status code
        http_response_code(200);
    default:
        echo 'Received unknown event type ' . $event->type;
}


?>