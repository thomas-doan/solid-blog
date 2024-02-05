<?php

use App\Class\Controller;
use App\Router\Router;

require_once 'vendor/autoload.php';

session_start();


$router = new Router($_SERVER['REQUEST_URI']);

$router->setBasePath('/stupid-blog/');

$router->get('/', function () {
    $controller = new Controller();
    $controller->render('index');
}, "home");

$router->get('/register', function () {
    try {
        $controller = new Controller();
        $controller->render('register');
    } catch (\Exception $e) {
        $controller->render('register', ['error' => $e->getMessage()]);
    }
}, "register");

$router->post('/register', function () {
    try {
        $controller = new Controller();
        $controller->registerUser($_POST['email'], $_POST['password'], $_POST['password_confirm'], $_POST['firstname'], $_POST['lastname']);
        $controller->redirect('login');
    } catch (\Exception $e) {
        $controller->render('register', ['error' => $e->getMessage()]);
    }
}, "register");

$router->get('/login', function () {
    $controller = new Controller();
    $controller->render('login');
}, "login");

$router->post('/login', function () {
    try {
        $controller = new Controller();
        $controller->loginUser($_POST['email'], $_POST['password']);
    } catch (\Exception $e) {
        $controller->render('login', ['error' => $e->getMessage()]);
    }
}, "login");

$router->get('/logout', function () {
    $controller = new Controller();
    $controller->logoutUser();
}, "logout");

$router->get('/profile', function () {
    $controller = new Controller();
    $controller->profile();
}, "profile");

$router->get('/posts/:page', function ($page = 1) {
    $controller = new Controller();
    $controller->paginatedPosts($page);
}, "posts")->with('page', '[0-9]+');

$router->get('/post/:id', function ($id) {
    $controller = new Controller();
    $controller->viewPost($id);
}, "post")->with('id', '[0-9]+');

$router->post('/comments/:post_id', function ($post_id) {
    try {
        $controller = new Controller();
        $controller->createComment($_POST['content'], $post_id);
    } catch (\Exception $e) {
        $controller->viewPost($post_id, ['error' => $e->getMessage()]);
    }
}, "add_comment")->with('post_id', '[0-9]+');

$router->get('/admin/:action/:entity', function ($action = 'list', $entity = 'user') {
    $controller = new Controller();
    $controller->admin($action, $entity);
}, "admin")->with('action', 'list')->with('entity', 'user|post|comment|category');

$router->get('/admin/:action/:entity/:id', function ($action = 'list', $entity = 'user', $id = null) {
    $controller = new Controller();
    $controller->admin($action, $entity, $id);
}, "admin-entity")->with('action', 'show')->with('entity', 'user|post|comment|category')->with('id', '[0-9]+');

$router->post('/admin/:action/:entity/:id', function ($action = 'list', $entity = 'user', $id = null) {
    $controller = new Controller();
    $controller->admin($action, $entity, $id);
}, "admin-entity")->with('action', 'edit|delete')->with('entity', 'user|post|comment|category')->with('id', '[0-9]+');


$router->run();
