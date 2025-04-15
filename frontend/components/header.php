<?php

// session_start();

if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: ../frontend/index.php?page=login");
  exit();
}

?>

<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
  <div class="container-fluid">
    <a class="navbar-brand">FUSALMO - Control de licencias</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="./index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1): ?>
            <a class="nav-link" href="index.php?page=admin/users">Usuarios</a>
          <?php endif; ?>
        </li>

        <li class="nav-item">
          <?php if (isset($_SESSION['user_id'])): ?>
            <a class="nav-link" href="?logout=true">Cerrar sesión</a>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </div>
</nav>