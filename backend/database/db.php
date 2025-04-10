<?php

use React\MySQL\Factory;

$factory = new Factory();

$connection = $factory->createLazyConnection('root:root@localhost/sitio_licencias_lis');
$connection->on(
  'error',
  function (Exception $error) {
    echo 'Error: ' . $error->getMessage() . PHP_EOL;
  }
);

return $connection;
