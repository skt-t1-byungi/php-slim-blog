<?php

namespace App\Controller;

use App\Model\Post;
use App\Validate;
use Illuminate\Pagination\LengthAwarePaginator;
use Slim\Http\Request;

class Posts
{
    const PER_PAGE = 5;

    use CanGetModelOrAbort;

    public function index()
    {
        /** @var LengthAwarePaginator $posts */
        $posts = Post::latest()
            ->with('tags')
            ->withCount('comments')
            ->publishedIfNotAdmin()
            ->paginate(self::PER_PAGE);

        if ($posts->isEmpty() && $posts->currentPage() !== 1) {
            abort(404);
        }

        return view('posts/index', ['posts' => $posts]);
    }

    public function tags($tag)
    {
        $posts = Post::latest()
            ->withCount('comments')
            ->publishedIfNotAdmin()
            ->whereHas('tags', function ($query) use ($tag) {
                return $query->whereTag($tag);
            })
            ->paginate(self::PER_PAGE);

        if ($posts->isEmpty() && $posts->currentPage() !== 1) {
            abort(404);
        }

        return view('posts/tags', ['posts' => $posts, 'tag' => $tag]);
    }

    public function latest()
    {
        $post = Post::published()->latest()->with('comments')->first();

        if (!$post) {
            return redirect_for('posts.index');
        }

        return $this->postView($post);
    }

    public function write()
    {
        if (!auth()->isAdmin()) {
            return redirect_for('home');
        }

        return view('posts/write');
    }

    public function create(Request $request)
    {
        if (!auth()->isAdmin()) {
            abort(401);
        }

        $form = $this->validatePost($request->getParams());

        if ($form->isNotValid()) {
            return back()->withInput()->withErrors($form->getMessages());
        }

        $post = auth()->user()->writePostWithTags($form->getValues());

        return redirect_for('posts.show', ['postId' => $post->id]);
    }

    public function parseMarkdown(Request $request)
    {
        if (!auth()->isAdmin()) {
            abort(401);
        }

        return markdown($request->getParam('markdown'));
    }

    public function show($postId)
    {
        $post = $this->getPostOrAbort($postId);

        if (!$post->is_published && !auth()->isAdmin()) {
            abort(404);
        }

        return $this->postView($post);
    }

    public function edit($postId)
    {
        $post = $this->getPostOrAbort($postId);

        if ($post->cannotUpdateOrDelete()) {
            abort(404);
        }

        return view('posts/edit', ['post' => $post]);
    }

    public function update($postId, Request $request)
    {
        $post = $this->getPostOrAbort($postId);

        if ($post->cannotUpdateOrDelete()) {
            abort(401);
        }

        $form = $this->validatePost($request->getParams());

        if ($form->isNotValid()) {
            return back()->withInput()->withErrors($form->getMessages());
        }

        $post->updateWithTags($form->getValues());

        return redirect_for('posts.show', ['postId' => $post->id]);
    }

    public function delete($postId)
    {
        $post = $this->getPostOrAbort($postId);

        if ($post->cannotUpdateOrDelete()) {
            abort(401);
        }

        $post->delete();

        return redirect_for('home');
    }

    /**
     * @param $params
     * @return \Particle\Validator\ValidationResult
     */
    protected function validatePost($params)
    {
        array_walk_recursive($params, 'trim');

        return validate($params, function (Validate $v) {
            $v->required('title', '제목');
            $v->required('markdown_content', '마크다운');
            $v->optional('is_published', '발행')->inArray(['0', '1']);
            $v->optional('tags', '태그')->isArray();
        });
    }

    /**
     * @param Post $post
     * @return \App\Response
     */
    protected function postView(Post $post)
    {
        return view('posts/show', [
            'post'           => $post,
            'comments'       => $post->nestedComments(),
            'comments_count' => $post->comments()->count()
        ]);
    }
}