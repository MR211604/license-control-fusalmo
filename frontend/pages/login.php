<?php

require __DIR__ . '/../utils/api-url.php';

// Procesar el formulario si se envió
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  //Haciendo la peticion HTTP hacia /auth/login
  $data = [
    'username' => $_POST['username'],
    'password' => $_POST['password']
  ];

  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . '/auth/login');
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
      $userData = json_decode($response, true);

      if ($userData && isset($userData['user'])) {
        $_SESSION["user_id"] = $userData['user']['id'];
        $_SESSION["username"] = $userData['user']['username'];
        $_SESSION["id_rol"] = $userData['user']['id_rol'];

        header("Location: index.php?page=home");
        exit();
      } else {
        $error_message = "Error al obtener los datos del usuario.";
      }
    } else {
      $responseData = json_decode($response, true);
      if ($responseData && isset($responseData['error'])) {
        $error_message = $responseData['error'];
      } else {
        $error_message = "Error desconocido al iniciar sesión." . $httpCode;
      }
    }
  } catch (Exception $e) {
    $error_message = "Error al procesar la solicitud: " . $e->getMessage();
  }
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
                <input type="password" class="form-control" name="password" placeholder="Ingresar usuario">
              </div>
              <button type="submit" class="btn btn-primary mt-4">Iniciar sesión</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>