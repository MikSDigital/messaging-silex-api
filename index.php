<?php

require 'vendor/autoload.php';
include 'bootstrap.php';

$env = getenv('env') ?: 'dev';

$app = require __DIR__ . '/src/app.php';
require __DIR__.'/config/' . $env . '.php';
require __DIR__ . '/src/controllers.php';
$app->run();
