<?php

namespace App\Controller\Interfaces;

use App\DTO\UserDTO;

interface UserAuthenticatable
{
    public function registerUser(string $email, string $password, string $confirmPassword, string $firstname, string $lastname): void;

    public function loginUser(string $email, string $password): void;

    public function logoutUser(): void;

    public function profile(): void;

    public function validateUserDTO(UserDTO $userDTO): void;
}