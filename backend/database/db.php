<?php

use React\MySQL\Factory;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$factory = new Factory();

// Construir la cadena de conexión usando variables de entorno
$connectionString = sprintf(
    '%s:%s@%s/%s',
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD'],
    $_ENV['HOSTNAME'],
    $_ENV['DB_NAME']
);

$connection = $factory->createLazyConnection($connectionString);
$connection->on(
  'error',
  function (Exception $error) {
    echo 'Error: ' . $error->getMessage() . PHP_EOL;
  }
);

return $connection;
