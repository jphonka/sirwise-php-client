<?php

spl_autoload_register(function ($class) {
    $file = "/home/joona/sirwise-new/sirwise-php-client/lib/" . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
require("../vendor/autoload.php");

$httpClient = new \Guzzle\Http\Client();

$grant = new \SirWise\Oauth2\GrantType\PasswordCredentials($httpClient, [
    'token_url' => 'https://api.sirwise.com/v1/token',
    'client_id' => '565a20f1424ff387da225600',
    'username' => 'username',
    'password' => 'password',
    'tenant' => 'mcare',
]);

$refresh = new \SirWise\Oauth2\GrantType\RefreshToken($httpClient, [
    'token_url' => 'https://api.sirwise.com/v1/token',
    'client_id' => '565a20f1424ff387da225600',
    'tenant' => 'mcare',
]);

$client = new \SirWise\Client();
$client->setTenant('mcare');
$client->authenticate($grant, $refresh);

/** @var \SirWise\Api\CurrentUser $meApi */
$meApi = $client->api('me');
$user = $meApi->show();

/** @var \SirWise\Api\Order $orderApi */
$orderApi = $client->api('order');
$order = $orderApi->create(
    [
        'contact' => [
            'firstName' => 'Test',
            'lastName' => 'Contact',
            'email' => 'testmail@test.test',
        ],
    ]
);
echo(json_encode($order, JSON_PRETTY_PRINT));

$order = $orderApi->edit($order->id,
    [
        'reference' => 'test order',
        'handledBy' => $user->id,
    ],
    [
        'expand' => 'handledBy(emails)'
    ]);
echo(json_encode($order, JSON_PRETTY_PRINT));
