<?php

namespace App\Controller\Interfaces;

interface HomeFacadeInterface
{
    public function paginatedPosts(int $page);
    public function viewPost(int $id);
    public function createComment(string $content, int $post_id);
}