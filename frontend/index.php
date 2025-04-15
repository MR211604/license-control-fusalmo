<?php

// Iniciar sesión al principio
session_start();

// Determinar qué página mostrar
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Lista de páginas válidas
$allowed_pages = ['home', 'login', 'register','licenses/addLicense', 'licenses/editLicense', 'admin/users', 'admin/addUser', 'admin/editUser'];

// Validar la página
if (!in_array($page, $allowed_pages)) {
  $page = 'home';
}

// Determinar la ruta completa del archivo a incluir
$page_path = "./pages/{$page}.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FUSALMO - Control de licencias</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

  <?php if (isset($_SESSION["user_id"]) || $page == 'login'): ?>
    <?php include('./components/header.php'); ?>
  <?php endif; ?>

  <!-- main component -->
  <main class="container my-4">
    <?php 
    if (file_exists($page_path)) {
      include($page_path);
    } else {
      echo "<div class='alert alert-danger'>La página solicitada no existe.</div>";
    }
    ?>
  </main>

  <!-- footer component -->
  <footer>
    <?php include('./components/footer.php'); ?>
  </footer>
</body>

</html>