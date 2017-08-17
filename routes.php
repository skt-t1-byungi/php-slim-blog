<?php

use App\Controller\About;
use App\Controller\Comments;
use App\Controller\Files;
use App\Controller\Login;
use App\Controller\Posts;

//$app->get('/', [Posts::class, 'latest'])->setName('home');
$app->get('/', [Posts::class, 'index'])->setName('home');
$app->get('/about', About::class)->setName('about');

$app->group('/login', function () {
    $this->get('/callback/{providerId}', [Login::class, 'callback'])->setName('oauth.callback');
    $this->get('/redirect/{providerId}', [Login::class, 'redirect'])->setName('oauth.redirect');
});

$app->get('/logout', [Login::class, 'logout'])->setName('logout');

$app->group('/posts', function () {
    $this->get('', [Posts::class, 'index'])->setName('posts.index');

    $this->get('/write', [Posts::class, 'write'])->setName('posts.write');
    $this->post('/write', [Posts::class, 'create'])->setName('posts.create');

    $this->post('/markdown', [Posts::class, 'parseMarkdown'])->setName('posts.markdownParser');

    $this->get('/{postId}', [Posts::class, 'show'])->setName('posts.show');
    $this->get('/{postId}/edit', [Posts::class, 'edit'])->setName('posts.edit');
    $this->post('/{postId}/edit', [Posts::class, 'update'])->setName('posts.update');

    $this->get('/{postId}/delete', [Posts::class, 'delete'])->setName('posts.delete');
});

$app->get('/tags/{tag}', [Posts::class, 'tags'])->setName('tags');

$app->group('/comments', function () {
    $this->post('/write', [Comments::class, 'create'])->setName('comments.create');
    $this->post('/{commentId}/edit', [Comments::class, 'update'])->setName('comments.update');
    $this->get('/{commentId}/delete', [Comments::class, 'delete'])->setName('comments.delete');
});

$app->group('/files', function () {
    $this->get('[/{postId}]', [Files::class, 'show'])->setName('files.show');
    $this->post('/upload[/{postId}]', [Files::class, 'upload'])->setName('files.upload');
    $this->post('/delete', [Files::class, 'delete'])->setName('files.delete');
    $this->get('/download/{hash}', [Files::class, 'download'])->setName('files.download');
});
