<?php

use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\QueryResult;

require __DIR__ . '/../lib/json-response.php';
require __DIR__ . '/../validations/license.validation.php';


//!REGLAS DE NEGOCIO
//*La fecha de suspension es cuando el usuario suspende la cuenta.

//*Fecha de renovacion es cuando el usuario renueva la cuenta. No puede ser menor a la fecha de compra obviamente.

//*Las fechas siguen formato YYYY-MM-DD

//*Una vez suspendida la licencia, no se puede volver a activar. Se tiene que crear una nueva licencia.

class LicenseController
{
  private $conn;

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  public function getLicenses()
  {
    return $this->conn->query('SELECT * FROM licencias')->then(function ($result) {
      return JSONResponse::response(200, ['ok' => true, 'licencias' => $result->resultRows]);
    }, function (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al obtener las licencias: ' . $e->getMessage()]);
    });
  }

  public function getLicenseById(ServerRequestInterface $request, $id)
  {

    if (empty($id)) {
      return JSONResponse::response(400, ['ok' => false, 'error' => 'ID de licencia no proporcionado']);
    }

    return $this->conn->query('SELECT * FROM licencias WHERE id = ?', [$id])->then(function ($result) {
      if (count($result->resultRows) > 0) {
        return JSONResponse::response(200, ['ok' => true, 'licencia' => $result->resultRows[0]]);
      } else {
        return JSONResponse::response(404, ['ok' => false, 'error' => 'Licencia no encontrada']);
      }
    }, function (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al obtener la licencia: ' . $e->getMessage()]);
    });
  }

  // *Validando que el correo no este en uso
  public function licenseExists($email)
  {
    return $this->conn->query('SELECT * FROM licencias WHERE correo = ?', [$email])->then(function ($result) {
      return count($result->resultRows) > 0;
    }, function (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al verificar la existencia de la licencia: ' . $e->getMessage()]);
    });
  }

  // *Validando que el correo no este en uso (para ids que no sean el correspondiente)
  public function licenseExistsUpdate($email, $id) {
    return $this->conn->query('SELECT * FROM licencias WHERE correo = ? AND NOT id = ?', [$email, $id])->then(function ($result) {
      return count($result->resultRows) > 0;
    }, function (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al verificar la existencia de la licencia: ' . $e->getMessage()]);
    });
  }

  public function createLicense(ServerRequestInterface $request)
  {

    try {

      $body = $request->getBody()->getContents();
      $data = json_decode($body, true);
      LicenseValidation::validateData($data);

      $licenseData = [
        'plataforma' => $data['plataforma'],
        'correo' => $data['correo'],
        'contrasena' => $data['contrasena'],
        'fechaDeCompra' => $data['fechaDeCompra'],
        'fechaDeSuspension' => $data['fechaDeSuspension'] ? $data['fechaDeSuspension'] : null,
        'fechaDeRenovacion' => $data['fechaDeRenovacion'] ? $data['fechaDeRenovacion'] : null,
        'fechaDeVencimiento' => $data['fechaDeVencimiento']
      ];

      $insertQuery = 'INSERT INTO licencias (plataforma, correo, contrasena, fecha_de_compra, fecha_de_suspension, fecha_de_renovacion, fecha_de_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?)';
      $params = array_values($licenseData);

      return $this->licenseExists($licenseData['correo'])->then(function ($exists) use ($licenseData, $insertQuery, $params) {
        if ($exists) {
          return JSONResponse::response(400, ['ok' => false, 'error' => 'El correo ya está en uso']);
        }
        return $this->conn->query($insertQuery, $params)->then(function (QueryResult $result) use ($licenseData) {
          $license = array_merge(['id' => $result->insertId], $licenseData);
          return JSONResponse::response(200, ['ok' => true, 'message' => 'Licencia agregada correctamente', 'licencia' => $license]);
        }, function (Exception $e) {
          return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al agregar la licencia: ' . $e->getMessage()]);
        });
      }, function (Exception $e) {
        return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al validar el correo: ' . $e->getMessage()]);
      });
    } catch (Exception $e) {
      return JSONResponse::response(400, ['ok' => false, 'error' => $e->getMessage()]);
    }
  }

  public function updateLicenseById(ServerRequestInterface $request, $id)
  {

    try {
      $body = $request->getBody()->getContents();
      $data = json_decode($body, true);
      LicenseValidation::validateData($data);

      $licenseData = [
        'plataforma' => $data['plataforma'],
        'correo' => $data['correo'],
        'contrasena' => $data['contrasena'],
        'fechaDeCompra' => $data['fechaDeCompra'],
        'fechaDeSuspension' => $data['fechaDeSuspension'],
        'fechaDeRenovacion' => $data['fechaDeRenovacion'],
        'fechaDeVencimiento' => $data['fechaDeVencimiento']
      ];

      $selectQueryValidation = 'SELECT id FROM licencias WHERE id = ?';
      $updateQuery = 'UPDATE licencias SET plataforma = ?, correo = ?, contrasena = ?, fecha_de_compra = ?, fecha_de_suspension = ?, fecha_de_renovacion = ?, fecha_de_vencimiento = ? WHERE id = ?';

      $params = array_values($licenseData);

      return $this->licenseExistsUpdate($licenseData['correo'], $id)->then(function ($exists) use ($licenseData, $id, $selectQueryValidation, $updateQuery, $params) {
        
        //*Validando que el correo no este en uso (para ids que no sean el correspondiente)
        if ($exists) {
          return JSONResponse::response(400, ['ok' => false, 'error' => 'El correo ya está en uso']);
        }

        return $this->conn->query($selectQueryValidation, [$id])->then(function ($result) use ($updateQuery, $params, $id, $licenseData) {

          //* Verificar que la licencia exista
          if (count($result->resultRows) === 0) {
            return JSONResponse::response(404, ['ok' => false, 'error' => 'Licencia no encontrada']);
          }

          //* Actualizar la licencia con la nueva información
          return $this->conn->query($updateQuery, array_merge($params, [$id]))->then(function (QueryResult $result) use ($licenseData) {
            if ($result->affectedRows > 0) {
              $license = array_merge(['id' => $result->insertId], $licenseData);
              return JSONResponse::response(200, ['ok' => true, 'message' => 'Licencia actualizada correctamente', 'licencia' => $license]);
            } else {
              return JSONResponse::response(400, ['ok' => false, 'error' => 'No se realizaron cambios en la licencia']);
            }
          }, function (Exception $e) {
            return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al actualizar la licencia: ' . $e->getMessage()]);
          });
        });
      });

    } catch (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => $e->getMessage()]);
    }
  }

  public function renovateLicenseById(ServerRequestInterface $request, $id) {
    if (empty($id)) {
      return JSONResponse::response(400, ['ok' => false, 'error' => 'ID de licencia no proporcionado']);
    }
    $suspendQuery = 'UPDATE licencias SET fecha_de_renovacion = NOW() WHERE id = ?';
    return $this->conn->query('SELECT id FROM licencias WHERE id = ?', [$id])->then(function ($result) use ($id, $suspendQuery) {
      if (count($result->resultRows) === 0) {
        return JSONResponse::response(404, ['ok' => false, 'error' => 'Licencia con el id ' . $id . ' no encontrada']);
      }
      return $this->conn->query($suspendQuery, [$id])->then(function () {
        return JSONResponse::response(200, ['ok' => true, 'message' => 'Licencia renovada correctamente']);
      }, function (Exception $error) {
        return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al renovar la licencia: ' . $error->getMessage()]);
      });
    }, function (Exception $error) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al encontrar la licencia: ' . $error->getMessage()]);
    });
  }
  
  public function suspendLicenseById(ServerRequestInterface $request, $id)
  {
    if (empty($id)) {
      return JSONResponse::response(400, ['ok' => false, 'error' => 'ID de licencia no proporcionado']);
    }
    $suspendQuery = 'UPDATE licencias SET fecha_de_suspension = NOW(), suspended = 1 WHERE id = ?';
    return $this->conn->query('SELECT id FROM licencias WHERE id = ?', [$id])->then(function ($result) use ($id, $suspendQuery) {
      if (count($result->resultRows) === 0) {
        return JSONResponse::response(404, ['ok' => false, 'error' => 'Licencia con el id ' . $id . ' no encontrada']);
      }
      return $this->conn->query($suspendQuery, [$id])->then(function () {
        return JSONResponse::response(200, ['ok' => true, 'message' => 'Licencia suspendida correctamente']);
      }, function (Exception $error) {
        return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al suspender el la licencia: ' . $error->getMessage()]);
      });
    }, function (Exception $error) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al encontrar la licencia: ' . $error->getMessage()]);
    });
  }
}
