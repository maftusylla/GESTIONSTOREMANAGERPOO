<?php

declare(strict_types=1);
require_once __DIR__ . '/src/Core/Session.php';

Session::init();

require_once __DIR__ . '/src/Core/Router.php';

$router = new Router();
$router->dispatch();