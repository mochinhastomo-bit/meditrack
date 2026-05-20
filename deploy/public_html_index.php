<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Path ke folder meditrack di luar public_html
// Sesuaikan dengan path absolut di server Anda
// Contoh: /home/username/meditrack
define('LARAVEL_ROOT', dirname(__DIR__) . '/meditrack');

if (file_exists($maintenance = LARAVEL_ROOT . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require LARAVEL_ROOT . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once LARAVEL_ROOT . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
