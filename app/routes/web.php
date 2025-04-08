<?php

use App\controllers\BingoController;
use App\controllers\TesteController;
use App\middlewares\JsonBingoCartValidatorMiddleware;
use App\middlewares\OAuthMiddleware;
use App\middlewares\JsonBodyParserMiddleware;

$app->get('/api/pix/txt', [TesteController::class, 'index'])->add(new OAuthMiddleware());
$app->get('/api/teste/one/{id}', [TesteController::class, 'teste']);
$app->post('/api/bingo/create', [BingoController::class, 'create'])
    ->add(new JsonBingoCartValidatorMiddleware)
    ->add(new JsonBodyParserMiddleware());
