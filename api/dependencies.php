<?php
use Api\Controller\ImageController;
use Api\Model\Image;

$container = $app->getContainer();


$container['ImageController'] = function ($container) {
    return new ImageController($container);
};

// $container['mongo'] = function () {
//    return new MongoDB\Client('mongodb://127.0.0.1:27017');
// };

$container['fr'] = function ($c) {
    return new Image($c);
};

// $container['organization'] = function ($container) {
//     return new Organization($container->get('mongo'));
// };
//
// $container['transaction'] = function ($container) {
//     return new Transaction($container->get('mongo'));
// };

// $container['logger'] = function ($c) {
//     $settings = $c->get('settings')['logger'];
//     $logger = new Monolog\Logger($settings['name']);
//     $logger->pushProcessor(new Monolog\Processor\UidProcessor());
//     $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
//     return $logger;
// };
