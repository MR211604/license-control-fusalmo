<?php

use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\QueryResult;

require __DIR__ . '/../validations/auth.validation.php';

class AuthController
{
  private $conn;

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  public function isUserDisabled($id_usuario)
  {
    return $this->conn->query('SELECT * FROM usuario WHERE id_usuario = ? AND active = 0', [$id_usuario])->then(function ($result) {
      return count($result->resultRows) > 0;
    }, function (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al verificar el estado del usuario: ' . $e->getMessage()]);
    });
  }

  public function login(ServerRequestInterface $request)
  {

    $body = $request->getBody()->getContents();
    $data = json_decode($body, true);

    AuthValidation::validateData($data);

    $username = $data['username'];
    $password = $data['password'];

    // Validar que el nombre de usuario y la contraseña no estén vacíos
    if (empty($username) || empty($password)) {
      return JSONResponse::response(400, [
        "ok" => false,
        "error" => "El nombre de usuario y la contraseña son obligatorios"
      ]);
    }
    return $this->conn->query(
      'SELECT id_usuario, nombre_usuario, contrasena, id_rol FROM usuario WHERE nombre_usuario = ?',
      [$username]
    )->then(
      function (QueryResult $result) use ($password) {
        // Verificar si se encontró algún usuario
        if (count($result->resultRows) === 0) {
          return JSONResponse::response(401, [
            "ok" => false,
            "error" => "El usuario o contraseña ingresados son incorrectos"
          ]);
        }

        $user = $result->resultRows[0];

        // Verificar si el usuario está deshabilitado
        return $this->isUserDisabled($user["id_usuario"])->then(
          function ($isDisabled) use ($user, $password) {
            if ($isDisabled) {
              return JSONResponse::response(403, [
                "ok" => false,
                "error" => "El usuario está deshabilitado"
              ]);
            }
            // Verificar la contraseña
            if (password_verify($password, $user["contrasena"])) {
              // Crear objeto de usuario para la respuesta (sin incluir contraseña)
              $userObj = [
                "id" => $user["id_usuario"],
                "username" => $user["nombre_usuario"],
                "id_rol" => $user["id_rol"]
              ];

              return JSONResponse::response(200, [
                "ok" => true,
                "message" => "Usuario autenticado correctamente",
                "user" => $userObj
              ]);
            } else {
              return JSONResponse::response(401, [
                "ok" => false,
                "error" => "El usuario o contraseña ingresados son incorrectos",
              ]);
            }
          }
        );
      },
      function (Exception $e) {
        return JSONResponse::response(500, [
          "ok" => false,
          "error" => $e->getMessage()
        ]);
      }
    );
  }
}
