<?php

/**
 * Función para renovar la fecha de una licencia
 * @param int $licenseId ID de la licencia a renovar
 */
function renovateLicense($licenseId)
{
  global $error_message;

  // Obtener primero la licencia actual para mantener todos sus datos
  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/license/renovate/{$licenseId}");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);

    $updateResponse = curl_exec($ch);
    $updateHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($updateHttpCode == 200) {
      // Pasar el mensaje de éxito como parámetro en la URL
      header("Location: index.php?page=home&editSucess=true");
      exit();
    } else {
      $updateData = json_decode($updateResponse, true);
      $error_message = isset($updateData['error']) ? $updateData['error'] : "Error al renovar la licencia. Código: {$updateHttpCode}";
    }
  } catch (Exception $e) {
    $error_message = "Error en la conexión: " . $e->getMessage();
  }
}

/**
 * Función para suspender la fecha de una licencia
 * @param int $licenseId ID de la licencia a suspender
 */
function suspendLicense($licenseId)
{
  global $error_message;

  // Obtener primero la licencia actual para mantener todos sus datos
  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/license/suspend/{$licenseId}");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);

    $updateResponse = curl_exec($ch);
    $updateHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($updateHttpCode == 200) {
      // Pasar el mensaje de éxito como parámetro en la URL
      header("Location: index.php?page=home&editSucess=true");
      exit();
    } else {
      $updateData = json_decode($updateResponse, true);
      $error_message = isset($updateData['error']) ? $updateData['error'] : "Error al suspender la licencia. Código: {$updateHttpCode}";
    }
  } catch (Exception $e) {
    $error_message = "Error en la conexión: " . $e->getMessage();
  }
}
