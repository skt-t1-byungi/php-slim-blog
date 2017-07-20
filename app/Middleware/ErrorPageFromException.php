<?php

namespace App\Middleware;

use App\HttpException;
use Slim\Exception\NotFoundException;

class ErrorPageFromException
{
    function __invoke($req, $res, $next)
    {
        // 개발시엔 에러를 노출합니다.
        if (getenv('DEV_MODE')) {
            return $next($req, $res);
        }

        try {
            return $next($req, $res);
        } catch (NotFoundException $exception) {
            return $this->notFound();
        } catch (HttpException $exception) {
            if ($exception->getCode() === 404) {
                return $this->notFound();
            }

            return $this->defaultErrorPage($exception->getCode(), ['error' => $exception]);
        } catch (\Exception $exception) {
            //로그기록
            error_log('An unexpected exception occurred in production. (' . $exception->getMessage() . ')');

            return $this->defaultErrorPage(500);
        }
    }

    /**
     * @return \App\Response
     */
    protected function notFound()
    {
        return view('errors/not-found')->withStatus(404);
    }

    /**
     * @param int $statusCode
     * @param array $data
     * @return \App\Response
     */
    protected function defaultErrorPage($statusCode = 500, array $data = [])
    {
        return view('errors/default', $data)->withStatus($statusCode);
    }
}