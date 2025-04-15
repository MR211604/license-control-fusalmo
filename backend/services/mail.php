<?php
// Cargamos las variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ServerRequestInterface;

class SendEmail
{
  public function __construct() {
  }

  public function sendEmail(ServerRequestInterface $request)
  {
    // Si no se proporciona una solicitud, devolver un error
    if ($request === null) {
      return JSONResponse::response(400, [
        "ok"=> false,
        "message" => "No se recibió una solicitud válida."
      ]);
    }

    //Body: email, name, subject, messageBody, altBody
    $body = $request->getBody()->getContents();
    $data = json_decode($body, true);

    if(empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      return JSONResponse::response(400, [
        "ok" => false,
        "message" => "No se recibió una direccion de correo valido."
      ]);
    }

    if(empty($data['name'])) {
      return JSONResponse::response(400, [
        "ok" => false,
        "message" => "No se recibió el nombre del correo."
      ]);
    }

    if(empty($data['subject'])) {
      return JSONResponse::response(400, [
        "ok" => false,
        "message" => "No se recibió un asunto para el correo."
      ]);
    }

    if(empty($data['messageBody'])) {
      return JSONResponse::response(400, [
        "ok" => false,
        "message" => "No se recibió un cuerpo para el correo."
      ]);
    }
    
    $mail = new PHPMailer(true);
    try {
      //Server settings
      //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
      $mail->isSMTP();                                            //Send using SMTP
      $mail->Host       = $_ENV['MAIL_HOST'];                     //Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
      $mail->Username   = $_ENV['MAIL_USERNAME'];                 //SMTP username
      $mail->Password   = $_ENV['MAIL_PASSWORD'];                 //SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
      $mail->Port       = $_ENV['MAIL_PORT'];                     //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

      //Recipients
      $mail->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_FROM_NAME']); //El nombre puede ser cualquiera en realidad
      $mail->addAddress($data['email'], $data['name']);               //Add a recipient
      //$mail->addCC('cc@example.com');
      //$mail->addBCC('bcc@example.com');

      //Attachments
      //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
      //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

      //Content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = $data['subject'];
      $mail->Body    = $data['messageBody'];
      $mail->AltBody = !empty($data['altBody']) ? $data['altBody'] : '';

      $mail->send();
      return JSONResponse::response(
        200, [
          "ok" => true,
          "message" => "El mensaje se ha enviado correctamente."
        ]
      );
    } catch (Exception $e) {
      return JSONResponse::response(
        500, [
          "ok" => false,
          "message" => "El mensaje no se pudo enviar. Error: {$mail->ErrorInfo}"
        ]
      ); 
    }
  }
}
