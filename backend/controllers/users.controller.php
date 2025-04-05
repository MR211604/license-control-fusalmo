<?php

class usersController
{

  private $conn;

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  public function getUsers()
  {

    $sql = "SELECT * FROM usuario";
    $result = $this->conn->query($sql);

    if (!$result) {
      return ["ok" => false, "message" => "Error en la consulta: " . $this->conn->error];
    }

    return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
  }

  public function userExists($email, $username)
  {
    $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE correo = ? OR nombre_usuario = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
  }

  //Por defecto, un usuario se crea con roles de usuario.
  public function createUser($username, $email, $password, $role = 2)
  {
    try {

      UserValidation::validateData($username, $email, $password, $role);

      if ($this->userExists($email, $username)) {
        return [
          "ok" => false,
          "message" => "El usuario ingresado ya existe"
        ];
      }

      $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

      $sql = "INSERT INTO usuario (nombre_usuario, correo, contrasena, id_rol) VALUES (?, ?, ?, ?)";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
      $stmt->execute();
      return [
        "ok" => true,
        "message" => "Usuario creado correctamente"
      ];
    } catch (Exception $e) {
      return [
        "ok" => false,
        "message" => $e->getMessage()
      ];
    }
  }

  public function editUser($id, $username, $email, $password, $role)
  {

    try {

      UserValidation::validateData($username, $email, $password, $role);

      if ($this->userExists($email, $username)) {
        return [
          "ok" => false,
          "message" => "El usuario ingresado ya existe"
        ];
      }

      $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

      $sql = "UPDATE usuario SET nombre_usuario = '$username', correo = '$email', contrasena = '$password', id_rol = '$role' WHERE id_usuario = $id";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("ssssi", $username, $email, $hashedPassword, $role, $id);

      $stmt->execute();

      return [
        "ok" => true,
        "message" => "Usuario editado correctamente"
      ];
    } catch (Exception $e) {
      return [
        "ok" => false,
        "message" => $e->getMessage()
      ];
    }
  }
}
