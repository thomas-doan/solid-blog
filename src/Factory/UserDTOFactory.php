<?php

namespace App\Factory;

use App\DTO\UserDTO;

class UserDTOFactory
{
    public function create(): UserDTO
    {
        return new UserDTO();
    }
}