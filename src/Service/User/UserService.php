<?php

namespace App\Service\User;

use App\Container\SimpleContainer;
use App\Exception\ValidationException;
use App\Factory\UserDTOFactory;
use App\Service\User\UserServiceInterface;
use App\DTO\UserDTO;
use App\Mapper\UserMapper;
use Exception;

class UserService implements UserServiceInterface
{
    private UserMapper $userMapper;
    private UserDTOFactory $userDtoFactory;

    private SimpleContainer $container;

    public function __construct(UserMapper $userMapper, UserDTOFactory $userDtoFactory, SimpleContainer $container)
    {
        $this->userMapper = $userMapper;
        $this->userDtoFactory = $userDtoFactory;
        $this->container = $container;
    }

    /**
     * @throws ValidationException|Exception
     */
    public function registerUser(array $userData)
    {
        try {
            $this->validateInputs($userData['email'], $userData['password'], $userData['confirmPassword'], $userData['firstname'], $userData['lastname']);
            $this->validateUniqueEmail($userData['email']);
            $userDTO = $this->userDtoFactory->create();
            $userDTO->setEmail($userData['email']);
            $userDTO->setPassword($userData['password']);
            $userDTO->setFirstname($userData['firstname']);
            $userDTO->setLastname($userData['lastname']);
            $this->userMapper->saveUser($userDTO);
        } catch (ValidationException $e) {
            throw new ValidationException($e->getMessage());
        }
    }

    public function getUser(string $email, string $password): UserDTO | ValidationException
    {
        $userDTO = $this->userDtoFactory->create();
        $userDTO->setEmail($email);
        $userDTO->setPassword($password);
        if ($this->userMapper->emailExists($email)) {
            $userDTO = $this->userMapper->mapUserToDTO($userDTO);
            if (password_verify($password, $userDTO->getPassword())) {
                return $userDTO;
            }
            throw new ValidationException("mot de passe incorrect");
        }
        throw new ValidationException("email inexistant");

    }
    /**
     * @throws ValidationException
     * @throws Exception
     */
    private function validateInputs($email, $password, $confirmPassword, $firstname, $lastname)
    {


        if (empty($email) || empty($password) || empty($confirmPassword) || empty($firstname) || empty($lastname)) {
            throw new ValidationException("Tous les champs sont obligatoires");

        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("L'email n'est pas valide");
        }

        if ($password !== $confirmPassword) {
            throw new ValidationException("Les mots de passe ne correspondent pas");
        }
    }

    private function validateUniqueEmail($email)
    {
        // Appeler le UserMapper pour vérifier l'unicité de l'email

        if ($this->userMapper->emailExists($email)) {
            throw new ValidationException("L'email existe déjà");
        }
    }
}
