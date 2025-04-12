<?php


class AuthValidation
{

  public static function validateData($data)
  {

    //Validar que data no este vacio
    if (empty($data)) {
      throw new Exception('Todos los campos son obligatorios');
    }

    $username = $data['username'];
    $password = $data['password'];

    if (empty($username) || empty($password)) {
      throw new Exception('Todos los campos son obligatorios');
    }
  }
}
