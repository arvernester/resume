<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$config = [
    'app' => [
        'name' => 'Online Resume',
        'author' => 'Dedy Yugo Purwanto',
        'email' => 'dedy.yugo.purwanto@gmail.com',
        'website' => 'http://yugo.my,id'
    ],
    'mashape' => [
        'quote' => [
            'key' => ''
        ]
    ]
];

$app = new \Slim\App($config);

// get container
$container = $app->getContainer();

// twig view
$container['view'] = function($container) {
    $view = new \Slim\Views\Twig('resources/views', [
        'cache' => 'resources/cache',
        'autoreload' => true
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

$app->get('/', function(Request $request, Response $response) use($container) {
    return $this->view->render($response, 'index.html', [
        'name' => $container->get('app')['name'],
        'email' => $container->get('app')['email'],
        'version' => $container->get('settings')['httpVersion']
    ]);
})->setName('index');

$app->get('/quote', function(Request $request, Response $response){
    $quote = [
        'quote' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
        'author' => 'Anonymous'
    ];

    $jsonResponse = $response->withJson($quote);

    return $jsonResponse;
})->setName('quote');

$app->run();
