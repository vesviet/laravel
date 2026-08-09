<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
try {
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    echo "ERROR CAUGHT: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
}
