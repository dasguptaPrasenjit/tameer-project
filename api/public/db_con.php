<?php
//Get DB connection
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../'); // Dir where your .env file is located
$dotenv = $dotenv->load();

$db_host = $dotenv['DB_HOST'];
$db_name = $dotenv['DB_DATABASE'];
$db_username = $dotenv['DB_USERNAME'];
$db_password = $dotenv['DB_PASSWORD'];

// Create connection
$con = mysqli_connect($db_host, $db_username, $db_password,$db_name);
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
?>