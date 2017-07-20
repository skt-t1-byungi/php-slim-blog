<?php

namespace App;

use App\Model\User;

class Auth
{
    /**
     * @var User
     */
    protected $user;

    public function __construct()
    {
        if (isset($_SESSION['userId'])) {
            $this->user = User::find($_SESSION['userId']);
        }
    }

    /**
     * @return User|null
     */
    public function user()
    {
        return $this->user;
    }

    public function check()
    {
        return !is_null($this->user);
    }

    public function isAdmin()
    {
        return $this->check() && $this->user->is_admin;
    }

    public function login(User $user)
    {
        $this->user = $user;
        $_SESSION['userId'] = $user->id;

        return $this;
    }

    public function logout()
    {
        $this->user = null;
        unset($_SESSION['userId']);

        return $this;
    }
}