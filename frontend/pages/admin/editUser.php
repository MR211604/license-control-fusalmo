<?php

if (!isset($_SESSION["id_rol"]) || $_SESSION["id_rol"] != 1) {
  header("Location: index.php?page=home");
  exit();
}

$user = null;
$error_message = null;

// Verificar si se proporciona un ID de licencia en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
  $userId = $_GET['id'];

  // Obtener los datos de la licencia específica
  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/user/{$userId}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $responseData = json_decode($response, true);

    if ($httpCode == 200) {
      if (isset($responseData['usuario'])) {
        $user = $responseData['usuario'];
      } else {
        $error_message = "No se encontró el usuario solicitado";
      }
    } else {
      $error_message = isset($responseData['error']) ? $responseData['error'] : "Error al obtener el usuario. Código: {$httpCode}";
    }
  } catch (Exception $e) {
    $error_message = "Error en la conexión: " . $e->getMessage();
  }
} else {
  $error_message = "ID de usuario no proporcionado";
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($userId)) {

  $data = [
    'username' => $_POST['username'],
    'email' => $_POST['email'],
    'password' => $_POST['password'],
    'confirmPassword' => $_POST['confirmPassword'],
    'rol' => $_POST['rol']
  ];

  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/user/update/{$userId}");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responseData = json_decode($response, true);
    if ($httpCode == 200) {
      // Redirigir a la página principal con mensaje de éxito
      header("Location: index.php?page=admin/users&editSucess=true");
      exit();
    } else {
      $error_message = isset($responseData['error']) ? $responseData['error'] : "Error al actualizar el usuario. Código: {$httpCode}";
    }
  } catch (Exception $e) {
    $error_message = "Error en la conexión: " . $e->getMessage();
  }
}

?>


<div class="container">

  <h2 class="mt-4 mb-4">Editar usuario</h2>

  <?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <?php echo htmlspecialchars($error_message); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-6">

      <?php if ($user): ?>
        <form method="POST">
          <div class="form-group mb-3">
            <label for="username">Nombre: </label>
            <input type="text" class="form-control" id="username" name="username" placeholder="username" value="<?php echo htmlspecialchars($user['nombre_usuario'] ?? ''); ?>">
          </div>

          <div class="form-group mb-3">
            <label for="username">Correo: </label>
            <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" value="<?php echo htmlspecialchars($user['correo'] ?? ''); ?>">
          </div>

          <div class="form-group mb-3">
            <label for="password">Contraseña: </label>
            <input type="password" class="form-control" id="password" name="password" placeholder="*********">
          </div>

          <div class="form-group mb-3">
            <label for="password">Confirmar contraseña: </label>
            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="*********">
          </div>

          <div class="form-group mb-3">
            <label for="rol">Rol: </label>
            <select class="form-control" id="rol" name="rol">
              <option value="1" <?php echo (isset($user['id_rol']) && $user['id_rol'] == 1) ? 'selected' : ''; ?>>Administrador</option>
              <option value="2" <?php echo (isset($user['id_rol']) && $user['id_rol'] == 2) ? 'selected' : ''; ?>>Usuario</option>
            </select>
          </div>
          <div class="row">
            <div class="col-12 mt-3 d-flex justify-content-center align-items-center" style="gap: 10px;">
              <button type="submit" class="btn btn-primary">Editar usuario</button>
              <a href="index.php?page=admin/users" class="btn btn-secondary">Cancelar</a>
            </div>
          </div>
        </form>
      <?php else: ?>
        <div class="alert alert-warning">
          No se pudo cargar la información del usuario. <a href="index.php?page=admin/users" class="alert-link">Volver a la lista de usuarios</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>