<?php

namespace App\Mapper;

use App\DTO\UserDTO;
use App\Model\User;

class UserMapper
{
    public function saveUser(UserDTO $userDTO): void
    {
        $user = $this->mapDTOToUser($userDTO);
        $user->setRole(['ROLE_USER']);
        $user->save();
    }

    public function emailExists(string $email): ?User
    {
        $user = new User();
        return $user->findOneByEmail($email);
    }

    public function mapDTOToUser(UserDTO $userDTO): User
    {
        $user = new User();
        $user->setEmail($userDTO->getEmail());
        $user->setPassword(password_hash($userDTO->getPassword(), PASSWORD_DEFAULT));
        if ($userDTO->getFirstname() !== null) {
            $user->setFirstname($userDTO->getFirstname());
        }
        if ($userDTO->getLastname() !== null) {
            $user->setLastname($userDTO->getLastname());
        }
        return $user;
    }

    public function mapUserToDTO(UserDTO $userDTO): UserDTO
    {
        $user = new User();
        $user = $user->findOneByEmail($userDTO->getEmail());

        $userDTO->setEmail($user->getEmail());
        $userDTO->setFirstname($user->getFirstname());
        $userDTO->setLastname($user->getLastname());
        $userDTO->setPassword($user->getPassword());
        $userDTO->setRole($user->getRole());
        return $userDTO;
    }
}
