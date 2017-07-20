<?php

namespace App\Controller;

use App\Model\User;
use App\Oauth\CanGetUserByCodeInterface;
use App\Oauth\ProviderRepository;
use Slim\Http\Request;

class Login
{
    /**
     * @var ProviderRepository
     */
    private $providers;

    public function __construct(ProviderRepository $providers)
    {
        $this->providers = $providers;
    }

    public function redirect($providerId, Request $request)
    {
        $this->storeReferer($request);

        $authUrl = $this->providers->get($providerId)
            ->getAuthorizationUrl([
                'state' => $this->createState(),
            ]);

        return redirect($authUrl);
    }

    protected function storeReferer(Request $request)
    {
        $referer = $request->getParam(
            'referer',
            array_get($_SERVER, 'HTTP_REFERER', path_for('home'))
        );

        flash()->set('oauth_referer', $referer);
    }

    protected function restoreReferer($default = null)
    {
        return flash('oauth_referer', $default);
    }

    protected function createState()
    {
        $state = str_random(10);
        flash()->set('oauth_state', $state);

        return $state;
    }

    protected function checkState($state)
    {
        return flash('oauth_state') === $state;
    }

    public function callback($providerId, Request $request)
    {
        //state 가 틀렸을 경우, oauth 토큰을 받기 위해 oauth 로 리다이렉트 합니다.
        if (!$this->checkState($request->getQueryParam('state'))) {
            return redirect_for('oauth.redirect', ['providerId' => $providerId]);
        }

        $user = $this->getUserThroughExchangeToken($providerId, $request->getQueryParam('code'));
        auth()->login($user);

        //리퍼러가 존재하지 않는다면 index 로 리다이렉트 합니다.
        $referer = $this->restoreReferer(path_for('home'));

        return redirect($referer);
    }

    /**
     * Oauth 토큰교환을 통해 유저정보를 가져옵니다.
     * 외부 API 통신을 하기 때문에 예외발생이 언제든 일어날 수 있습니다.
     *
     * @param $providerId
     * @param $code
     * @throws \OAuthException
     * @return User
     */
    protected function getUserThroughExchangeToken($providerId, $code)
    {
        /** @var CanGetUserByCodeInterface $provider */
        $provider = $this->providers->get($providerId);

        return User::updateOrCreateByOauthUser($provider->getUserByCode($code));
    }

    public function logout()
    {
        auth()->logout();

        return redirect_for('home');
    }
}