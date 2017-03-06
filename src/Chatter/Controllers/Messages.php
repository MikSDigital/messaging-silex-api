<?php

namespace Chatter\Controllers;

use Chatter\Models\Message;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Messages
{
    public function getAll(Request $request, Application $app)
    {
        $message  = new Message();
        $messages = $message->all();

        $payload = [];
        foreach ($messages as $message) {
            $payload[$message->id] = $message->output();
        }

        return $app->json($payload);
    }

    public function createAction(Request $request, Application $app)
    {
        $message            = new Message();
        $message->body      = $request->get('message');
        $message->user_id   = -1;
        $message->image_url = $request->headers->get('filepath');
        $message->save();

        $code    = 400;
        $payload = [];

        if ($message->id) {
            $code    = 201;
            $payload = $message->output();
        }

        return $app->json($payload, $code);
    }

    public function deleteAction(Request $request, Application $app, $id)
    {
        $message = Message::find($id);
        $message->delete();

        if ($message->exists) {
            return new Response('', 400);
        }

        return new Response('', 204);
    }
}
