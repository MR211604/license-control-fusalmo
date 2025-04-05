<?php


class UserValidation {

  public static function validateData($name, $email, $password, $rol) {
    $errors = [];

    if(empty($name) || strlen($name) < 3) {
      $errors[] = "El nombre es obligatorio y debe tener al menos 3 caracteres";
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "El correo electrónico es inválido";
    }

    if(empty($password)) {
      $errors[] = "La contraseña es obligatoria y debe tener al menos 8 caracteres";
    }

    if(!empty($errors)) {
      throw new Exception(implode(" | ", $errors));
    }

  }

}