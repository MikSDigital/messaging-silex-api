<?php

use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;
use Silex\Application;

$app = new Application();

$app->before(function ($request, $app) {
    ChatterLogging::log($request, $app);
    ChatterAuth::authenticate($request, $app);
});

return $app;