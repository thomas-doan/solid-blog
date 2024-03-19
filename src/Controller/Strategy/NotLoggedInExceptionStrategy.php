<?php

namespace App\Controller\Strategy;

class NotLoggedInExceptionStrategy implements ExceptionStrategy
{
    public function handle(\Exception $exception)
    {
        error_log($exception->getMessage());
        header('Location: /error/not_logged_in');
        exit();
    }
}