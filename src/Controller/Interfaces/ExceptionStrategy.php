<?php

namespace App\Controller\Interfaces;

interface ExceptionStrategy
{
public function handle(\Exception $exception);
}