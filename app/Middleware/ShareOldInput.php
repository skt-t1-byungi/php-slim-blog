<?php

namespace App\Middleware;

use Twig_Environment;

class ShareOldInput
{
    public function __invoke($request, $response, $next)
    {
        $twig = resolve(Twig_Environment::class);

        $this->extendTwig($twig);

        return $next($request, $response);
    }

    protected function extendTwig(Twig_Environment $twig)
    {
        $twig->addFunction(new \Twig_SimpleFunction('old_input', 'old_input'));
        $twig->addFilter(new \Twig_SimpleFilter('old', [$this, 'oldFilter']));
    }

    public function oldFilter($value, $key)
    {
        return old_input($key, $value);
    }
}