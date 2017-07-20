<?php

namespace App\Oauth;

use League\OAuth2\Client\Provider\FacebookUser;

class Facebook extends \League\OAuth2\Client\Provider\Facebook implements CanGetUserByCodeInterface
{
    use CanGetUserByCode;

    protected function parseByResourceUser(FacebookUser $user)
    {
        return (new User())
            ->setProvider('facebook')
            ->setUid($user->getId())
            ->setName($user->getName())
            ->setEmail($user->getEmail())
            ->setAvatar($user->getPictureUrl());
    }
}