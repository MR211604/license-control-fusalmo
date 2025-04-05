<?php

// session_start();

if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: ./pages/login.php");
  exit();
}

?>
<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
  <div class="container-fluid">
    <ul class="nav navbar-nav">
      <a class="navbar-brand" href="./index.php">Inicio</a>
      
      <a href="?logout=true">Cerrar sesión</a>
      <li class="nav-item">
        <a class="nav-link" href="https://fusalmo.org/salesianos/?view=aboutus&about=fusalmo" target="_blank">¿Quiénes somos?</a>
      </li>
    </ul>
  </div>
</nav>