<?php

namespace App\Controller;

use App\Controller\AbstractController;
use App\Controller\Facade\HomeFacade;


class HomeController extends AbstractController
{
    private HomeFacade $homeFacade;

    public function __construct(HomeFacade $homeFacade)
    {
        $this->homeFacade = $homeFacade;
    }

    public function paginatedPosts($page)
    {
        $data = $this->homeFacade->paginatedPosts($page);
        $this->render('posts', $data);
    }

    public function viewPost($id, $error = null)
    {
        if (is_numeric($id) === false) {
            throw new \Exception("L'identifiant du post n'est pas valide");

            return;
        }
        $post = $this->homeFacade->viewPost((int)$id);
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

        $comment = $this->homeFacade->createComment($content, (int)$post_id);
        $comment->setUser(self::getUser());
        $comment->save();

        $this->redirect('post', ['id' => $post_id]);
    }
}