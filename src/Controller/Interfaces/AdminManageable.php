<?php

namespace App\Controller\Interfaces;

interface AdminManageable
{
    public function admin($action, $entity, $id = null);
}
