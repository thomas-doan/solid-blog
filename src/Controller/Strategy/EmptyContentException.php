<?php

namespace App\Controller\Strategy;



use Exception;

class EmptyContentException extends Exception
{
    public function handle(\Exception $exception)
    {
        file_put_contents(__DIR__ . '/../../log_error/numeric_id_error.log', $exception->getMessage() . PHP_EOL, FILE_APPEND);
        header('Location: /error/invalid_id');
        exit();
    }
}