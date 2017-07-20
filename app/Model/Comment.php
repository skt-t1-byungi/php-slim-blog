<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int user_id
 * @property User user
 * @property Post post
 * @property Carbon updated_at
 * @property Carbon created_at
 * @property string comment
 * @property int id
 * @property int post_id
 */
class Comment extends Model
{
    use UsingUser, SoftDeletes;

    protected $fillable = ['comment', 'user_id', 'parent_id'];

    protected $dates = ['deleted_at'];

    public $childrens;

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isUpdated()
    {
        return !$this->created_at->equalTo($this->updated_at);
    }

    public function getContentHtmlAttribute()
    {
        return '<p>' . preg_replace("/(\n)+/", '</p><p>', htmlspecialchars($this->comment)) . '</p>';
    }

    public function idInTag()
    {
        return 'comment-' . $this->id;
    }

    public function idInEditForm()
    {
        return 'edit-comment-' . $this->id;
    }

    public function idInReplyForm()
    {
        return 'reply-comment-' . $this->id;
    }

    public function hasChildrens()
    {
        return self::whereParentId($this->id)->count() >= 1;
    }
}