<?php

if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 1) {
  header("Location: index.php?page=home");
  exit();
}

// Obtener los usuarios desde la API
$usuarios = [];
$error_message = null;

try {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/license/getAll');
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

?>


<article>
  <div class="container text-center">
    <h1>Listado de usuarios</h1>
  </div>
</article>


<div class="container">
  <div class="row">
    <div class="col-md-6">
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="user_id">ID: </label>
          <input type="text" required class="form-control" id="user_id" name="user_id" placeholder="user_id">
        </div>

        <div class="form-group">
          <label for="username">Nombre: </label>
          <input type="text" class="form-control" id="username" name="username" placeholder="username">
        </div>

        <div class="form-group">
          <label for="password">Contraseña: </label>
          <input type="password" class="form-control" id="password" name="password" placeholder="password">
        </div>

        <div class="form-group">
          <label for="role">Rol: </label>
          <select class="form-control" id="role" name="role">
            <option value="1">Administrador</option>
            <option value="2">Usuario</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary">Crear usuario</button>

      </form>
    </div>

  </div>
</div>


<!-- Tablas -->

<div class="container">
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
              <td><?php echo htmlspecialchars($user['id_rol'] ?? '') ?></td>

              <td>
                <form method="POST">
                  <input type="hidden" name="user_id" id="user_id" value="<?php echo $user['id_usuario'] ?>">
                  <button type="submit" class="btn btn-danger">Eliminar</button>

                </form>
            </tr>

          <?php  } ?>

        </tbody>
      </table>
    </div>
  </div>
</div>