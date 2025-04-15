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
  // 'plataforma' => $data['plataforma'],
  //       'correo' => $data['correo'],
  //       'contrasena' => $data['contrasena'],
  //       'fechaDeCompra' => $data['fechaDeCompra'],
  //       'fechaDeSuspension' => $data['fechaDeSuspension'],
  //       'fechaDeRenovacion' => $data['fechaDeRenovacion'],
  //       'fechaDeVencimiento' => $data['fechaDeVencimiento']
  $data = [
    'plataforma' => $_POST['platform'],
    'correo' => $_POST['email'],
    'contrasena' => $_POST['password'],
    'fechaDeCompra' => $_POST['buy_date'],
    //Sumarle a la fecha de compra 1 año
    'fechaDeSuspension' => $_POST['buy_date'] + 365 * 24 * 60 * 60,
    'fechaDeRenovacion' => $_POST['renovation_date'],
    'fechaDeVencimiento' => $_POST['expire_date']
  ];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/license/create');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
  ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($httpCode == 200) {

    //Agregar al arreglo de licencias
    $responseData = json_decode($response, true);
    if ($responseData && isset($responseData['licencia'])) {
      $licenses[] = $responseData['licencia'];
    } else {
      $error_message = "Error al obtener los datos de la licencia.";
    }
  } else {
    $responseData = json_decode($response, true);
    if ($responseData && isset($responseData['error'])) {
      $error_message = $responseData['error'];
    } else {
      $error_message = "Error desconocido al agregar la licencia.";
    }
  }
}


?>

<!-- Titulo -->
<div class="container text-center">
  <h1 class="display-4">Bienvenido <strong><?php echo $_SESSION["username"] ?></strong></h1>
  <h1>Administración de licencias de FUSALMO</h1>
</div>
<!-- Formulario de licencias -->

<?php if ($error_message): ?>
  <div class="alert alert-danger">
    <?php echo htmlspecialchars($error_message); ?>
  </div>
<?php endif; ?>

<div class="container">
  <form method="POST" class="mt-3">
    <div class="row">
      <div class="col-6">
        <div class="form-group">
          <label for="license_id">ID: </label>
          <input type="text" class="form-control" id="license_id" name="license_id" placeholder="license_id">
        </div>
        <div class="form-group">
          <label for="platform">Plataforma: </label>
          <input type="text" class="form-control" id="platform" name="platform" placeholder="platform">
        </div>
        <div class="form-group">
          <label for="email">Correo: </label>
          <input type="text" class="form-control" id="email" name="email" placeholder="email@example.com">
        </div>
        <div class="form-group">
          <label for="password">Contraseña: </label>
          <input type="password" class="form-control" id="password" name="password">
        </div>
      </div>
      <div class="col-6">
        <div class="form-row">
          <label for="buy_date">Fecha de compra: </label>
          <input type="date" class="form-control" id="buy_date" name="buy_date" placeholder="buy_date">
        </div>

        <div class="form-row">
          <label for="renovation_date">Fecha de renovacion: </label>
          <input type="date" class="form-control" id="renovation_date" name="renovation_date" placeholder="renovation_date">
        </div>

        <div class="form-row">
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
        <th>Fecha de suspension</th>
        <th>Fecha de vencimiento</th>
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
            <td><?php echo htmlspecialchars($license['fecha_de_suspension'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($license['fecha_de_vencimiento'] ?? ''); ?></td>
            <td>
              <form method="post">
                <input type="hidden" name="license_id" value="<?php echo htmlspecialchars($license['id'] ?? ''); ?>" />
                <div class="d-flex justify-content-center align-items-center" style="gap: 10px;">
                  <button type="submit" name="action_send_email" value="SendEmail" class="btn btn-primary">
                    <i class="bi bi-envelope"></i>
                  </button>
                  <button type="submit" name="action_delete" value="Delete" class="btn btn-danger">
                    <i class="bi bi-trash"></i>
                  </button>
                  <button type="submit" name="action_editar" value="Edit" class="btn btn-warning text-white">
                    <i class="bi bi-pencil"></i>
                  </button>
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