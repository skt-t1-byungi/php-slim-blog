<?php

namespace App\Oauth;

interface CanGetUserByCodeInterface
{
    public function getUserByCode($code);
}