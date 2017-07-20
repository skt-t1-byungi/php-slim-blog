<?php

namespace App;

use Carbon\Carbon;
use DI\Bridge\Slim\App as DIApp;
use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Events\Dispatcher;
use Illuminate\Pagination\Paginator;
use Slim\Http\Request;

class App extends DIApp
{
    protected function configureContainer(ContainerBuilder $builder)
    {
        $builder->addDefinitions(ROOT_DIR . "/definitions.php");
    }

    public function run($silent = false)
    {
        $this->boot();
        return parent::run($silent);
    }

    protected function boot()
    {
        $this->bootCarbonLanguage();
        $this->bootDatabase();
        $this->bootPagination();
        $this->bootSession();
    }

    protected function bootCarbonLanguage()
    {
        Carbon::setLocale('ko');
    }

    public function bootDatabase()
    {
        $db = $this->getContainer()->get(Manager::class);

        $db->addConnection([
            'driver'    => getenv('DB_DRIVER'),
            'host'      => getenv('DB_HOST'),
            'port'      => getenv('DB_PORT'),
            'database'  => getenv('DB_DATABASE'),
            'username'  => getenv('DB_USERNAME'),
            'password'  => getenv('DB_PASSWORD'),
            'charset'   => getenv('DB_CHARSET'),
            'collation' => getenv('DB_COLLATION')
        ]);

        //eloquent event 을 사용하기 위해선 event dispatcher 을 세팅해야합니다.
        $db->setEventDispatcher(new Dispatcher());
        $db->setAsGlobal();
        $db->bootEloquent();

        //쿼리 디버그
        if (getenv('SHOW_QUERIES')) {
            $db->getConnection()->listen(function (QueryExecuted $sql) {
                $GLOBALS['queries'][] = $sql;
            });
        }
    }

    protected function bootPagination()
    {
        /**
         * @var $request Request
         */
        $request = $this->getContainer()->get('request');

        Paginator::currentPathResolver(function () use ($request) {
            return $request->getUri()->getPath();
        });

        Paginator::currentPageResolver(function () use ($request) {
            return (int)$request->getParam('page', 1);
        });
    }

    protected function bootSession()
    {
        session_name('_uid');
        session_start();

        if (rand(1, 10) <= 1) {
            session_regenerate_id();
        }
    }
}