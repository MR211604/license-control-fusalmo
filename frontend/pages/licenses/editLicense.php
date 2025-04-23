<?php

$api_url = getenv('API_URL') ?: 'http://localhost:8080';

if (!isset($_SESSION["user_id"])) {
  header("Location: index.php?page=login");
  exit();
}

$license = null;
$error_message = null;

// Verificar si se proporciona un ID de licencia en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
  $licenseId = $_GET['id'];

  // Obtener los datos de la licencia específica
  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . "/license/{$licenseId}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
      $responseData = json_decode($response, true);
      if (isset($responseData['licencia'])) {
        $license = $responseData['licencia'];
      } else {
        $error_message = "No se encontró la licencia solicitada";
      }
    } else {
      $error_message = "Error al obtener la licencia. Código: " . $httpCode;
    }
  } catch (Exception $e) {
    $error_message = "Error en la conexión: " . $e->getMessage();
  }
} else {
  $error_message = "ID de licencia no proporcionado";
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($licenseId)) {
  $data = [
    'plataforma' => $_POST['platform'],
    'correo' => $_POST['email'],
    'contrasena' => $_POST['password'],
    'fechaDeCompra' => $_POST['buy_date'],
    'fechaDeVencimiento' => $_POST['expire_date'],
    'fechaDeRenovacion' => isset($license['fecha_de_renovacion']) ? $license['fecha_de_renovacion'] : null,
    'fechaDeSuspension' => isset($license['fecha_de_vencimiento']) ? $license['fecha_de_vencimiento'] : null,
    'id_usuario' => $license['id_usuario'],
  ];

  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . "/license/update/{$licenseId}");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
      // Redirigir a la página principal con mensaje de éxito
      header("Location: index.php?page=home&editSucess=true");
      exit();
    } else {
      $responseData = json_decode($response, true);
      if (isset($responseData['error'])) {
        $error_message = $responseData['error'];
      } else {
        $error_message = "Error al actualizar la licencia. Código: " . $httpCode;
      }
    }
  } catch (Exception $e) {
    $error_message = "Error en la conexión: " . $e->getMessage();
  }
}
?>

<div class="container">
  <h2 class="mt-4 mb-4">Editar Licencia</h2>

  <?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <?php echo htmlspecialchars($error_message); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if ($license): ?>
    <form method="POST" class="mt-3">
      <div class="row">
        <div class="col-6">
          <div class="form-group mb-3">
            <label for="platform">Plataforma: </label>
            <input type="text" class="form-control" id="platform" name="platform" value="<?php echo htmlspecialchars($license['plataforma'] ?? ''); ?>">
          </div>
          <div class="form-group mb-3">
            <label for="email">Correo: </label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($license['correo'] ?? ''); ?>">
          </div>
          <div class="form-group mb-3">
            <label for="password">Contraseña: </label>
            <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($license['contrasena'] ?? ''); ?>">
          </div>
        </div>
        <div class="col-6">
          <div class="form-group mb-3">
            <label for="buy_date">Fecha de compra: </label>
            <input type="date" class="form-control" id="buy_date" name="buy_date" value="<?php echo htmlspecialchars($license['fecha_de_compra'] ?? ''); ?>">
          </div>
          <div class="form-group mb-3">
            <label for="expire_date">Fecha de vencimiento: </label>
            <input type="date" class="form-control" id="expire_date" name="expire_date" value="<?php echo htmlspecialchars($license['fecha_de_vencimiento'] ?? ''); ?>">
          </div>
        </div>
        <div class="row">
          <div class="col-12 mt-3 d-flex justify-content-center align-items-center" style="gap: 10px;">
            <button type="submit" class="btn btn-primary">Editar licencia</button>
            <a href="index.php?page=home" class="btn btn-secondary">Cancelar</a>
          </div>
        </div>
      </div>
    </form>
  <?php else: ?>
    <div class="alert alert-warning">
      No se pudo cargar la información de la licencia. <a href="index.php?page=home" class="alert-link">Volver a la lista de licencias</a>
    </div>
  <?php endif; ?>
</div>