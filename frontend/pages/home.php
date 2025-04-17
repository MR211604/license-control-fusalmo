<?php

// Verificar si el usuario está autenticado
if (!isset($_SESSION["user_id"])) {
  // Redirigir usando el parámetro page en lugar de cambiar la URL completa
  header("Location: index.php?page=login");
  exit();
}

// Obtener las licencias desde la API
$licenses = [];
$error_message = null;
$success_message = null;

// Verificar si hay un mensaje de éxito en la URL
if (isset($_GET['success']) && $_GET['success'] === 'true') {
  $success_message = "Licencia actualizada correctamente";
}

// *Cargando las licencias desde la API*
try {
  // Configurar la petición cURL para obtener todas las licencias
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/license/getAll');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    // Si la API requiere token de autenticación:
    // 'Authorization: Bearer ' . ($_SESSION['token'] ?? '')
  ]);

  // Ejecutar la petición
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  // Verificar si la petición fue exitosa
  if ($httpCode == 200) {
    $responseData = json_decode($response, true);
    // Verificar si la respuesta contiene el array de licencias
    if (isset($responseData['licencias']) && is_array($responseData['licencias'])) {
      $licenses = $responseData['licencias'];
    } else {
      $error_message = "No se encontraron licencias en la respuesta";
    }
  } else {
    $error_message = "Error al obtener licencias. Código: " . $httpCode;
  }
} catch (Exception $e) {
  $error_message = "Error en la conexión: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

  // Procesar renovación de licencia
  if (isset($_POST['action_renovate']) && $_POST['action_renovate'] === 'Renovate' && isset($_POST['license_id'])) {
    $licenseId = $_POST['license_id'];
    renovateLicense($licenseId);
  } elseif (isset($_POST['action_suspend']) && $_POST['action_suspend'] === 'Suspend' && isset($_POST['license_id'])) {
    $licenseId = $_POST['license_id'];
    suspendLicense($licenseId);
  } else {
    // Procesar el formulario de creación de licencia
    $data = [
      'plataforma' => $_POST['platform'],
      'correo' => $_POST['email'],
      'contrasena' => $_POST['password'],
      'fechaDeCompra' => $_POST['buy_date'],
      'fechaDeRenovacion' => $_POST['renovation_date'] ? $_POST['renovation_date'] : null,
      'fechaDeVencimiento' => $_POST['expire_date'] ? $_POST['expire_date'] : null,
    ];
    echo 'info que se envia' . json_encode($data);
  }
}

/**
 * Función para renovar la fecha de una licencia
 * @param int $licenseId ID de la licencia a renovar
 */
function renovateLicense($licenseId)
{
  global $error_message;

  // Obtener primero la licencia actual para mantener todos sus datos
  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/license/renovate/{$licenseId}");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);

    $updateResponse = curl_exec($ch);
    $updateHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($updateHttpCode == 200) {
      // Pasar el mensaje de éxito como parámetro en la URL
      header("Location: index.php?page=home&success=true");
      exit();
    } else {
      $updateData = json_decode($updateResponse, true);
      $error_message = isset($updateData['error']) ? $updateData['error'] : "Error al renovar la licencia. Código: {$updateHttpCode}";
    }
  } catch (Exception $e) {
    $error_message = "Error en la conexión: " . $e->getMessage();
  }
}

/**
 * Función para suspender la fecha de una licencia
 * @param int $licenseId ID de la licencia a suspender
 */
function suspendLicense($licenseId)
{
  global $error_message;

  // Obtener primero la licencia actual para mantener todos sus datos
  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/license/suspend/{$licenseId}");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);

    $updateResponse = curl_exec($ch);
    $updateHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($updateHttpCode == 200) {
      // Pasar el mensaje de éxito como parámetro en la URL
      header("Location: index.php?page=home&success=true");
      exit();
    } else {
      $updateData = json_decode($updateResponse, true);
      $error_message = isset($updateData['error']) ? $updateData['error'] : "Error al suspender la licencia. Código: {$updateHttpCode}";
    }
  } catch (Exception $e) {
    $error_message = "Error en la conexión: " . $e->getMessage();
  }
}

?>

<!-- Titulo -->
<div class="container text-center">
  <h1 class="display-4">Bienvenido <strong><?php echo $_SESSION["username"] ?></strong></h1>
  <h1>Administración de licencias de FUSALMO</h1>
</div>
<!-- Formulario de licencias -->

<?php if ($success_message): ?>
  <div class="alert alert-success">
    <?php echo htmlspecialchars($success_message); ?>
  </div>
<?php endif; ?>

<?php if ($error_message): ?>
  <div class="alert alert-danger">
    <?php echo htmlspecialchars($error_message); ?>
  </div>
<?php endif; ?>

<div class="container">
  <form method="POST" class="mt-3">
    <div class="row">
      <div class="col-6">
        <div class="form-group mb-3">
          <label for="platform">Plataforma: </label>
          <input type="text" class="form-control" id="platform" name="platform" placeholder="platform">
        </div>
        <div class="form-group mb-3">
          <label for="email">Correo: </label>
          <input type="text" class="form-control" id="email" name="email" placeholder="email@example.com">
        </div>
        <div class="form-group mb-3">
          <label for="password">Contraseña: </label>
          <input type="password" class="form-control" id="password" name="password">
        </div>
      </div>
      <div class="col-6">
        <div class="form-group mb-3">
          <label for="buy_date">Fecha de compra: </label>
          <input type="date" class="form-control" id="buy_date" name="buy_date" placeholder="buy_date">
        </div>
        <div class="form-group mb-3">
          <label for="expire_date">Fecha de vencimiento: </label>
          <input type="date" class="form-control" id="expire_date" name="expire_date" placeholder="expire_date">
        </div>
      </div>
      <div class="row">
        <div class="col-12 mt-3 d-flex justify-content-center align-items-center" style="gap: 10px;">
          <button type="submit" class="btn btn-primary">Agregar nueva licencia</button>
        </div>
      </div>

  </form>

</div>

<!-- Tabla de licencias -->
<div class="table-responsive">
  <table class="mt-3 table table-bordered table-light">
    <thead class="table-active">
      <tr>
        <th>ID</th>
        <th>Plataforma</th>
        <th>Correo</th>
        <th>Contraseña</th>
        <th>Fecha de compra</th>
        <th>Fecha de vencimiento</th>
        <th>Fecha de renovación</th>
        <th>Fecha de suspensión</th>
        <th>Estado</th>
        <th>Opciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($licenses)): ?>
        <tr>
          <td colspan="8" class="text-center">No hay licencias disponibles</td>
        </tr>
      <?php else: ?>
        <?php foreach ($licenses as $license): ?>
          <tr>
            <td><?php echo htmlspecialchars($license['id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($license['plataforma'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($license['correo'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($license['contrasena'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($license['fecha_de_compra'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($license['fecha_de_vencimiento'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($license['fecha_de_renovacion'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($license['fecha_de_suspension'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($license['suspended'] == 0 ? 'Activa' : 'Suspendida'); ?></td>
            <td>
              <form method="post">
                <input type="hidden" name="license_id" value="<?php echo htmlspecialchars($license['id'] ?? ''); ?>" />
                <div class="d-flex justify-content-center align-items-center" style="gap: 10px;">

                  <?php if ($license['suspended'] == 0): ?>
                    <button type="submit" name="action_send_email" value="SendEmail" class="btn btn-primary">
                      <i class="bi bi-envelope"></i>
                    </button>
                    <button type="submit" name="action_editar" value="Edit" class="btn btn-warning text-white">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="submit" name="action_suspend" value="Suspend" class="btn btn-danger">
                      <i class="bi bi-x-circle"></i>
                    </button>
                    <button type="submit" name="action_renovate" value="Renovate" class="btn btn-success">
                      <i class="bi bi-arrow-clockwise"></i>
                    </button>
                  <?php endif ?>
                </div>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</div>