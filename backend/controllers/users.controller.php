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
        'rol' => $data['rol'] ? $data['rol'] : 2, // Rol por defecto es 2 (Usuario)
        'active' => 0 // Por defecto, el usuario se crea como inactivo
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

          $createData = [
            $userData['username'],
            $userData['email'],
            $hashedPassword,
            $userData['rol'],
            $userData['active']
          ];

          $createQuery = 'INSERT INTO usuario (nombre_usuario, correo, contrasena, id_rol, active) VALUES (?, ?, ?, ?, ?)';

          return $this->conn->query($createQuery, $createData)->then(
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
        'password' => $data['password'] ? $data['password'] : null,
        'confirmPassword' => $data['confirmPassword'] ? $data['confirmPassword'] : null,
        'rol' => $data['rol'] ? $data['rol'] : 2
      ];

      $updateQuery = 'UPDATE usuario SET nombre_usuario = ?, correo = ?, contrasena = ?, id_rol = ? WHERE id_usuario = ?';
      $selectQueryValidation = 'SELECT id_usuario FROM usuario WHERE id_usuario = ?';

      //No cambiar la contraseña si no se proporciona una nueva
      if ($userData['password'] === null && $userData['confirmPassword'] === null) {

        $updateWithoutPasswordQuery = 'UPDATE usuario SET nombre_usuario = ?, correo = ?, id_rol = ? WHERE id_usuario = ?';

        return $this->userExistsUpdate($userData['email'], $userData['username'], $id)->then(
          function ($exists) use ($id, $updateWithoutPasswordQuery, $userData, $selectQueryValidation) {

            if ($exists === true) {
              return JSONResponse::response(400, [
                "ok" => false,
                "error" => "El correo o usuario ingresado ya está en uso"
              ]);
            }
            $updateDataWithoutPassword = [
              $userData['username'],
              $userData['email'],
              $userData['rol'],
              $id
            ];
            return $this->conn->query($selectQueryValidation, [$id])->then(function ($result) use ($updateWithoutPasswordQuery, $updateDataWithoutPassword) {
              if (count($result->resultRows) === 0) {
                return JSONResponse::response(404, ['ok' => false, 'error' => 'Usuario no encontrado']);
              }
              return $this->conn->query($updateWithoutPasswordQuery, $updateDataWithoutPassword)->then(function (QueryResult $result) {
                if ($result->affectedRows > 0) {
                  return JSONResponse::response(200, ['ok' => true, 'message' => 'Usuario actualizado correctamente']);
                } else {
                  return JSONResponse::response(404, ['ok' => false, 'error' => 'Usuario no actualizado. Los datos ingresados son iguales a los actuales']);
                }
              }, function (Exception $e) {
                return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al actualizar el usuario: ' . $e->getMessage()]);
              });
            });
          }
        );
      }

      $hashedPassword = password_hash($userData['password'], PASSWORD_BCRYPT);

      $updateData = [
        $userData['username'],
        $userData['email'],
        $hashedPassword,
        $userData['rol'],
        $id
      ];

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
                return JSONResponse::response(404, ['ok' => false, 'error' => 'Usuario no actualizado. Los datos ingresados son iguales a los actuales']);
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

  public function disableUser(ServerRequestInterface $request, $id)
  {
    try {
      if (empty($id)) {
        return JSONResponse::response(400, ['ok' => false, 'error' => 'ID de usuario no proporcionado']);
      }

      $disableQuery = 'UPDATE usuario SET active = 0 WHERE id_usuario = ?';
      $selectQueryValidation = 'SELECT id_usuario FROM usuario WHERE id_usuario = ?';

      return $this->conn->query($selectQueryValidation, [$id])->then(function ($result) use ($disableQuery, $id) {
        if (count($result->resultRows) === 0) {
          return JSONResponse::response(404, ['ok' => false, 'error' => 'Usuario no encontrado']);
        }
        return $this->conn->query($disableQuery, [$id])->then(function (QueryResult $result) {
          if ($result->affectedRows > 0) {
            return JSONResponse::response(200, ['ok' => true, 'message' => 'Usuario deshabilitado correctamente']);
          } else {
            return JSONResponse::response(404, ['ok' => false, 'error' => 'Usuario no encontrado']);
          }
        }, function (Exception $e) {
          return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al deshabilitar el usuario: ' . $e->getMessage()]);
        });
      });
    } catch (Exception $e) {
      return JSONResponse::response(500, [
        "ok" => false,
        "error" => $e->getMessage()
      ]);
    }
  }

  public function enableUser(ServerRequestInterface $request, $id)
  {
    try {
      if (empty($id)) {
        return JSONResponse::response(400, ['ok' => false, 'error' => 'ID de usuario no proporcionado']);
      }

      $disableQuery = 'UPDATE usuario SET active = 1 WHERE id_usuario = ?';
      $selectQueryValidation = 'SELECT id_usuario FROM usuario WHERE id_usuario = ?';

      return $this->conn->query($selectQueryValidation, [$id])->then(function ($result) use ($disableQuery, $id) {
        if (count($result->resultRows) === 0) {
          return JSONResponse::response(404, ['ok' => false, 'error' => 'Usuario no encontrado']);
        }
        return $this->conn->query($disableQuery, [$id])->then(function (QueryResult $result) {
          if ($result->affectedRows > 0) {
            return JSONResponse::response(200, ['ok' => true, 'message' => 'Usuario habilitado correctamente']);
          } else {
            return JSONResponse::response(404, ['ok' => false, 'error' => 'Usuario no encontrado']);
          }
        }, function (Exception $e) {
          return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al habilitar el usuario: ' . $e->getMessage()]);
        });
      });
    } catch (Exception $e) {
      return JSONResponse::response(500, [
        "ok" => false,
        "error" => $e->getMessage()
      ]);
    }
  }
}
