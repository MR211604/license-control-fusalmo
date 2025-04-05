<?php

$env = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/FUSALMO-licencias/backend/.env');

$hostname = $env["HOSTNAME"];
$dbuser = $env["DB_USER"];
$dbpassword = $env["DB_PASSWORD"];
$dbname = $env["DB_NAME"];

$conn = mysqli_connect($hostname, $dbuser, $dbpassword, $dbname);

if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}
