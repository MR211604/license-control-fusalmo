<?php


/**
 * Función para suspender la fecha de una licencia
 * @param int $licenseId ID de la licencia a suspender
 */
function sendEmail($licenseId)
{
  global $api_url;

  //*Primero obtenemos la licencia
  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . "/license/{$licenseId}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
      $licenseData = json_decode($response, true);
      if ($licenseData && isset($licenseData['licencia'])) {
        $license = $licenseData['licencia'];
        sendEmailFunction($license['correo'], "INVITACION A FUSALMO VISUAL PARTY", "TE HACEMOS LA INVITACION A FUSALMO VISUAL PARTY");
      } else {
        throw new Exception("Error al obtener datos de la licencia.");
      }
    } else {
      throw new Exception("Error al obtener la licencia. Código: {$httpCode}");
    }
  } catch (Exception $e) {
    global $error_message;
    $error_message = "Error en la conexión: " . $e->getMessage();
  }
}

function sendEmailFunction($email, $subject, $messageBody)
{
  global $error_message;
  global $api_url;

  //Llamaremos a la API de envio de correos
  $data = [
    'email' => $email,
    'subject' => $subject,
    'messageBody' => $messageBody
  ];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $api_url . '/email/send');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
  ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($httpCode == 200) {
    $responseData = json_decode($response, true);
    if ($responseData && isset($responseData['ok']) && $responseData['ok'] === true) {
      header("Location: index.php?page=home&sendEmail=true");
      exit();
    } else {
      $error_message = "Error al enviar el correo.";
    }
  } else {
    $responseData = json_decode($response, true);
    if ($responseData && isset($responseData['error'])) {
      $error_message = $responseData['error'];
    } else {
      $error_message = "Error desconocido al enviar el correo.";
    }
  }
}
