<?php

namespace App\Controller\Strategy;
class NumericIdExceptionStrategy implements ExceptionStrategy
{
    public function handle(\Exception $exception)
    {
        error_log($exception->getMessage());
        header('Location: /error/invalid_id');
        exit();
    }
}