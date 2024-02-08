<?php

namespace App\Container;



use App\Container\ContainerInterface;
use App\Container\NotFoundExceptionInterface;
use App\Container\ContainerExceptionInterface;
use App\Factory\UserDTOFactory;
use App\Mapper\UserMapper;
use App\Service\User\UserService;
use App\Service\User\UserServiceInterface;

class SimpleContainer implements ContainerInterface
{
    private array $entries = [];
    private array $resolved = [];

    public function __construct()
    {
        $this->entries = [
            UserDTOFactory::class => function() {
                return new UserDTOFactory();
            },
            UserMapper::class => function() {
                return new UserMapper();
            },
            UserService::class => function($container) {
                return new UserService(
                    $container->get(UserMapper::class),
                    $container->get(UserDTOFactory::class)
                );
            },

        ];
    }

    public function get($classEntity)
    {
        if (!$this->has($classEntity)) {
            throw new class extends \Exception implements NotFoundExceptionInterface {};
        }

        // Retourner l'instance résolue si elle existe
        if (isset($this->resolved[$classEntity])) {
            return $this->resolved[$classEntity];
        }

        $resolver = $this->entries[$classEntity];
        // Résoudre l'instance et la stocker
        $instance = $resolver($this);
        $this->resolved[$classEntity] = $instance;

        return $instance;
    }

    public function has($classEntity): bool
    {
        return isset($this->entries[$classEntity]);
    }

    public function set($classEntity, callable $resolver): void
    {
        $this->entries[$classEntity] = $resolver;
    }
}
