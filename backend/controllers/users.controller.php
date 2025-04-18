<?php

use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\QueryResult;

require __DIR__ . '/../validations/user.validation.php';


class UserController
{

  private $conn;

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  public function getUsers()
  {
    try {
      return $this->conn->query('SELECT * FROM usuario')->then(function ($result) {
        return JSONResponse::response(200, ['ok' => true, 'usuarios' => $result->resultRows]);
      }, function (Exception $e) {
        return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al obtener los usuarios: ' . $e->getMessage()]);
      });
    } catch (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al obtener los usuarios: ' . $e->getMessage()]);
    }
  }

  public function getUserById(ServerRequestInterface $request, $id)
  {
    if (empty($id)) {
      return JSONResponse::response(400, ['ok' => false, 'error' => 'ID de usuario no proporcionado']);
    }

    return $this->conn->query('SELECT * FROM usuario WHERE id_usuario = ?', [$id])->then(function ($result) {
      if (count($result->resultRows) > 0) {
        return JSONResponse::response(200, ['ok' => true, 'usuario' => $result->resultRows[0]]);
      } else {
        return JSONResponse::response(404, ['ok' => false, 'error' => 'usuario no encontrada']);
      }
    }, function (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al obtener el usuario: ' . $e->getMessage()]);
    });
  }

  public function userExists($email, $username)
  {
    return $this->conn->query('SELECT * FROM usuario WHERE correo = ? OR nombre_usuario = ?', [$email, $username])->then(function ($result) {
      return count($result->resultRows) > 0;
    }, function (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al verificar la existencia del usuario: ' . $e->getMessage()]);
    });
  }

  public function userExistsUpdate($email, $username, $id)
  {
    return $this->conn->query('SELECT * FROM usuario WHERE (correo = ? OR nombre_usuario = ?) AND id_usuario != ?', [$email, $username, $id])->then(function ($result) {
      return count($result->resultRows) > 0;
    }, function (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al verificar la existencia del usuario: ' . $e->getMessage()]);
    });
  }

  //Por defecto, un usuario se crea con roles de usuario.
  public function createUser(ServerRequestInterface $request)
  {
    try {
      //($username, $email, $password, $role = 2
      $body = $request->getBody()->getContents();
      $data = json_decode($body, true);

      UserValidation::validateData($data);

      $userData = [
        'username' => $data['username'],
        'email' => $data['email'],
        'password' => $data['password'],
        'confirmPassword' => $data['confirmPassword'],
        'rol' => $data['rol'] ? $data['rol'] : 2 // Rol por defecto es 2 (Usuario)
      ];

      UserValidation::validateData($userData);

      UserValidation::validateConfirmPassword($userData['password'], $userData['confirmPassword']);

      // Modificar esta parte para manejar la promesa correctamente
      return $this->userExists($userData['email'], $userData['username'])->then(
        function ($exists) use ($userData) {

          if ($exists === true) {
            return JSONResponse::response(400, [
              "ok" => false,
              "error" => "El correo o usuario ingresado ya está en uso"
            ]);
          }

          // Si el usuario no existe, continúa con la creación
          $hashedPassword = password_hash($userData['password'], PASSWORD_BCRYPT);

          $updateData = [
            $userData['username'],
            $userData['email'],
            $hashedPassword,
            $userData['rol'],
          ];

          $createQuery = 'INSERT INTO usuario (nombre_usuario, correo, contrasena, id_rol) VALUES (?, ?, ?, ?)';

          return $this->conn->query($createQuery, $updateData)->then(
            function (QueryResult $result) use ($userData) {
              $user = array_merge(['id' => $result->insertId], $userData);
              return JSONResponse::response(200, [
                "ok" => true,
                "message" => "Usuario creado correctamente",
                "user" => $user
              ]);
            },
            function (Exception $e) {
              return JSONResponse::response(500, [
                "ok" => false,
                "error" => "Error al crear el usuario: " . $e->getMessage()
              ]);
            }
          );
        }
      );
    } catch (Exception $e) {
      return JSONResponse::response(500, [
        "ok" => false,
        "error" => $e->getMessage()
      ]);
    }
  }

  public function updateUserById(ServerRequestInterface $request, $id)
  {
    try {
      $body = $request->getBody()->getContents();
      $data = json_decode($body, true);

      UserValidation::validateData($data);

      $userData = [
        'username' => $data['username'],
        'email' => $data['email'],
        'password' => $data['password'],
        'confirmPassword' => $data['confirmPassword'],
        'rol' => $data['rol'] ? $data['rol'] : 2
      ];


      $hashedPassword = password_hash($userData['password'], PASSWORD_BCRYPT);

      $updateData = [
        $userData['username'],
        $userData['email'],
        $hashedPassword,
        $userData['rol'],
        $id
      ];

      $updateQuery = 'UPDATE usuario SET nombre_usuario = ?, correo = ?, contrasena = ?, id_rol = ? WHERE id_usuario = ?';
      $selectQueryValidation = 'SELECT id_usuario FROM usuario WHERE id_usuario = ?';

      UserValidation::validateConfirmPassword($userData['password'], $userData['confirmPassword']);

      return $this->userExistsUpdate($userData['email'], $userData['username'], $id)->then(
        function ($exists) use ($id, $updateQuery, $updateData, $selectQueryValidation) {
          if ($exists === true) {
            return JSONResponse::response(400, [
              "ok" => false,
              "error" => "El correo o usuario ingresado ya está en uso"
            ]);
          }
          return $this->conn->query($selectQueryValidation, [$id])->then(function ($result) use ($updateQuery, $updateData) {
            if (count($result->resultRows) === 0) {
              return JSONResponse::response(404, ['ok' => false, 'error' => 'Usuario no encontrado']);
            }
            return $this->conn->query($updateQuery, $updateData)->then(function (QueryResult $result) {
              if ($result->affectedRows > 0) {
                return JSONResponse::response(200, ['ok' => true, 'message' => 'Usuario actualizado correctamente']);
              } else {
                return JSONResponse::response(404, ['ok' => false, 'error' => 'Usuario no encontrado']);
              }
            }, function (Exception $e) {
              return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al actualizar el usuario: ' . $e->getMessage()]);
            });
          });
        }
      );
    } catch (Exception $e) {
      return JSONResponse::response(500, [
        "ok" => false,
        "error" => $e->getMessage()
      ]);
    }
  }
}
