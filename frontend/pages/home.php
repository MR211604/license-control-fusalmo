<?php

// Verificar si el usuario está autenticado
if (!isset($_SESSION["user_id"])) {
  // Redirigir usando el parámetro page en lugar de cambiar la URL completa
  header("Location: index.php?page=login");
  exit();
}


?>

<!-- Titulo -->
<div class="container text-center">
  <h1 class="display-4">Bienvenido <strong><?php echo $_SESSION["username"] ?></strong></h1>
  <h1>Administración de licencias de FUSALMO</h1>
</div>
<!-- Formulario de licencias -->

<div class="container">
  <div class="row">
    <div class="col-md-6">
      <form action="">
        <div class="form-group">
          <label for="license_id">ID: </label>
          <input type="text" class="form-control" id="license_id" name="license_id" placeholder="license_id">
        </div>
        <div class="form-group">
          <label for="platform">Plataforma: </label>
          <input type="text" class="form-control" id="platform" name="platform" placeholder="platform">
        </div>
        <div class="form-group">
          <label for="email">Correo: </label>
          <input type="text" class="form-control" id="email" name="email" placeholder="email">
        </div>
        <div class="form-group">
          <label for="password">Contraseña: </label>
          <input type="text" class="form-control" id="password" name="password" placeholder="password">
        </div>

        <div class="form-row">
          <label for="buy_date">Fecha de compra: </label>
          <input type="date" class="form-control" id="buy_date" name="buy_date" placeholder="buy_date">
        </div>

        <div class="form-row">
          <label for="cease_date">Fecha de suspension: </label>
          <input type="date" class="form-control" id="cease_date" name="cease_date" placeholder="cease_date">
        </div>

        <div class="form-row">
          <label for="expire_date">Fecha de vencimiento: </label>
          <input type="date" class="form-control" id="expire_date" name="expire_date" placeholder="expire_date">
        </div>

      </form>
    </div>
  </div>

  <!-- Tabla de licencias -->
  <table class="table table-bordered table-light">
    <thead class="table-active">
      <tr>
        <th>ID</th>
        <th>Plataforma</th>
        <th>Correo</th>
        <th>Contraseña</th>
        <th>Fecha de compra</th>
        <th>Fecha de suspension</th>
        <th>Fecha de vencimiento</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($licenses as $license) ?>
      <tr>
        <td><?php echo $license['id'] ?></td>
        <td><?php echo $license['plataforma'] ?></td>
        <td><?php echo $license['correo'] ?></td>
        <td><?php echo $license['contrasena'] ?></td>
        <td><?php echo $license['fecha_de_compra'] ?></td>
        <td><?php echo $license['fecha_de_suspension'] ?></td>
        <td><?php echo $license['fecha_de_renovacion'] ?></td>
        <td><?php echo $license['fecha_de_vencimiento'] ?></td>
        <td>
          <form method="post">
            <input type="hidden" name="license_id" id="license_id" value="<?php echo $license['id']; ?>" />
            <input type="submit" name="accion" value="Enviar correo" class="btn btn-primary" />
            <input type="submit" name="accion" value="Borrar" class="btn btn-danger" />
          </form>
        </td>
      </tr>
    </tbody>
  </table>
</div>