<?php

require_once("../database/db.php");
require_once("../API/controllers/auth.controller.php");

$authController = new AuthController($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  //credenciales de administrador: 
  //user: admin
  //pass: admin
  $result = $authController->login($username, $password);

  if ($result["ok"]) {
    session_start(); // Iniciar la sesión aquí
    $_SESSION["user_id"] = $result["user_id"];
    $_SESSION["username"] = $result["username"];
    $_SESSION["id_rol"] = $result["id_rol"];
    header("Location: ../index.php");
    exit();
  } else {
    echo "<script>alert('".$result["message"]."')</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <title>FUSALMO - Inicio de sesion</title>
</head>

<body>
  <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div id="iniciodesesion" class="container-fluid" style="min-width: 50%; max-width: 70%;">
      <div class="p-4 mb-4 bg-light rounded-3 m-4">
        <div class="col">
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
                <a href="../pages/register.php">No tengo una cuenta</a>
                <button type="submit" class="btn btn-primary mt-4">Iniciar sesión</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</body>

</div>

</html>