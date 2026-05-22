<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Illuminate\Foundation\Application(
    dirname(__DIR__)
);

// Bind the configuration loader
$app->make(Illuminate\Foundation\Application::class)->useConfigPath($app->basePath('config'));

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;
