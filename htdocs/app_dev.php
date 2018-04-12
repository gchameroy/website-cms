<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

$env = getenv('SYMFONY_ENV');
if (!in_array($env, ['dev', 'heroku'])) {
    if (!in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'], true)) {
        header('HTTP/1.0 403 Forbidden');
        die('You are not allowed to access this file.');
    }
}

require __DIR__.'/../vendor/autoload.php';
Debug::enable();

$kernel = new AppKernel($env, true);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
