<?php

namespace App\Controller;

use App\Controller\Interfaces\AdminManageable;

class AdminController extends AbstractController implements AdminManageable
{
    public function admin($action, $entity, $id = null)
    {
        // Admin logic
    }
}
