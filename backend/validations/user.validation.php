<?php


class UserValidation
{

  public static function validateData($data)
  {
    //$name, $email, $password, $rol

    //Validando que los datos no esten vacios
    if (empty($data)) {
      throw new Exception('Todos los campos son obligatorios');
    }

    $name = $data['name'];
    $email = $data['email'];
    $password = $data['password'];
    $rol = $data['rol'];

    if (empty($name) || empty($email) || empty($password) || empty($rol)) {
      throw new Exception('Todos los campos son obligatorios');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new Exception('El correo no es valido');
    }
  }

  public static function validateConfirmPassword($password, $confirmPassword)
  {
    
    if ($password !== $confirmPassword) {
      throw new Exception('Las contraseñas no coinciden');
    }
  }
}
