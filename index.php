<?php

use App\Container\SimpleContainer;
use App\Controller\AdminController;
use App\Controller\Facade\HomeFacade;
use App\Controller\HomeController;
use App\Controller\UserController;
use App\Exception\ValidationException;
use App\Factory\UserDTOFactory;
use App\Mapper\UserMapper;
use App\Router\Router;
use App\Service\User\UserService;
use App\Service\User\UserServiceInterface;

require_once 'vendor/autoload.php';

session_start();


$router = new Router($_SERVER['REQUEST_URI']);


$router->setBasePath('/blog/');

$container = new SimpleContainer(); // Container d'injection de dépendance

$container->set(UserMapper::class, function() {
    return new UserMapper();
});

$container->set(UserService::class, function($container) {
    return new UserService($container->get(UserMapper::class),
                            $container->get(UserDTOFactory::class));
});

$router->get('/', function () {
    $homeFacade = new HomeFacade();
    $controller = new HomeController($homeFacade);
    $controller->render('index');
}, "home");

$router->get('/register', function () use($container) {
    try {
        $controller = new UserController($container->get(UserService::class), $container);
        $controller->render('register');
    } catch (\Exception $e) {
        $controller->render('register', ['error' => $e->getMessage()]);
    }
}, "register");

$router->post('/register', function () use($container) {

        $controller = new UserController($container->get(UserService::class), $container);
        $userData = [
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'confirmPassword' => $_POST['password_confirm'],
            'firstname' => $_POST['firstname'],
            'lastname' => $_POST['lastname']
        ];
        $controller->registerUser($userData);
}, "register");

$router->get('/login', function () use($container) {

    $controller = new UserController($container->get(UserService::class),  $container);
    $controller->render('login');
}, "login");

$router->post('/login', function () use($container) {

        $controller = new UserController($container->get(UserService::class),  $container);
        $controller->loginUser($_POST['email'], $_POST['password']);

}, "login");

$router->get('/logout', function () use($container) {
    $controller = new UserController($container->get(UserService::class),  $container);
    $controller->logoutUser();
}, "logout");

$router->get('/profile', function () use($container) {
    $controller = new UserController($container->get(UserService::class),  $container);
    $controller->profile();
}, "profile");

$router->get('/posts/:page', function ($page = 1) {
    $homeFacade = new HomeFacade();
    $controller = new HomeController($homeFacade);
    $controller->paginatedPosts($page);
}, "posts")->with('page', '[0-9]+');

$router->get('/post/:id', function ($id) {
    $homeFacade = new HomeFacade();
    $controller = new HomeController($homeFacade);
    $controller->viewPost($id);
}, "post")->with('id', '[0-9]+');

$router->post('/comments/:post_id', function ($post_id) {
    try {
        $homeFacade = new HomeFacade();
        $controller = new HomeController($homeFacade);
        $controller->createComment($_POST['content'], $post_id);
    } catch (\Exception $e) {
        $controller->viewPost($post_id, ['error' => $e->getMessage()]);
    }
}, "add_comment")->with('post_id', '[0-9]+');

$router->get('/admin/:action/:entity', function ($action = 'list', $entity = 'user') {
    $controller = new AdminController();
    $controller->admin($action, $entity);
}, "admin")->with('action', 'list')->with('entity', 'user|post|comment|category');

$router->get('/admin/:action/:entity/:id', function ($action = 'list', $entity = 'user', $id = null) {
    $controller = new AdminController();
    $controller->admin($action, $entity, $id);
}, "admin-entity")->with('action', 'show')->with('entity', 'user|post|comment|category')->with('id', '[0-9]+');

$router->post('/admin/:action/:entity/:id', function ($action = 'list', $entity = 'user', $id = null) {
    $controller = new AdminController();
    $controller->admin($action, $entity, $id);
}, "admin-entity")->with('action', 'edit|delete')->with('entity', 'user|post|comment|category')->with('id', '[0-9]+');


$router->run();
