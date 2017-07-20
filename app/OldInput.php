<?php

namespace App;

use Slim\Http\Request;
use Twig_Environment;

class OldInput
{
    /**
     * @var array
     */
    protected $oldInput;

    public function __construct()
    {
        $this->oldInput = flash()->getInput();
    }

    public function get($key, $default = null)
    {
        return array_get($this->oldInput, $key, $default);
    }
}