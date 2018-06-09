<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

$env = $_SERVER['APP_ENV'] ?? 'dev';
if ($env != 'dev') {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file.');
}

require __DIR__ . '/../vendor/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
