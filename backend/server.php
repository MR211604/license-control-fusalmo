<?php

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use React\Http\HttpServer;
use Sikei\React\Http\Middleware\CorsMiddleware;

require 'vendor/autoload.php';
require 'database/db.php';
require './lib/router.php';
require './controllers/license.controller.php';
require './controllers/users.controller.php';
require './controllers/auth.controller.php';
require './services/mail.php';


$r = new RouteCollector(new Std(), new GroupCountBased());

$licenseController = new LicenseController($connection);
$userController = new UserController($connection);
$authController = new AuthController($connection);
$emailService = new SendEmail();

//Authentication
$r->addGroup('/auth', function (RouteCollector $r) use ($authController) {
  $r->addRoute('POST', '/login', [$authController, 'login']);
});

//Licenses
$r->addGroup('/license', function (RouteCollector $r) use ($licenseController) {
  $r->addRoute('GET', '/getAll', [$licenseController, 'getLicenses']);
  $r->addRoute('GET', '/{id:\d+}', [$licenseController, 'getLicenseById']);
  $r->addRoute('POST', '/create', [$licenseController, 'createLicense']);
  $r->addRoute('PUT', '/update/{id:\d+}', [$licenseController, 'updateLicenseById']);
  $r->addRoute('POST', '/renovate/{id:\d+}', [$licenseController, 'renovateLicenseById']);
  $r->addRoute('POST', '/suspend/{id:\d+}', [$licenseController, 'suspendLicenseById']);
});

//Users
$r->addGroup('/user', function (RouteCollector $r) use ($userController) {  
  $r->addRoute('GET', '/getAll', [$userController, 'getUsers']);
  $r->addRoute('GET', '/{id:\d+}', [$userController, 'getUserById']);
  $r->addRoute('POST', '/create', [$userController, 'createUser']);
  $r->addRoute('PUT', '/update/{id:\d+}', [$userController, 'updateUserById']);
  $r->addRoute('POST', '/disable/{id:\d+}', [$userController, 'disableUser']);
  $r->addRoute('POST', '/enable/{id:\d+}', [$userController, 'enableUser']);
});

//Email sending
$r->addGroup('/email', function (RouteCollector $r) use ($emailService) {
  $r->addRoute('POST', '/send', [$emailService, 'sendEmail']);
});


$http = new HttpServer(new CorsMiddleware(['allow_origin' => ['*'],]), new Router($r));
$socket = new React\Socket\SocketServer('0.0.0.0:8080');
$http->listen($socket);

$http->on(
  'error',
  function (Exception $error) {
    echo 'Error: ' . $error->getMessage() . PHP_EOL;
  }
);

echo "Server running at http://0.0.0.0:8080" . PHP_EOL;
