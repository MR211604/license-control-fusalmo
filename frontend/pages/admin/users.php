<?php

if (!isset($_SESSION["id_rol"]) || $_SESSION["id_rol"] != 1) {
  header("Location: index.php?page=home");
  exit();
}

// Obtener los usuarios desde la API
$usuarios = [];
$error_message = null;

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

if ($_SERVER['REQUEST_METHOD'] == "POST") {

  $data = [
    'username' => $_POST['username'],
    'email' => $_POST['password'],
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
    } else {
      $error_message = "Errror al obtener datos del usuario.";
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


<?php if ($error_message): ?>
  <div class="alert alert-danger">
    <?php echo htmlspecialchars($error_message); ?>
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
    <div class="col-md-12">
      <table class="table table-bordered table-light">
        <thead class="table-active">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Contraseña</th>
            <th>Rol</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $user) {  ?>

            <tr>
              <td><?php echo htmlspecialchars($user['id_usuario'] ?? '') ?></td>
              <td><?php echo htmlspecialchars($user['nombre_usuario'] ?? '') ?></td>
              <td><?php echo htmlspecialchars($user['contrasena' ?? '']) ?></td>
              <td><?php echo htmlspecialchars($user['id_rol'] === "1" ? 'Administrador' : 'Usuario') ?></td>

              <td>
                <form method="POST">
                  <input type="hidden" name="user_id" id="user_id" value="<?php echo $user['id_usuario'] ?>">
                  <div class="d-flex justify-content-center align-items-center" style="gap: 10px;">
                    <button type="submit" class="btn btn-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                    <button type="submit" name="action_editar" value="Edit" class="btn btn-warning text-white">
                      <i class="bi bi-pencil"></i>
                    </button>
                  </div>
                </form>
            </tr>

          <?php  } ?>

        </tbody>
      </table>
    </div>
  </div>
</div>