<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/node_modules/PHPMailer/src/Exception.php';
require './vendor/node_modules/PHPMailer/src/PHPMailer.php';
require './vendor/node_modules/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $message = $_POST["message"];

    // Verify reCAPTCHA
    $recaptcha_secret_key = '6LcRovApAAAAAOrJSsjpALM8IFAi3Orwszdlf3G7';
    $response = $_POST['g-recaptcha-response'];

    $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
    $recaptcha_data = [
        'secret' => $recaptcha_secret_key,
        'response' => $response,
    ];

    $recaptcha_options = [
        'http' => [
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data),
            'header' => 'Content-Type: application/x-www-form-urlencoded',
        ],
    ];

    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    $recaptcha_result_data = json_decode($recaptcha_result, true);

    if (!$recaptcha_result_data['success']) {
        // Handle reCAPTCHA verification failure
        echo json_encode(["success" => false, "error" => "reCAPTCHA verification failed"]);
        exit;
    }

    // Continue with the rest of the PHPMailer logic
    try {
        // Initialize PHPMailer
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.ionos.com'; // Specify your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@stonedcookiegaming.com'; // SMTP username
        $mail->Password   = 'F3ngK0h101$';   // SMTP password
        $mail->SMTPSecure = 'tls';              // Enable TLS encryption
        $mail->Port       = 587;                // TCP port to connect to

        // Sender info
        $mail->setFrom('noreply@stonedcookiegaming.com', 'NoReply');
        $mail->addReplyTo($email, $name);

        // Recipient (website owner)
        $mail->addAddress('crazycookie5485@gmail.com', 'Website Owner');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission';
        $mail->Body    = "<p><strong>Name:</strong> $name</p><p><strong>Email:</strong> $email</p><p><strong>Phone:</strong> $phone</p><p><strong>Message:</strong><br>$message</p>";

        // Send email
        $mail->send();

        // Return success response to the frontend
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        // Return error response to the frontend
        echo json_encode(["success" => false, "error" => $mail->ErrorInfo]);
    }
} else {
    // Return error response for non-POST requests
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}
?>