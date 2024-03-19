<?php

namespace App\Controller\Strategy;

class EmptyContentExceptionStrategy implements ExceptionStrategy
{
    public function handle(\Exception $exception)
    {
        error_log($exception->getMessage());
        header('Location: /error/empty_content');
        exit();
    }
}