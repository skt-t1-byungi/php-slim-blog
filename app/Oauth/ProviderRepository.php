<?php

namespace App\Oauth;

use League\OAuth2\Client\Provider\AbstractProvider;

class ProviderRepository
{
    public $providers = [];

    /**
     * @param $providerId
     * @return AbstractProvider
     */
    public function get($providerId)
    {
        if (array_key_exists($providerId, $this->providers)) {
            return $this->providers[$providerId];
        }

        if (method_exists($this, $providerId)) {
            return $this->providers[$providerId] = call_user_func([$this, $providerId]);
        }

        throw new \OutOfRangeException("There is no create method : \"{$providerId}\"");
    }

    public function naver()
    {
        return new Naver([
            'clientId'     => getenv('OAUTH_NAVER_ID'),
            'clientSecret' => getenv('OAUTH_NAVER_SECRET'),
            'redirectUri'  => $this->redirectUri('naver'),
        ]);
    }

    public function google()
    {
        return new Google([
            'clientId'     => getenv('OAUTH_GOOGLE_ID'),
            'clientSecret' => getenv('OAUTH_GOOGLE_SECRET'),
            'redirectUri'  => $this->redirectUri('google'),
        ]);
    }

    public function facebook()
    {
        return new Facebook([
            'clientId'        => getenv('OAUTH_FACEBOOK_ID'),
            'clientSecret'    => getenv('OAUTH_FACEBOOK_SECRET'),
            'redirectUri'     => $this->redirectUri('facebook'),
            'graphApiVersion' => 'v2.9',
        ]);
    }

    /**
     * @param $providerId
     * @return string
     */
    protected function redirectUri($providerId)
    {
        return getenv('HTTP_BASE_URL') . path_for('oauth.callback', ['providerId' => $providerId]);
    }
}