<?php

use App\Router\Router;

?>

<body>
    <h1>Tous les articles</h1>
    <?php foreach ($posts as $post) : ?>
        <article>
            <h2><?= $post->getTitle() ?></h2>
            <p><?= $post->getContent() ?></p>
            <p><?= $post->getCreatedAt()->format('d/m/Y') ?></p>
            <p><?= $post->getUser()->getFirstname() ?> <?= $post->getUser()->getLastname() ?></p>
            <a href="<?= Router::url('post', ['id' => $post->getId()]) ?>">Voir l'article</a>
        </article>
    <?php endforeach ?>
    <?php for ($i = 1; $i <= $pages; $i++) : ?>
        <a href="<?= Router::url('posts', ['page' => $i]) ?>"><?= $i ?></a>
    <?php endfor ?>
</body>