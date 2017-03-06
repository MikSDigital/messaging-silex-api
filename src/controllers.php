<?php

use Chatter\Middleware\File\Filter as FileFilter;
use Chatter\Middleware\File\ImageRemoveExif;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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

$app->get('/messages', 'Chatter\\Controllers\\Messages::getAll');

$app->post('/messages', 'Chatter\\Controllers\\Messages::createAction')
    ->before($filter)
    ->before($removeExif)
;

$app->delete('/messages/{id}', 'Chatter\\Controllers\\Messages::deleteAction');
