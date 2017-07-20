<?php

function app()
{
    global $app;
    return $app;
}

function resolve($class)
{
    return app()->getContainer()->get($class);
}

/**
 * @param int $status
 *
 * @return \App\Response
 */
function response($status = 200)
{
    return new App\Response($status);
}

/**
 * @param string $path
 * @param array $data
 *
 * @return \App\Response
 */
function view($path, $data = [])
{
    /**
     * @var \Twig_Environment $twig
     */
    $twig = resolve('twig');
    return response()->write(
        $twig->load($path . '.twig')->render($data)
    );
}

function abort($status = 500, $message = " A server error has occurred.")
{
    throw new \App\HttpException($message, $status);
}

function path_for($name, $data = [], $query = [])
{
    $path = resolve('router')->pathFor($name, $data, $query);

    //TrailingSlash 미들웨어를 사용하고 있어, 마지막 슬래시가 불필요합니다.
    return $path === '/' ? $path : rtrim($path, '/');
}

/**
 * @param string $url
 * @param int $status
 *
 * @return \App\Response
 */
function redirect($url, $status = 302)
{
    return response($status)->withRedirect($url);
}

function redirect_for($name, $data = [], $query = [])
{
    return redirect(path_for($name, $data, $query));
}

/**
 * @return \App\Response
 */
function back()
{
    //리퍼러가 존재하지 않을 경우 기본 index 페이지로 리다이렉트합니다.
    $referer = array_get($_SERVER, 'HTTP_REFERER', path_for('home'));
    return redirect($referer);
}

/**
 * @return \App\Auth
 */
function auth()
{
    return resolve(\App\Auth::class);
}

/**
 * @param null $accessor
 * @param null $default
 * @return \App\Flash|mixed|null
 */
function flash($accessor = null, $default = null)
{
    /**
     * @var \App\Flash $flash
     */
    $flash = resolve(\App\Flash::class);

    if (is_null($accessor)) {
        return $flash;
    }

    return $flash->get($accessor, $default);
}

/**
 * @param array $data
 * @param Closure $schema
 *
 * @return \Particle\Validator\ValidationResult
 */
function validate(array $data, Closure $schema)
{
    return \App\Validate::usingSchema($data, $schema);
}

/**
 * @param $key
 * @param mixed $default
 * @return mixed
 */
function old_input($key, $default = null)
{
    return resolve(\App\OldInput::class)->get($key, $default);
}

/**
 * @param string $markdown
 * @return string
 */
function markdown($markdown)
{
    return resolve(\Parsedown::class)->text($markdown);
}

/**
 * @param ... $params
 * @return string
 */
function hash_encode()
{
    return resolve('hash')->encode($params = func_get_args());
}

/**
 * @param $hash
 * @return array
 */
function hash_decode($hash)
{
    return resolve('hash')->decode($hash);
}

