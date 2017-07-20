<?php

namespace App;

class Response extends \Slim\Http\Response
{
    public function withErrors(array $errors)
    {
        flash()->setErrors($errors);
        return $this;
    }

    public function withInput()
    {
        flash()->setInput(resolve('request')->getParams());
        return $this;
    }
}