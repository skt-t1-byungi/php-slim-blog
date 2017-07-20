<?php

namespace App\Middleware;

use Slim\Exception\SlimException;
use Slim\Http\Request;
use Slim\Http\Response;

class CsrfGuard
{
    protected $token;

    public function __construct(\Twig_Environment $twig)
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = str_random(40);
        }

        $this->token = $_SESSION['_csrf_token'];

        $this->addTwigFunction($twig);
    }

    public function __invoke(Request $request, $response, $next)
    {
        if (in_array($request->getMethod(), ['POST', 'PATCH', 'DELETE', 'PUT'])) {
            $this->assertToken($request);
        }

        return $next($request, $response);
    }

    /**
     * @param Request $request
     * @throws SlimException
     */
    protected function assertToken(Request $request)
    {
        $requestToken = $request->getParam('_csrf_token');

        if (!$requestToken) {
            $requestToken = $request->getHeaderLine('X-CSRF-TOKEN');
        }

        if ($requestToken !== $this->getToken()) {
            throw new SlimException($request, $this->errorResponse());
        }
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    public function getField()
    {
        return "<input type='hidden' name='_csrf_token' value='{$this->getToken()}'>";
    }

    protected function errorResponse()
    {
        return (new Response(400))->write('Csrf verification failed.');
    }

    /**
     * @param \Twig_Environment $twig
     */
    protected function addTwigFunction(\Twig_Environment $twig)
    {
        $twig->addFunction($this->newTwigFunction('csrf_field', 'getField', ['is_safe' => ['html']]));
        $twig->addFunction($this->newTwigFunction('csrf_token', 'getToken'));
    }

    /**
     * @param $name
     * @param $method
     * @param $options
     * @return \Twig_SimpleFunction
     */
    protected function newTwigFunction($name, $method, array $options = [])
    {
        return new \Twig_SimpleFunction($name, [$this, $method], $options);
    }
}