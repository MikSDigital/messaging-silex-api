<?php

require 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;

$app = new Silex\Application();
$app->before(function ($request, $app) {
    ChatterLogging::log($request, $app);
    ChatterAuth::authenticate($request, $app);
});

$app->get('/messages', function () use ($app) {
    $message = new Message();
    $messages = $message->all();

    $payload = [];
    foreach ($messages as $message) {
        $payload[$message->id] = [
            'body'       => $message->body,
            'user_id'    => $message->user_id,
            'created_at' => $message->created_at
        ];
    }

    return json_encode($payload);
});

$app->run();
