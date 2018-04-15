<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

if (!in_array(getenv('SYMFONY_ENV'), ['dev'])) {
    if (!in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'], true)) {
        header('HTTP/1.0 403 Forbidden');
        die('You are not allowed to access this file.');
    }
}

require __DIR__.'/../vendor/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
