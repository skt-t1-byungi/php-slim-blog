<?php

namespace app\Controller;

use App\Model\Comment;
use App\Model\Post;

trait CanGetModelOrAbort
{
    /**
     * @param $postId
     * @param array $with
     * @return Post
     */
    protected function getPostOrAbort($postId, array $with = [])
    {
        return $this->getModelOrAbort(Post::class, $postId, $with);
    }

    /**
     * @param $commentId
     * @param array $with
     * @return Comment
     */
    protected function getCommentOrAbort($commentId, array $with = [])
    {
        return $this->getModelOrAbort(Comment::class, $commentId, $with);
    }

    protected function getModelOrAbort($class, $id, array $with = [])
    {
        $model = call_user_func([$class, 'whereId'], $id)->with($with)->first();

        if (is_null($model)) {
            abort(404);
        }

        return $model;
    }
}