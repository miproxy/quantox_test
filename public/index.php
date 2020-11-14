<?php session_start();

ob_start();

require_once __DIR__ . "/../vendor/autoload.php";

App\Core\Token::create();
// start the application
$app = new App\Core\Application();
