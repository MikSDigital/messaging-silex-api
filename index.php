<?php

require 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

$app->post('/messages', function (Request $request) use ($app) {
    $body = $request->get('message');

    $message = new Message();
    $message->body = $body;
    $message->user_id = -1;
    $message->save();

    $code    = 400;
    $payload = [];

    if ($message->id) {
        $code    = 201;
        $payload = [
            'message_id'  => $message->id,
            'message_uri' => '/messages/' . $message->id,
        ];
    }

    return $app->json($payload, $code);
});

$app->delete('/messages/{id}', function ($id) {
    $message = Message::find($id);
    $message->delete();

    if ($message->exists) {
        return new Response('', 400);
    }

    return new Response('', 204);
});

$app->run();
