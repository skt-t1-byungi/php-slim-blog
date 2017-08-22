<?php

namespace App\Model;

use App\Controller\Posts;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * @property int id
 * @property string markdown_content
 * @property boolean is_published
 * @property Collection comments
 * @property int user_id
 * @property User user
 * @property string parsed_content
 * @property string summary
 * @property Carbon created_at
 * @property string title
 */
class Post extends Model
{
    use UsingUser;

    protected $fillable = ['title', 'markdown_content', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        //DB에 저장하기 전, 마크다운을 파싱합니다.
        self::saving(function (Post $post) {
            $post->parsed_content = markdown($post->markdown_content);
            $post->summary = mb_substr(html_entity_decode(strip_tags($post->parsed_content)), 0, 250) . '...';
        });

        //낮은 확률로 post 가 없는 태그들을 정리합니다.
        self::updated(function () {
            if (rand(1, 10) <= 1) {
                Tag::cleanUp();
            }
        });
    }

    public function updateWithTags(array $params)
    {
        $this->fill($params + ['is_published' => false])->save();

        $this->syncTags(array_get($params, 'tags', []));

        return $this;
    }

    /**
     * @param array $params
     * @return Comment
     */
    public function writeComment(array $params)
    {
        $comment = new Comment($params);
        $comment->user()->associate(auth()->user());

        $this->comments()->save($comment);

        return $comment;
    }

    /**
     * @param $query Builder
     * @return Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * @param $query Builder
     * @return Builder
     */
    public function scopePublishedIfNotAdmin($query)
    {
        return $query->when(!auth()->isAdmin(), function ($query) {
            return $query->published();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function link($withHost = false)
    {
        if ($withHost) {
            return url_for('posts.show', ['postId' => $this->id]);
        }

        return path_for('posts.show', ['postId' => $this->id]);
    }

    public function pageLink()
    {
        $count = self::where('id', '>', $this->id)
            ->publishedIfNotAdmin()
            ->count();

        $page = intval($count / Posts::PER_PAGE) + 1;

        return path_for('posts.index', [], ['page' => $page]);
    }

    public function syncTags(array $tags = [])
    {
        $this->tags()->sync(Tag::getOrCreate($tags)->pluck('id'));
    }

    public function nestedComments()
    {
        $comments = $this->comments()->withTrashed()->with('user')->get();

        return $comments
            ->each(function ($comment) use ($comments) {
                if ($comment->parent_id) {
                    /** @var Comment $parent */
                    $parent = $comments->find($comment->parent_id);

                    $parent->childrens[] = $comment;
                }
            })
            ->filter(function ($comment) {
                return !$comment->parent_id;
            });
    }
}