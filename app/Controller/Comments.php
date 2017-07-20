<?php

namespace App\Controller;

use App\Model\Comment;
use App\Validate;
use Slim\Http\Request;

class Comments
{
    use CanGetModelOrAbort;

    public function create(Request $request)
    {
        if (!auth()->check()) {
            abort(401);
        }

        $post = $this->getPostOrAbort($request->getParam('post_id'));

        $form = $this->validateComment($request->getParams());

        if ($form->isNotValid()) {
            $parentId = $request->getParam('parent_id');

            return $this->redirectWithHashBang($post, $parentId ? "reply-comment-{$parentId}" : 'write-comment')
                ->withErrors($form->getMessages())
                ->withInput();
        }

        $comment = $post->writeComment($form->getValues());

        return $this->redirectWithHashBang($post, $comment->idInTag());
    }

    protected function redirectWithHashBang($post, $hashbang)
    {
        return redirect(path_for('posts.show', ['postId' => $post->id]) . '#' . $hashbang);
    }

    public function update($commentId, Request $request)
    {
        $comment = $this->getCommentOrAbort($commentId);

        if ($comment->cannotUpdateOrDelete()) {
            abort(401);
        }

        $form = $this->validateComment($request->getParams());

        if ($form->isNotValid()) {
            return $this->redirectWithHashBang($comment->post, $comment->idInEditForm())
                ->withErrors($form->getMessages())
                ->withInput();
        }

        $comment->update($form->getValues());

        return $this->redirectWithHashBang($comment->post, $comment->idInTag());
    }

    public function delete($commentId)
    {
        $comment = $this->getCommentOrAbort($commentId);

        if ($comment->cannotUpdateOrDelete()) {
            abort(401);
        }

        if ($comment->hasChildrens()) {
            $comment->delete();
        } else {
            $comment->forceDelete();
        }

        return redirect_for('posts.show', ['postId' => $comment->post->id]);
    }

    /**
     * @param $params
     * @return \Particle\Validator\ValidationResult
     */
    protected function validateComment($params)
    {
        return validate(array_map('trim', $params), function (Validate $v) {
            $v->required('comment', '댓글');
            $v->optional('parent_id', '부모댓글')->callback(function ($value) {
                return Comment::whereId((int)$value)->count() === 1;
            });
        });
    }
}