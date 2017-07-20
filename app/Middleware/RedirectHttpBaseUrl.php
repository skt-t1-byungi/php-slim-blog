<?php

namespace App\Middleware;

use Slim\Http\Request;

class RedirectHttpBaseUrl
{
    function __invoke(Request $req, $res, $next)
    {
        $envUrl = getenv('HTTP_BASE_URL');

        //포트검사는 생략합니다.
        if (strpos($envUrl, $this->requestUrl($req)) === false) {
            return redirect($envUrl);
        }

        return $next($req, $res);
    }

    private function requestUrl(Request $req)
    {
        $uri = $req->getUri();

        return $uri->getScheme() . '://' . $uri->getHost();
    }
}