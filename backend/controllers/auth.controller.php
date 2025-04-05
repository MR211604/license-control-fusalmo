<?php


class AuthController
{
  private $conn;

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  public function login($username, $password)
  { 
    $stmt_check = $this->conn->prepare("SELECT id_usuario, nombre_usuario, contrasena, id_rol FROM usuario WHERE nombre_usuario = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
      $user = $result_check->fetch_assoc();

      if (password_verify($password, $user["contrasena"])) {
        session_start();
        $_SESSION["user_id"] = $user["id_usuario"];
        $_SESSION["username"] = $user["nombre_usuario"];
        $_SESSION["id_rol"] = $user["id_rol"];

        $stmt_check->close();
        return [
          "ok" => true,
          "user_id" => $user["id_usuario"],
          "username" => $user["nombre_usuario"],
          "id_rol" => $user["id_rol"],
          "message" => "Usuario autenticado correctamente"
        ];
      } else {
        $stmt_check->close();
        return [
          "ok" => false,
          "message" => "El usuario o contraseña ingresados son incorrectos"
        ];
      }
    } else {
      $stmt_check->close();
      return [
        "ok" => false,
        "message" => "No hay resultados"
      ];
    }
  }
}
