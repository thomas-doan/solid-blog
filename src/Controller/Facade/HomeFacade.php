<?php

namespace App\Controller\Facade;

use App\Class\Comment;
use App\Class\Post;
use App\Controller\Interfaces\HomeFacadeInterface;

class HomeFacade implements HomeFacadeInterface
{
    public function paginatedPosts(int $page)
    {
        $post = new Post();
        $posts = $post->findAllPaginated($page);
        $pages = count($post->findAll()) / 10;

        return ['posts' => $posts, 'pages' => $pages];
    }

    public function viewPost(int $id)
    {
        $post = new Post();
        $post = $post->findOneById($id);

        return $post;
    }

    public function createComment(string $content, int $post_id)
    {
        $comment = new Comment();
        $comment->setContent($content);
        $comment->setPost($post_id);
        $comment->setCreatedAt(new \DateTime());

        return $comment;
    }
}