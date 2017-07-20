<?php

namespace App\Oauth;

trait CanGetUserByCode
{
    /**
     * @param $code
     * @return User
     */
    public function getUserByCode($code)
    {
        $token = $this->getAccessToken('authorization_code', ['code' => $code]);

        $user = $this->getResourceOwner($token);

        return $this->parseByResourceUser($user);
    }
}