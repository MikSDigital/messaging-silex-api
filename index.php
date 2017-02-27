<?php

require 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;
use Chatter\Middleware\File\Filter as FileFilter;
use Chatter\Middleware\File\ImageRemoveExif;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Application();
$app['debug'] = true;
$app->before(function ($request, $app) {
    ChatterLogging::log($request, $app);
    ChatterAuth::authenticate($request, $app);
});

$filter = function(Request $request, Application $app) {
    try {
        $fileFilter = new FileFilter();
        $filePath   = $fileFilter->filter($request->files->get('file'));
        $request->headers->set('filepath', $filePath);
    } catch (\Exception $e) {
        $app->abort(415);
    }
    
};

$removeExif = function(Request $request, Application $app) {
    $filePath = ImageRemoveExif::removeExif($request->headers->get('filepath'));
    $request->headers->set('filepath', $filePath);
};

$app->get('/messages', function () use ($app) {
    $message = new Message();
    $messages = $message->all();

    $payload = [];
    foreach ($messages as $message) {
        $payload[$message->id] = [
            'body'        => $message->body,
            'user_id'     => $message->user_id,
            'user_uri'    => '/users/' . $message->user_id,
            'image_url'   => $message->image_url,
            'message_id'  => $message->id,
            'message_uri' => '/messages/' . $message->id,
            'created_at'  => $message->created_at
        ];
    }

    return json_encode($payload);
});

$app->post('/messages', function (Request $request) use ($app) {
    $message            = new Message();
    $message->body      = $request->get('message');
    $message->user_id   = -1;
    $message->image_url = $request->headers->get('filepath');
    $message->save();

    $code    = 400;
    $payload = [];

    if ($message->id) {
        $code    = 201;
        $payload = [
            'message_id'  => $message->id,
            'message_uri' => '/messages/' . $message->id,
            'image_url'   => $message->image_url,
        ];
    }

    return $app->json($payload, $code);
})
    ->before($filter)
    ->before($removeExif)
;

$app->delete('/messages/{id}', function ($id) {
    $message = Message::find($id);
    $message->delete();

    if ($message->exists) {
        return new Response('', 400);
    }

    return new Response('', 204);
});

$app->run();
