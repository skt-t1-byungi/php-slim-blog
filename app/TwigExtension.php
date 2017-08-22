<?php

namespace App;

use Slim\Http\Request;

class TwigExtension extends \Twig_Extension
{
    public function getGlobals()
    {
        /**
         * @var Request $request
         */
        $request = resolve(Request::class);

        return [
            'auth'             => auth(),
            'request'          => $request,
            'blogger'          => getenv('ADMIN_PROVIDER_NAME'),
            'blog_description' => getenv('BLOG_DESCRIPTION'),
            'canonical'        => rtrim(getenv('HTTP_BASE_URL'), '/') . $request->getUri()->getPath()
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('path_for', [$this, 'functionPathFor']),
            new \Twig_SimpleFunction('flash', [$this, 'functionFlash']),
            new \Twig_SimpleFunction('resolve', [$this, 'functionResolve']),
            new \Twig_SimpleFunction('backUrl', [$this, 'functionBackUrl']),
        ];
    }

    public function functionPathFor($name, array $data = [], array $query = [])
    {
        return path_for($name, $data, $query);
    }

    public function functionFlash($id = null, $default = null)
    {
        return flash($id, $default);
    }

    public function functionResolve($class)
    {
        return resolve($class);
    }

    public function functionBackUrl()
    {
        return array_get($_SERVER, 'HTTP_REFERER', path_for('home'));
    }
}