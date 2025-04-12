<?php


use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use Sikei\React\Http\Middleware\CorsMiddleware;

require 'vendor/autoload.php';
require 'database/db.php';
require './lib/router.php';
require './controllers/license.controller.php';
require './controllers/users.controller.php';
require './controllers/auth.controller.php';

$r = new RouteCollector(new Std(), new GroupCountBased());

$licenseController = new LicenseController($connection);
$userController = new UserController($connection);
$authController = new AuthController($connection);

//Authentication
$r->addGroup('/auth', function (RouteCollector $r) use ($authController) {
  $r->addRoute('POST', '/login',[$authController, 'login']);
});

//Licenses
$r->addGroup('/license', function (RouteCollector $r) use ($licenseController) {
  $r->addRoute('GET', '/getAll', [$licenseController, 'getLicenses']);
  $r->addRoute('GET', '/{id:\d+}', [$licenseController, 'getLicenseById']);
  $r->addRoute('POST', '/create', [$licenseController, 'createLicense']);
  $r->addRoute('PUT', '/update/{id:\d+}', [$licenseController, 'updateLicenseById']);
  $r->addRoute('DELETE', '/delete/{id:\d+}', [$licenseController, 'delete']);
});

//Users
$r->addGroup('/users', function(RouteCollector $r) use ($userController) {
  $r->addRoute('GET', '/getAll', [$userController, 'getUsers']);
  $r->addRoute('GET', '/{id:\d+}', [$userController, 'getUserById']);
  $r->addRoute('POST', '/create', [$userController, 'createUser']);
  $r->addRoute('PUT', '/update/{id:\d+}', [$userController, 'updateUserById']);
  // $r->addRoute('DELETE', '/delete/{id:\d+}', [$licenseController, 'delete']);
});


$http = new HttpServer(new CorsMiddleware(['allow_origin' => ['*'],]), new Router($r));
$socket = new React\Socket\SocketServer('127.0.0.1:8080');
$http->listen($socket);

$http->on(
  'error',
  function (Exception $error) {
    echo 'Error: ' . $error->getMessage() . PHP_EOL;
  }
);

echo "Server running at http://127.0.0.1:8080" . PHP_EOL;
