<?php

class LicenseController
{
  private $conn;

  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  public function obtenerLicencias()
  {
    $sql = "SELECT * FROM licencias";
    $result = $this->conn->query($sql);

    if (!$result) {
      return ["ok" => false, "message" => "Error en la consulta: " . $this->conn->error];
    }

    return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
  }

  public function agregarLicencia($plataforma, $correo, $contrasena, $fechaDeCompra, $fechaDeSuspension, $fechaDeRenovacion, $fechaDeVencimiento)
  {

    try {
      $stmt = $this->conn->prepare("INSERT INTO licencias (plataforma, correo, contrasena, fecha_de_compra, fecha_de_suspension, fecha_de_renovacion, fecha_de_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sssssss", $plataforma, $correo, $contrasena, $fechaDeCompra, $fechaDeSuspension, $fechaDeRenovacion, $fechaDeVencimiento);

      $stmt->execute();

      return [
        "ok" => true,
        "message" => "Licencia agregada correctamente"
      ];
    } catch (Exception $e) {
      return [
        "ok" => false,
        "message" => "Error al agregar la licencia"
      ];
    }
  }

  public function editarLicencia($id, $plataforma, $correo, $contrasena, $fechaDeCompra, $fechaDeSuspension, $fechaDeRenovacion, $fechaDeVencimiento)
  {

    try {
      $stmt = $this->conn->prepare("UPDATE licencias SET plataforma = '$plataforma', correo = '$correo', contrasena = '$contrasena', fecha_de_compra = '$fechaDeCompra', fecha_de_suspension = '$fechaDeSuspension', fecha_de_renovacion = '$fechaDeRenovacion', fecha_de_vencimiento = '$fechaDeVencimiento' WHERE id = $id");
      $stmt->bind_param("ssssssss", $plataforma, $correo, $contrasena, $fechaDeCompra, $fechaDeSuspension, $fechaDeRenovacion, $fechaDeVencimiento, $id);

      $stmt->execute();

      return [
        "ok" => true,
        "message" => "Licencia editada correctamente"
      ];
    } catch (Exception $e) {
      return [
        "ok" => false,
        "message" => "Error al editar la licencia"
      ];
    }
  }

  public function borrarLicencia($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM licencias WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
  }
}
