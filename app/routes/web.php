<?php

use App\controllers\AppController;
use App\controllers\BingoController;
use App\controllers\TesteController;
use App\middlewares\OAuthMiddleware;
use App\controllers\UploadController;
use App\middlewares\JsonBodyParserMiddleware;
use App\middlewares\JsonBingoCartValidatorMiddleware;
use App\middlewares\JsonFileSignatureValidatorMiddleware;

// API
$app->get('/api/pix/txt', [TesteController::class, 'index'])->add(new OAuthMiddleware());
$app->get('/api/teste/one/{id}', [TesteController::class, 'teste']);
$app->post('/api/bingo/create', [BingoController::class, 'create'])
    ->add(new JsonBingoCartValidatorMiddleware)
    ->add(new JsonBodyParserMiddleware());
$app->post('/api/bingo/upload/json', [UploadController::class, 'uploadJson'])
    ->add(new JsonFileSignatureValidatorMiddleware());

// APP
$app->get('/app/bingo/upload', [AppController::class, 'bingoUpload']);
