<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../config.php';

$app = new \Slim\App(['config' => $config]);
$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer('../templates/');


$app->map(['GET', 'POST'], '/', function (Request $request, Response $response, array $args) {
    $information = "";

    if($data = $request->getParsedBody()) {
        $message = $data['message'];
        echo $message;
        $config = $this->get('config');
        error_log(json_encode($config));
        
        $client = new \GuzzleHttp\Client(['base_uri' => "https://api.nexmo.com/v0.1/messages"]);

        $apiResponse = $client->request('POST', '/v0.1/messages', [
            'headers' => [
                'Authorization' => 'Bearer ' . $config['jwt'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => [
                'from' => $config['from'],
                'to' => $config['customer1'][0],
                'message' => [
                    'content' => [
                        'type' => 'text',
                        'text' => $message
                    ]
                ]
            ]
        ]);

        echo $apiResponse->getStatusCode();
        echo $apiResponse->getBody();
    }

    $response = $this->view->render($response, 'index.html', ['information' => $information]);
    return $response;
});

$app->post('/status', function (Request $request, Response $response) {
    print_r($request->getParsedBody());
});
$app->post('/inbound', function (Request $request, Response $response) {
    print_r($request->getParsedBody());
});
$app->run();
