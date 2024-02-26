<?php

namespace App\Service\User;
//Interface Segregation Principle

interface UserServiceInterface
{
    public function registerUser(array $userData);

    public function getUser(string $email, string $password);
}
