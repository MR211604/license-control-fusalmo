<?php

// session_start();

if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: ../frontend/index.php?page=login");
  exit();
}

?>

<style>
  /* Estilo para centrar elementos del menú solo cuando está colapsado */
  @media (max-width: 991.98px) {
    .navbar-collapse .navbar-nav {
      text-align: center;
      width: 100%;
      margin-left: auto;
      margin-right: auto;      
    }
  }
</style>

<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
  <div class="container-fluid">
    <!-- <a class="navbar-brand">FUSALMO - Control de licencias</a> -->
    <a class="brand" href="https://fusalmo.org/" aria-label="FUSALMO" rel="home"></a>
    <img fetchpriority="high" width="250" height="60" src="https://fusalmo.org/wp-content/uploads/2024/07/LOGO-FUSALMO2.svg" class="neve-site-logo skip-lazy" alt="" data-variant="logo" decoding="async">
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