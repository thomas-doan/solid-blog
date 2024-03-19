<?php

namespace App\Controller\Strategy;
class ExceptionHandler
{
    private array $strategies;

    public function __construct()
    {
        $this->strategies = [
            'NumericIdException' => new NumericIdExceptionStrategy(),
            'EmptyContentException' => new EmptyContentExceptionStrategy(),
            'NotLoggedInException' => new NotLoggedInExceptionStrategy(),
        ];
    }

    public function handle(\Exception $exception)
    {
        $exceptionName = get_class($exception);
        if (isset($this->strategies[$exceptionName])) {
            $this->strategies[$exceptionName]->handle($exception);
        } else {
            error_log($exception->getMessage());
            header('Location: /error/default');
            exit();
        }
    }
}