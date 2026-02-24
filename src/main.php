<?php

declare(strict_types=1);

use Progphil1337\Config\Config;
use ProgPhil1337\DependencyInjection\ClassLookup;
use ProgPhil1337\DependencyInjection\Injector;
use ProgPhil1337\SimpleReactApp\App;
use ProgPhil1337\SimpleReactApp\HTTP\Request\Pipeline\DefaultRequestPipelineHandler;
use ProgPhil1337\SimpleReactApp\HTTP\Request\Pipeline\RoutingPipelineHandler;

require_once 'vendor/autoload.php';

const PROJECT_PATH = __DIR__;

$config = Config::create(__DIR__ . '/config.json');

$classLookup = (new ClassLookup())
    ->singleton($config)
    ->singleton(Injector::class)
    ->register($config);

$container = new Injector($classLookup);

$app = new App($config, $container);

return $app->run([
    RoutingPipelineHandler::class,
    DefaultRequestPipelineHandler::class
]);
