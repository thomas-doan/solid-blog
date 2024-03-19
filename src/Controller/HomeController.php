<?php

namespace App\Controller;

use App\Controller\AbstractController;
use App\Controller\Facade\HomeFacade;
use App\Controller\Strategy\ExceptionHandler;
use App\Controller\Strategy\EmptyContentException;
use App\Controller\Strategy\NotLoggedInException;
use App\Controller\Strategy\NumericIdException;

class HomeController extends AbstractController
{
    private HomeFacade $homeFacade;
    private ExceptionHandler $exceptionHandler;

    public function __construct(HomeFacade $homeFacade, ExceptionHandler $exceptionHandler)
    {
        $this->homeFacade = $homeFacade;
        $this->exceptionHandler = $exceptionHandler;
    }

    public function paginatedPosts($page)
    {
        try {
            $data = $this->homeFacade->paginatedPosts($page);
            $this->render('posts', $data);
        } catch (\Exception $exception) {
            $this->exceptionHandler->handle($exception);
        }
    }

    public function viewPost($id, $error = null)
    {
        try {
            if (is_numeric($id) === false) {
                throw new NumericIdException("L'identifiant du post n'est pas valide");
            }
            $post = $this->homeFacade->viewPost((int)$id);
            $this->render('post', ['post' => $post, 'error' => $error]);
        } catch (\Exception $exception) {
            $this->exceptionHandler->handle($exception);
        }
    }

    public function createComment($content, $post_id)
    {
        try {
            if (empty($content)) {
                throw new EmptyContentException("Le contenu ne peut pas être vide");
            }

            if (self::getUser() === null) {
                throw new NotLoggedInException("Vous devez être connecté pour commenter");
            }

            if (is_numeric($post_id) === false) {
                throw new NumericIdException("L'identifiant du post n'est pas valide");
            }

            $comment = $this->homeFacade->createComment($content, (int)$post_id);
            $comment->setUser(self::getUser());
            $comment->save();

            $this->redirect('post', ['id' => $post_id]);
        } catch (\Exception $exception) {
            $this->exceptionHandler->handle($exception);
        }
    }
}