<?php

use Illuminate\Database\Capsule\Manager;
use Slim\Http\Request;
use function DI\decorate;
use function DI\get;
use function DI\object;

return [
    'settings.determineRouteBeforeAppMiddleware' => false,
    'settings.displayErrorDetails'               => true,

    'notFoundHandler' => decorate(function (callable $slimHandler) {
        // 개발시에는 슬림 기본 예외페이지로 출력되게 합니다.
        if (getenv('DEV_MODE')) {
            return $slimHandler;
        }

        return function ($request, $response) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        };
    }),

    Twig_Environment::class => function () {
        $loader = new Twig_Loader_Filesystem(ROOT_DIR . '/view');

        $twig = new Twig_Environment($loader, [
            'cache' => getenv('DEV_MODE') ? false : ROOT_DIR . '/view/cache'
        ]);

        $twig->addExtension(new \App\TwigExtension);

        return $twig;
    },

    'twig' => get(Twig_Environment::class),

    'db' => get(Manager::class),

    Request::class => get('request'),

    \Hashids\Hashids::class => object(\Hashids\Hashids::class)->constructor('byungi', 8),

    'hash' => get(\Hashids\Hashids::class)
];