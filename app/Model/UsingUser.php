<?php

namespace App\Model;

trait UsingUser
{
    /**
     * @return bool
     */
    public function canUpdateOrDelete()
    {
        return auth()->isAdmin() || $this->user->equals(auth()->user());
    }

    /**
     * @return bool
     */
    public function cannotUpdateOrDelete()
    {
        return !$this->canUpdateOrDelete();
    }
}