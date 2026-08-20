<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine the base Laravel application directory
// Support both standard structure and cPanel public_html/laravel_app structure
$baseDir = file_exists(__DIR__.'/../laravel_app/vendor/autoload.php')
    ? __DIR__.'/../laravel_app'
    : __DIR__.'/..';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $baseDir.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $baseDir.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $baseDir.'/bootstrap/app.php';

// If running in public_html, bind public path properly
if (basename(__DIR__) === 'public_html') {
    $app->usePublicPath(__DIR__);
}

$app->handleRequest(Request::capture());

