<?php

use App\controllers\TesteController;
use App\middlewares\OAuthMiddleware;

$app->get('/api/pix/txt', [TesteController::class, 'index'])->add(new OAuthMiddleware());
$app->get('/api/teste/one/{id}', [TesteController::class, 'teste']);
