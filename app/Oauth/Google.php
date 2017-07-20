<?php

namespace App\Oauth;

use League\OAuth2\Client\Provider\GoogleUser;

class Google extends \League\OAuth2\Client\Provider\Google implements CanGetUserByCodeInterface
{
    use CanGetUserByCode;

    protected function parseByResourceUser(GoogleUser $user)
    {
        return (new User())
            ->setProvider('google')
            ->setUid($user->getId())
            ->setName($user->getName())
            ->setEmail($user->getEmail())
            ->setAvatar($user->getAvatar());
    }
}