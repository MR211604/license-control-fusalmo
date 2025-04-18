<?php

if (!isset($_SESSION["id_rol"]) || $_SESSION["id_rol"] != 1) {
  header("Location: index.php?page=home");
  exit();
}

// Obtener los usuarios desde la API
$usuarios = [];
$error_message = null;
$success_message = null;

try {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/user/getAll');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
  ]);

  // Ejecutar la petición
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  // Verificar si la petición fue exitosa
  if ($httpCode == 200) {
    $responseData = json_decode($response, true);
    if (isset($responseData['usuarios']) && is_array($responseData['usuarios'])) {
      $usuarios = $responseData['usuarios'];
    } else {
      $error_message = "No se encontraron usuarios en la respuesta";
    }
  } else {
    $error_message = "Error al obtener usuarios. Código: " . $httpCode;
  }
} catch (Exception $e) {
  $error_message = "Error en la conexión: " . $e->getMessage();
}

if (isset($_GET['createSuccess']) && $_GET['createSuccess'] === 'true') {
  $success_message = "Usuario creado exitosamente";
}

if (isset($_GET['editSucess']) && $_GET['editSucess'] === 'true') {
  $success_message = "Usuario actualizado exitosamente";
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

  $data = [
    'username' => $_POST['username'],
    'email' => $_POST['email'],
    'password' => $_POST['password'],
    'confirmPassword' => $_POST['confirmPassword'],
    'rol' => $_POST['rol']
  ];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/user/create');
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

    $responseData = json_decode($response, true);
    if ($responseData && isset($responseData['user'])) {
      $usuarios[] = $responseData['user'];
      header("Location: index.php?page=admin/users&createSuccess=true");
      exit();
    } else {
      $error_message = "Error al obtener datos del usuario.";
    }
  } else {
    $responseData = json_decode($response, true);
    if ($responseData && isset($responseData['error'])) {
      $error_message = $responseData['error'];
    } else {
      $error_message = "Error desconocido al crear el usuario.";
    }
  }
}

?>

<article>
  <div class="container text-center">
    <h1>Listado de usuarios</h1>
  </div>
</article>

<?php if ($success_message): ?>
  <div class="alert alert-success alert-dismissible fade show">
    <?php echo htmlspecialchars($success_message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if ($error_message): ?>
  <div class="alert alert-danger alert-dismissible fade show">
    <?php echo htmlspecialchars($error_message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="container">
  <div class="row">
    <div class="col-md-6">
      <form method="POST">
        <div class="form-group mb-3">
          <label for="username">Nombre: </label>
          <input type="text" class="form-control" id="username" name="username" placeholder="username">
        </div>

        <div class="form-group mb-3">
          <label for="username">Correo: </label>
          <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com">
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
            <option value="1">Administrador</option>
            <option value="2">Usuario</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary mt-2 mb-4">Crear usuario</button>
      </form>
    </div>
  </div>
</div>


<!-- Tabla de usuarios -->
<div class="container mt-2">
  <div class="row">
    <div class="table-responsive">
      <table class="table table-bordered table-light">
        <thead class="table-active">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $user) {  ?>

            <tr>
              <td><?php echo htmlspecialchars($user['id_usuario'] ?? '') ?></td>
              <td><?php echo htmlspecialchars($user['nombre_usuario'] ?? '') ?></td>
              <td><?php echo htmlspecialchars($user['correo'] ?? '') ?></td>
              <td><?php echo htmlspecialchars($user['id_rol'] === "1" ? 'Administrador' : 'Usuario') ?></td>
              <td>
                <?php if ($user['active'] == 1): ?>
                  <span class="badge text-bg-success">Activo</span>
                <?php else: ?>
                  <span class="badge text-bg-danger">Deshabilitado</span>
                <?php endif; ?>
              </td>

              <td>
                <form method="POST">
                  <input type="hidden" name="user_id" id="user_id" value="<?php echo $user['id_usuario'] ?>">
                  <div class="d-flex justify-content-center align-items-center" style="gap: 10px;">

                    <?php if ($user['active'] == 1): ?>
                      <button type="submit" class="btn btn-danger" value="action_disable_user" value="DisableUser" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deshabilitar usuario">
                        <i class="bi bi-person-dash"></i>
                      </button>
                    <?php else: ?>
                      <button type="submit" class="btn btn-primary text-white" name="action_enable_user" value="EnableUser" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Reactivar usuario">
                      <i class="bi bi-arrow-clockwise"></i>
                      </button>
                    <?php endif; ?>

                    <a href="index.php?page=admin/editUser&id=<?php echo htmlspecialchars($user['id_usuario'] ?? ''); ?>" class="btn btn-warning text-white" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Editar usuario">
                      <i class="bi bi-pencil"></i>
                    </a>

                  </div>
                </form>
            </tr>

          <?php  } ?>

        </tbody>
      </table>
    </div>
  </div>
</div>