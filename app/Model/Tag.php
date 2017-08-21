<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property string tag
 */
class Tag extends Model
{
    protected $fillable = ['tag'];

    public $timestamps = false;

    /**
     * @param array $tags
     * @return Collection
     */
    public static function getOrCreate(array $tags)
    {
        return collect($tags)->filter()->map(function ($tag) {
            return self::firstOrCreate(['tag' => $tag], ['tag' => $tag]);
        });
    }

    public static function cleanUp()
    {
        return self::doesntHave('posts')->delete();
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public function link()
    {
        return path_for('tags', ['tag' => urlencode($this->tag)]);
    }

    public function __toString()
    {
        return $this->tag;
    }
}