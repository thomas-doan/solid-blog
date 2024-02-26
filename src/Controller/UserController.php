<?php

namespace App\Controller;

use App\Container\SimpleContainer;
use App\DTO\UserDTO;
use App\Exception\ValidationException;
use App\Factory\UserDTOFactory;
use App\Mapper\UserMapper;
use App\Model\User;
use App\Service\User\UserServiceInterface;
use Exception;

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

        if (!$_SESSION['user']) {
            $this->redirect('login');
        }
    $user = $_SESSION['user'];
            $user->setPassword('');
            $this->render('profile', ['user' => $user]);







    }

}