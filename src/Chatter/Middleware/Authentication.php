<?php

namespace Chatter\Middleware;

use Chatter\Models\User;

class Authentication
{
    public static function authenticate($request, $app)
    {
        $auth   = $request->headers->get('Authorization');

        $apiKey = trim(substr($auth, strpos($auth, ' ')));

        $user = new User();
        if (!$user->authenticate($apiKey)) {
            $app->abort(401);
        }
    }
}
