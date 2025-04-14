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
    <ul class="nav navbar-nav">

      <a class="navbar-brand" href="./index.php">Inicio</a>

      <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
        <a class="navbar-brand" href="index.php?page=admin/users">Usuarios</a>
      <?php endif; ?>

      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="?logout=true">Cerrar sesión</a>
      <?php endif; ?>

      <li class="nav-item">
        <a class="nav-link" href="https://fusalmo.org/salesianos/?view=aboutus&about=fusalmo" target="_blank">¿Quiénes somos?</a>
      </li>
    </ul>
  </div>
</nav>