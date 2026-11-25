<?php
   // Composer autoload
   require_once __DIR__ . '/vendor/autoload.php';

   // Classes from PHPMailer
   use PHPMailer\PHPMailer\PHPMailer;
   use PHPMailer\PHPMailer\Exception;

   // 1. Allow origin
   header("Access-Control-Allow-Origin: https://rodrigopazz.com");

   // 2. Allow methods (POST for data, OPTIONS for browser)
   header("Access-Control-Allow-Methods: POST, OPTIONS");

   // 3. Allow to send Content-Type header (for JSON)
   header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

   // 4. Response successfully to "Preflight" request (OPTIONS from browser)
   if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      http_response_code(200);
      exit();
   }

   // 5. Validate it's a POST request before processing
   if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(["error" => "Method not permitted. Use POST."]);
      exit();
   }

   // Read JSON
   $input = file_get_contents("php://input");
   $data = json_decode($input, true);
   
   // (Opcional) Responder al cliente para confirmar la recepción
   //header("Content-Type: application/json");
   //echo json_encode(["status" => "success", "received" => $data]);

   // Get data
   $name = $data["name"];
   $email = $data["email"];
   $subject = $data["subject"];
   $message = $data["message"];

   try {
      // =========================
      // HOSTING CONFIGURATION 
      // =========================

      
      $dotenv = Dotenv\Dotenv::createImmutable('/home/u279829464/domains/rodrigopazz.com');
      $dotenv->load();
      
      $smtpHost     = $_ENV['SMTP_HOST'];       // Hosting's SMPT Server
      $smtpUsername = $_ENV['SMTP_USERNAME'];   // Hosting email for notifications
      $smtpPassword = $_ENV['SMTP_PASSWORD'];   // Email password
      $smtpPort     = $_ENV['SMTP_PORT'];       // Port SSL
      $smtpCharSet  = $_ENV['SMTP_CHARSET'];    // Charset


      /******* 1. EMAIL FOR REALTOR *******/
      $mail = new PHPMailer(true);
      $mail->isSMTP();                                            
      $mail->Host       = $smtpHost;
      $mail->SMTPAuth   = true;
      $mail->Username   = $smtpUsername;
      $mail->Password   = $smtpPassword;
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port       = $smtpPort;
      $mail->CharSet    = $smtpCharSet;

      $mail->setFrom($smtpUsername, 'Rodrigo Pazz');
      $mail->addAddress("rodrigo1305paz@gmail.com", "Rodrigo Paz");

      $mail->isHTML(true);                                  
      $mail->Subject = "New contact from Landing Page";
      $mail->Body    = "
         <h2>Hello Rodrigo Paz</h2>
         <p>You have received a new contact from your landing page https://rodrigopazz.com <br> This person is looking for <b>'{$subject}'.</b> Here are the details:</p>
         <ul>
            <li>Name:    {$name}</li>
            <li>Email:   {$email}</li>
            <li>Subject: {$subject}</li>
            <li>Message: {$message}</li>
         </ul>
         <p>Get in touch with this person at {$email} to make a follow-up of the requirements.</p>
      ";
      $mail->send();

   }catch(Exception $e) {
      // Catch errors from PHPMailer or exception personalized
      error_log("Error in proccess: " . $e->getMessage());
      http_response_code(500);
      echo json_encode([
         "status" => "error", 
         "message" => "There was a problem processing your request.",
         "debug" => $e->getMessage() // Quitar en producción
      ]);
   }
?>