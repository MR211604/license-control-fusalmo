<?php

use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\QueryResult;

require __DIR__ . '/../lib/json-response.php';
require __DIR__ . '/../validations/license.validation.php';


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

  public function createLicense(ServerRequestInterface $request)
  {

    try {

      //$plataforma, $correo, $contrasena, $fechaDeCompra, $fechaDeSuspension, $fechaDeRenovacion, $fechaDeVencimiento
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

      $insertQuery = 'INSERT INTO licencias (plataforma, correo, contrasena, fecha_de_compra, fecha_de_suspension, fecha_de_renovacion, fecha_de_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?)';
      $params = array_values($licenseData);

      return $this->conn->query($insertQuery, $params)->then(function (QueryResult $result) use ($licenseData) {

        $license = array_merge(['id' => $result->insertId], $licenseData);

        return JSONResponse::response(200, ['ok' => true, 'message' => 'Licencia agregada correctamente', 'licencia' => $license]);
      }, function (Exception $e) {
        return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al agregar la licencia: ' . $e->getMessage()]);
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

      return $this->conn->query($selectQueryValidation, [$id])->then(function ($result) use ($updateQuery, $params, $id, $licenseData) {
        if (count($result->resultRows) === 0) {
          return JSONResponse::response(404, ['ok' => false, 'error' => 'Licencia no encontrada']);
        }
        return $this->conn->query($updateQuery, array_merge($params, [$id]))->then(function (QueryResult $result) use ($licenseData) {
          if ($result->affectedRows > 0) {
            $license = array_merge(['id' => $result->insertId], $licenseData);
            return JSONResponse::response(200, ['ok' => true, 'message' => 'Licencia actualizada correctamente', 'licencia' => $license]);
          } else {
            return JSONResponse::response(404, ['ok' => false, 'error' => 'Licencia no encontrada']);
          }
        }, function (Exception $e) {
          return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al actualizar la licencia: ' . $e->getMessage()]);
        });
      });
    } catch (Exception $e) {
      return JSONResponse::response(500, ['ok' => false, 'error' => $e->getMessage()]);
    }
  }

  public function deleteLicenseById(ServerRequestInterface $request, $id)
  {
    if (empty($id)) {
      return JSONResponse::response(400, ['ok' => false, 'error' => 'ID de producto no proporcionado']);
    }

    return $this->conn->query('SELECT id FROM licencias WHERE id = ?', [$id])->then(function ($result) use ($id) {
      if (count($result->resultRows) === 0) {
        return JSONResponse::response(404, ['ok' => false, 'error' => 'Producto con el id ' . $id . ' no encontrado']);
      }
      return $this->conn->query('DELETE FROM licencias WHERE id = ?', [$id])->then(function () {
        return JSONResponse::response(200, ['ok' => true, 'message' => 'Producto eliminado correctamente']);
      }, function (Exception $error) {
        return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al eliminar el producto: ' . $error->getMessage()]);
      });
    }, function (Exception $error) {
      return JSONResponse::response(500, ['ok' => false, 'error' => 'Error al buscar el producto: ' . $error->getMessage()]);
    });
  }
}
