<?php

include('./components/header.php');

require_once('./database/db.php');
require_once('./API/controllers/users.controller.php');

$usersController = new usersController($conn);

$users = $usersController->getUsers();

// //Obtiendo los datos del formulario
// $stmt = $conn->prepare("SELECT id, nombre_usuario, contrasena, id_rol FROM usuario");
// $stmt->execute();
// $result = $stmt->get_result();

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
          <?php foreach ($users as $user) {  ?>

            <tr>
              <td><?php echo $user['id_usuario'] ?></td>
              <td><?php echo $user['nombre_usuario'] ?></td>
              <td><?php echo $user['contrasena'] ?></td>
              <td><?php echo $user['id_rol'] ?></td>

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