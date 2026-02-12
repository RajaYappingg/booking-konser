<?php
declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/config/config.php';
require BASE_PATH . '/app/core/helpers.php';
require BASE_PATH . '/app/core/Router.php';
require BASE_PATH . '/app/core/Controller.php';
require BASE_PATH . '/app/core/Model.php';
require BASE_PATH . '/app/core/Database.php';
require BASE_PATH . '/app/core/Validation.php';

// Auto-loader for models and controllers
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/app/models/' . $class . '.php',
        BASE_PATH . '/app/controllers/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
});

$router = new Router();
require BASE_PATH . '/app/routes/web.php';

$router->dispatch($_GET['url'] ?? '');
