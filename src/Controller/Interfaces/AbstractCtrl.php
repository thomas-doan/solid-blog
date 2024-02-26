<?php

namespace App\Controller\Interfaces;

interface AbstractCtrl
{
    public function render($view, $data = []);
}