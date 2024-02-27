<?php

namespace App\Controller;

use App\Container\SimpleContainer;
use App\DTO\UserDTO;
use App\Exception\ValidationException;
use App\Mapper\UserMapper;
use App\Service\User\UserServiceInterface;
use Exception;

// Principe de ségrégation des interfaces
// Dependency Inversion Principle

class UserController extends AbstractController
{
    private UserServiceInterface $userService;
    private SimpleContainer $container;

    public function __construct(UserServiceInterface $userService, SimpleContainer $container)
    {
        $this->userService = $userService;
        $this->container = $container;
    }

    public static function getUser()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        } else {
            return null;
        }
    }

    /**
     * @throws Exception
     */
    public function registerUser(array $userData)
    {
        try {
            $this->userService->registerUser(
                $userData
            );
            $this->redirect('login');
        } catch (ValidationException $e) {
            $this->render('register', ['error' => $e->getMessage()]);
        }
    }


    /**
     * @throws ValidationException
     */
    public function loginUser(string $email, string $password): void
    {
        try {
            $this->userService->getUser($email, $password);
            $_SESSION['user'] = $this->userService->getUser($email, $password);
            $this->redirect('home');
        } catch (ValidationException $e) {
            $this->render('login', ['error' => $e->getMessage()]);
        }

    }

    public function logoutUser(): void
    {
        unset($_SESSION['user']);
        $this->redirect('home');
    }

    public function profile(): void
    {
        $user = new User();
        if (self::getUser() === null) {
            $this->redirect('login');

            return;
        }
        $user = $user->findOneById($_SESSION['user']->getId());
        if ($user) {
            $user->setPassword('');
            $this->render('profile', ['user' => $user]);

            return;
        }

        $this->redirect('login');

        return;
    }

}
