<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property int bytes
 * @property string name
 * @property string path
 * @property int id
 * @property string type
 * @property Carbon created_at
 */
class File extends Model
{
    protected $fillable = ['path', 'name', 'bytes', 'type', 'post_id'];

    public $timestamps = false;

    protected $dates = ['created_at'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function isImage()
    {
        return strpos($this->type, 'image') !== false;
    }
}