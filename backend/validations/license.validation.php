<?php

class LicenseValidation
{
  public static function validateData($data)
  {
    
    //Validando que los datos no esten vacios
    if (empty($data)) {
      throw new Exception('Todos los campos son obligatorios');
    }

    $plataforma = $data['plataforma'];
    $correo = $data['correo'];
    $contrasena = $data['contrasena'];
    $fechaDeCompra = $data['fechaDeCompra'];
    $fechaDeSuspension = $data['fechaDeSuspension'];
    $fechaDeRenovacion = $data['fechaDeRenovacion'];
    $fechaDeVencimiento = $data['fechaDeVencimiento'];


    if (empty($plataforma) || empty($correo) || empty($contrasena) || empty($fechaDeCompra) || empty($fechaDeSuspension) || empty($fechaDeRenovacion) || empty($fechaDeVencimiento)) {
      throw new Exception ('Todos los campos son obligatorios');
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
      throw new Exception ('El correo no es valido');
    }

    if ($fechaDeCompra > $fechaDeVencimiento) {
      throw new Exception ('La fecha de compra no puede ser mayor a la fecha de vencimiento');
    }

    if ($fechaDeRenovacion < $fechaDeCompra) {
      throw new Exception ('La fecha de renovacion no puede ser menor a la fecha de compra');
    }
  }
}
