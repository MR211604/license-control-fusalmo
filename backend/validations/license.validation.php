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

    $plataforma = $data['plataforma'];
    $correo = $data['correo'];
    $contrasena = $data['contrasena'];
    $fechaDeCompra = $data['fechaDeCompra'];
    $fechaDeVencimiento = $data['fechaDeVencimiento'];

    // Los campos opcionales pueden ser null
    $fechaDeRenovacion = isset($data['fechaDeRenovacion']) ? $data['fechaDeRenovacion'] : null;
    $fechaDeSuspension = isset($data['fechaDeSuspension']) ? $data['fechaDeSuspension'] : null;

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
      throw new Exception('El correo no es valido');
    }

    if ($fechaDeCompra > $fechaDeVencimiento) {
      throw new Exception('La fecha de compra no puede ser mayor a la fecha de vencimiento');
    }

    // Validación adicional para fechaDeRenovacion si está presente
    if ($fechaDeRenovacion !== null && $fechaDeRenovacion < $fechaDeCompra) {
      throw new Exception('La fecha de renovación no puede ser menor a la fecha de compra');
    }
  }
}
