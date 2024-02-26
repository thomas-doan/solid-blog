<?php

namespace App\Mapper;

use App\DTO\UserDTO;
use App\Model\User;

class UserMapper
{
    private User $user;
    private UserDTO $userDTO;

    public function __construct(User $user, UserDTO $userDTO)
    {
        $this->user = $user;
        $this->userDTO = $userDTO;
    }

    public function saveUser(UserDTO $userDTO): void
    {
        $user = $this->mapDTOToUser($userDTO);
        $user->setRole(['ROLE_USER']);
        $user->save();
    }

    public function emailExists(string $email): ?User
    {
        return $this->user->findOneByEmail($email);
    }

    public function mapDTOToUser(UserDTO $userDTO): User
    {
        $this->user->setEmail($userDTO->getEmail());
        $this->user->setPassword(password_hash($userDTO->getPassword(), PASSWORD_DEFAULT));
        if ($userDTO->getFirstname() !== null) {
            $this->user->setFirstname($userDTO->getFirstname());
        }
        if ($userDTO->getLastname() !== null) {
            $this->user->setLastname($userDTO->getLastname());
        }
        return $this->user;
    }

    public function mapUserToDTO(UserDTO $userDTO): UserDTO
    {
        $user = $this->user->findOneByEmail($userDTO->getEmail());

        $userDTO->setId($user->getId());
        $userDTO->setEmail($user->getEmail());
        $userDTO->setFirstname($user->getFirstname());
        $userDTO->setLastname($user->getLastname());
        $userDTO->setPassword($user->getPassword());
        $userDTO->setRole($user->getRole());
        return $userDTO;
    }
}