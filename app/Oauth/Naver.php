<?php

namespace App\Oauth;

use SktT1Byungi\OAuth2\Client\Provider\NaverUser;

class Naver extends \SktT1Byungi\OAuth2\Client\Provider\Naver implements CanGetUserByCodeInterface
{
    use CanGetUserByCode;

    protected function parseByResourceUser(NaverUser $user)
    {
        return (new User())
            ->setProvider('naver')
            ->setUid($user->getId())
            ->setName($user->getName())
            ->setEmail($user->getEmail())
            ->setAvatar($user->getAvatar());
    }
}