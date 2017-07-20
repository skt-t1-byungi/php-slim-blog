<?php

namespace App\Middleware;

use Twig_Environment;

class ShareErrors
{
    public function __invoke($request, $response, $next)
    {
        resolve(Twig_Environment::class)->addGlobal('errors', flash()->getErrors());

        return $next($request, $response);
    }
}