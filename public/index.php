<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../config.php';

$app = new \Slim\App(['config' => $config]);
$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer('../templates/');

// allow us to handle errors ourselves
unset($app->getContainer()['errorHandler']);

$app->map(['GET', 'POST'], '/', function (Request $request, Response $response, array $args) {
    $config = $this->get('config');
    $information = [];
    $title = "Cupcake Bakery Customer Messaging";

    if($data = $request->getParsedBody()) {
        $message = $data['message'];
        
        $client = new \GuzzleHttp\Client(['base_uri' => "https://api.nexmo.com/v0.1/messages"]);

        try{
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

            $information['statusCode'] = $apiResponse->getStatusCode();
            $information['body'] = $apiResponse->getBody();
        } catch (Exception $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            echo $responseBodyAsString;
            error_log($responseBodyAsString);
        }

    }

    $response = $this->view->render($response, 'index.html', ['information' => $information, 'title' => $title]);
    return $response;
});

$app->map(['GET', 'POST'], '/message-with-dispatch', function (Request $request, Response $response, array $args) {
    $config = $this->get('config');
    $information = [];
    $title = "Cupcake Bakery Customer Messaging With Fallback";

    if($data = $request->getParsedBody()) {
        $message = $data['message'];
        
        $client = new \GuzzleHttp\Client(['base_uri' => "https://api.nexmo.com/v0.1/messages"]);

        try{
            $apiResponse = $client->request('POST', '/v0.1/dispatch', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $config['jwt'],
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'template' => 'failover',
                    'workflow' => [
                        [
                            'from' => $config['from'],
                            'to' => $config['customer1'][0],
                            'message' => [
                                'content' => [
                                    'type' => 'text',
                                    'text' => $message
                                ]
                            ],
                            'failover' => [
                                'expiry_time' => 15, // in seconds, 15 is the minimum
                                'condition_status' => 'delivered' // some platforms offer "read" as well
                            ]
                        ],
                        [
                            'from' => $config['from'],
                            'to' => $config['customer1'][1],
                            'message' => [
                                'content' => [
                                    'type' => 'text',
                                    'text' => 'Message retry. ' . $message
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $information['statusCode'] = $apiResponse->getStatusCode();
            $information['body'] = $apiResponse->getBody();
        } catch (Exception $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            echo $responseBodyAsString;
            error_log($responseBodyAsString);
        }

    }

    $response = $this->view->render($response, 'index.html', ['information' => $information, 'title' => $title]);
    return $response;
});

$app->post('/status', function (Request $request, Response $response) {
    print_r($request->getParsedBody());
});
$app->post('/inbound', function (Request $request, Response $response) {
    print_r($request->getParsedBody());
});
$app->run();
