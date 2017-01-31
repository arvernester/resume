<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use GuzzleHttp\Client;

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
            'base_uri' => 'https://andruxnet-random-famous-quotes.p.mashape.com/',
            'key' => ''
        ]
    ],
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$setContainer = new \Slim\Container($config);
$app = new \Slim\App($setContainer);

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
    // $app->config('debug', true);

    $client = new Client;

    $quote = $client->request('GET', $container->get('mashape')['quote']['base_uri'], [
        'query' => [
            'cat' => 'movies'
        ],
        'headers' => [
            'X-Mashape-Key' => $container->get('mashape')['quote']['key']
        ],
    ]);

    if ($quote->getStatusCode() == 200) {
        $randomQuote = json_decode($quote->getBody()->getContents());
    }

    // return view
    return $this->view->render($response, 'index.html', [
        'name' => $container->get('app')['name'],
        'author' => $container->get('app')['author'],
        'email' => $container->get('app')['email'],
        'version' => $container->get('settings')['httpVersion'],
        'quote' => isset($randomQuote) ? $randomQuote : null
    ]);
})->setName('index');

$app->run();
