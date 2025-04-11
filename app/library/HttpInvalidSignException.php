<?php

namespace App\library;

use Slim\Exception\HttpSpecializedException;

class HttpInvalidSignException extends HttpSpecializedException
{
    protected $code = 400;
    protected $message = 'Invalid sign.';
    protected string $title = '400 Invalid Sign';
    protected string $description = 'The json file signature is invalid or are missing.';
}
