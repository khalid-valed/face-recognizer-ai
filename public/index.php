<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}
if ($_SERVER['REQUEST_URI'] == '/docs/') {
    include './docs/index.html';
    die();
}
define('ROOT_DIR', dirname(__DIR__, 1));
require __DIR__ . '/../vendor/autoload.php';
// Instantiate the app
$settings = require __DIR__ . '/../api/settings.php';
$app = new \Slim\App($settings);


// Set up dependencies
require __DIR__ . '/../api/dependencies.php';
// Register middleware
unset($app->getContainer()['notFoundHandler']);
$app->getContainer()['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $response = new \Slim\Http\Response(400);
        return $response->withJson(array("err"=>'not found'));
    };
};
require __DIR__ . '/../api/middleware.php';
// Register routes
require __DIR__ . '/../api/routes.php';
// Run app
$app->run();
