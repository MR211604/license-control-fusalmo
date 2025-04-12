<?php
// Verificar si ya está logueado
if (isset($_SESSION["user_id"])) {
  header("Location: index.php?page=home");
  exit();
}

// Procesar el formulario si se envió
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Si la autenticación es exitosa:
  $_SESSION["user_id"] = $user_id; // ID del usuario
  $_SESSION["username"] = $username; // Nombre de usuario
  header("Location: index.php?page=home");
  exit();
}


?>

<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <div id="iniciodesesion" class="container-fluid" style="min-width: 50%; max-width: 70%;">
    <div class="p-4 mb-4 bg-light rounded-3 m-4">
      <div class="col">

        <?php if (isset($error_message)): ?>
          <div class="alert alert-danger mx-4">
            <?php echo htmlspecialchars($error_message); ?>
          </div>
        <?php endif; ?>

        <div class="card">
          <div class="class-header text-center pt-4">
            <strong>
              <h3>Inicio de sesion</h3>
            </strong>
          </div>
          <div class="card-body">
            <form class="m-4" method="POST">
              <div class="form-group mb-4">
                <label for="usuario">Usuario</label>
                <input type="text" class="form-control" name="username" placeholder="Ingresar usuario">
              </div>
              <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="text" class="form-control" name="password" placeholder="Ingresar usuario">
              </div>
              <button type="submit" class="btn btn-primary mt-4">Iniciar sesión</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>