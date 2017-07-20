<?php

require_once "../bootstrap.php";

$app = new \App\App();

require_once ROOT_DIR . "/routes.php";

$app
    ->add(\App\Middleware\ShareOldInput::class)
    ->add(\App\Middleware\ShareErrors::class)
    ->add(\App\Middleware\CsrfGuard::class)
    ->add(\App\Middleware\ErrorPageFromException::class)
    ->add(\App\Middleware\TrailingSlash::class);

// 일부 oauth 서비스는 등록된 도메인만 허용합니다.
// 따라서 개발할 때가 아닌 경우는 기본 url 접속만 허용합니다.
if (!getenv('DEV_MODE')) {
    $app->add(\App\Middleware\RedirectHttpBaseUrl::class);
}

// 쿼리 디버그
if (getenv('SHOW_QUERIES')) {
    $queries = [];
    $app->run(true);
    dd($queries);
}

$app->run();