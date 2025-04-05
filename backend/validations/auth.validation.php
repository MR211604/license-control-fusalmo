<?php


class AuthValidation {

  public function validateLogin($username, $password) {
    if (empty($username) || empty($password)) {
      return [
        "ok" => false,
        "message" => "El usuario y la contraseña son obligatorios"
      ];
    }

    return [
      "ok" => true
    ];
  }

}


?>