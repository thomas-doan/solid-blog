<?php

use App\Class\Database;
use App\Controller\UserController;
use App\Core\Database\DatabaseManager;
use App\Core\DataTransferObjectManager\DTOInterface;
use App\Core\DataTransferObjectManager\DTOManager;
use App\Core\EntityManager\EntityManager;
use App\Router\Router;
use App\DevTools\EchoDebug;
use App\DTO\UserDTO;

class updatePostDTO implements DTOInterface
{
    /**
     * @primaryKey
     */
    public int $id = 1;

    public string $title = "Et et tenetur assumenda fugit et recusandae voluptate.";

    public string $content = "Voluptas hic dolorem quae iure sit.";

    public DateTime $created_at = "2021-01-01 00:00:00";

    /**
     * @updatedTime
     */
    public DateTime $updated_at;

    /**
     * @foreignKey : user 
     */
    public int $user_id = new UserDTO();
}

$insertUser = new updatePostDTO();
$entityManager = new EntityManager('post');
$entityManager->update($insertUser);
//$entityManager->find(["user_id" => 1,"*","populate" => ['post',["id", "content"]]]);
Echodebug::xDebug($entityManager->getResult());


var_dump(UserController::getUser());
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stupid Blog</title>
</head>

<header>
    <h1>Stupid Blog</h1>
    <?php if (UserController::getUser()) : ?>
        <p>Bonjour <?= UserController::getUser()->getFirstname() ?> <?= UserController::getUser()->getLastname() ?></p>
    <?php endif ?>
    <nav>
        <ul>
            <li><a href="<?= Router::url('home') ?>">Accueil</a></li>
            <li><a href="<?= Router::url('posts', ['page' => 1]) ?>">Articles</a></li>
            <?php if (null !== UserController::getUser()) : ?>
                <li><a href="<?= Router::url('profile') ?>">Profil</a></li>
                <li><a href="<?= Router::url('logout') ?>">Se déconnecter</a></li>
                <?php if (UserController::getUser()->hasRole('ROLE_ADMIN')) : ?>
                    <li><a href="<?= Router::url('admin', ['action' => 'list', 'entity' => 'user']) ?>">Admin</a></li>
                <?php endif ?>
            <?php else : ?>
                <li><a href="<?= Router::url('login') ?>">Se connecter</a></li>
                <li><a href="<?= Router::url('register') ?>">S'inscrire</a></li>
            <?php endif ?>
        </ul>
    </nav>
</header>