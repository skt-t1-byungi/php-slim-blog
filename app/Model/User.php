<?php

namespace App\Model;

use App\Oauth\User as OauthUser;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property boolean is_admin
 * @property string oauth_provider
 * @property string oauth_uid
 */
class User extends Model
{
    public $timestamps = false;

    protected $dates = [
        'created_at'
    ];

    protected $fillable = [
        'oauth_provider',
        'oauth_uid',
        'name',
        'email',
        'avatar',
        'is_admin'
    ];

    protected $hidden = [
        'oauth_uid',
        'oauth_provider',
        'created_at'
    ];

    protected $casts = [
        'is_admin' => 'boolean'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }


    public static function updateOrCreateByOauthUser(OauthUser $user)
    {
        return User::where('oauth_provider', $user->getProvider())
            ->where('oauth_uid', $user->getUid())
            ->updateOrCreate(
                [
                    'oauth_provider' => $user->getProvider(),
                    'oauth_uid'      => $user->getUid()
                ],
                self::userForUpdate($user)
            );
    }

    public static function userForUpdate(OauthUser $user)
    {
        // 관리자일 경우, 관리자 권한과 관리자 이름으로 대체합니다
        // oauth 공급자와 이메일을 사용해 관리자를 구분합니다.
        if (
            $user->getProvider() === getenv('ADMIN_PROVIDER') &&
            $user->getEmail() === getenv('ADMIN_PROVIDER_EMAIL')
        ) {
            return [
                'name'     => getenv('ADMIN_PROVIDER_NAME'),
                'email'    => $user->getEmail(),
                'avatar'   => $user->getAvatar(),
                'is_admin' => true
            ];
        }

        return [
            'name'     => $user->getName(),
            'email'    => $user->getEmail(),
            'avatar'   => $user->getAvatar(),
            'is_admin' => false
        ];
    }

    /**
     * @param array $params
     * @return Post
     */
    public function writePostWithTags(array $params)
    {
        $post = new Post($params);

        $this->posts()->save($post);

        if (!empty($params['tags'])) {
            $post->syncTags($params['tags']);
        }

        return $post;
    }

    public function equals(self $user)
    {
        return $this->oauth_provider === $user->oauth_provider &&
            $this->oauth_uid === $user->oauth_uid;
    }
}