<?php

class LicenseValidation
{
  public static function validateData($data)
  {
    // Verificar que los campos obligatorios existan
    $requiredFields = ['plataforma', 'correo', 'contrasena', 'fechaDeCompra', 'fechaDeVencimiento'];
    foreach ($requiredFields as $field) {
      if (!isset($data[$field]) || empty($data[$field])) {
        throw new Exception('El campo ' . $field . ' es obligatorio');
      }
    }
    
    $correo = $data['correo'];
    $fechaDeCompra = $data['fechaDeCompra'];
    $fechaDeVencimiento = $data['fechaDeVencimiento'];

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
      throw new Exception('El correo no es valido');
    }

    if ($fechaDeCompra > $fechaDeVencimiento) {
      throw new Exception('La fecha de compra no puede ser mayor a la fecha de vencimiento');
    }

  }
}
