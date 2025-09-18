<?php

namespace Base\Admin\Traits;

use Base\Admin\Facades\Admin;

trait BootExtension
{
    public static function boot()
    {
        static::registerRoutes();
        Admin::extend('log-viewer', __CLASS__);
    }

    protected static function registerRoutes()
    {
        parent::routes(function($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('logs', 'Base\Admin\Controllers\LogController@index')->name('log-viewer-index');
            $router->get('logs/{file}', 'Base\Admin\Controllers\LogController@index')->name('log-viewer-file');
            $router->get('logs/{log}/tail', 'Base\Admin\Controllers\LogController@tail')->name('log-viewer-tail');
        });
    }

    public static function import()
    {
        parent::createMenu('Log viewer', 'logs', 'icon-exclamation-triangle');
        parent::createPermission('Logs', 'log-viewer', 'logs*');
    }
}
