<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$request = Request::capture();
try {
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (Throwable $e) {
    echo 'ERROR CAUGHT: '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine();
}
