<?php

namespace App\Controller;
use App\Class\Comment;
use App\Class\Post;
use App\Controller;
use App\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function paginatedPosts($page)
    {
        $post = new Post();
        $posts = $post->findAllPaginated($page);
        $pages = count($post->findAll()) / 10;
        $this->render('posts', ['posts' => $posts, 'pages' => $pages]);
    }

    public function viewPost($id, $error = null)
    {
        if (is_numeric($id) === false) {
            throw new \Exception("L'identifiant du post n'est pas valide");

            return;
        }
        $post = new Post();
        $post = $post->findOneById((int)$id);
        $this->render('post', ['post' => $post, 'error' => $error]);
    }

    public function createComment($content, $post_id)
    {
        if (empty($content)) {
            throw new \Exception("Le contenu ne peut pas être vide");

            return;
        }

        if (self::getUser() === null) {
            throw new \Exception("Vous devez être connecté pour commenter");
            return;
        }

        if (is_numeric($post_id) === false) {
            throw new \Exception("L'identifiant du post n'est pas valide");
            return;
        }

        $post_id = (int)$post_id;

        $post = new Post();
        $post = $post->findOneById($post_id);

        $comment = new Comment();
        $comment->setContent($content);
        $comment->setUser(self::getUser());
        $comment->setPost($post);
        $comment->setCreatedAt(new \DateTime());
        $comment->save();

        $this->redirect('post', ['id' => $post->getId()]);
    }

}