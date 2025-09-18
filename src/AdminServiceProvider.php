<?php

namespace Base\Admin;

use Base\Admin\Layout\Content;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\AdminCommand::class,
        Console\MakeCommand::class,
        Console\ControllerCommand::class,
        Console\MenuCommand::class,
        Console\InstallCommand::class,
        Console\PublishCommand::class,
        Console\UninstallCommand::class,
        Console\ImportCommand::class,
        Console\CreateUserCommand::class,
        Console\ResetPasswordCommand::class,
        Console\ExtendCommand::class,
        Console\ExportSeedCommand::class,
        Console\MinifyCommand::class,
        Console\FormCommand::class,
        Console\PermissionCommand::class,
        Console\ActionCommand::class,
        Console\GenerateMenuCommand::class,
        Console\ConfigCommand::class,
        Console\DevLinksCommand::class,
    ];
    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'backend.auth'       => Middleware\Authenticate::class,
        'backend.throttle'   => Middleware\Throttle::class,
        'backend.pjax'       => Middleware\Pjax::class,
        'backend.log'        => Middleware\LogOperation::class,
        'backend.permission' => Middleware\Permission::class,
        'backend.bootstrap'  => Middleware\Bootstrap::class,
        'backend.session'    => Middleware\Session::class,
    ];
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'backend' => [
            'backend.auth',
            'backend.throttle',
            'backend.pjax',
            'backend.log',
            'backend.bootstrap',
            'backend.permission',
            //            'backend.session',
        ],
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(resource_path('views/layout/backend'), 'backend');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'backend');
        $this->ensureHttps();
        if(file_exists($routes = admin_path('routes.php'))) {
            $this->loadRoutesFrom($routes);
        }
        $this->registerPublishing();
        $this->compatibleBlade();
        $this->bladeDirectives();
    }

    /**
     * Force to set https scheme if https enabled.
     *
     * @return void
     */
    protected function ensureHttps()
    {
        if(config('backend.https') || config('backend.secure')) {
            url()->forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config' => config_path()], 'base-admin-config');
            $this->publishes([__DIR__ . '/../resources/lang' => resource_path('lang')], 'base-admin-lang');
            $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'base-admin-migrations');
            $this->publishes([__DIR__ . '/../resources/assets' => public_path('vendor/base')], 'base-admin-assets');
            $this->publishes([__DIR__ . '/../resources/assets/test' => public_path('vendor/base-admin-test')], 'base-admin-test');
        }
    }

    /**
     * Remove the default feature of double encoding enable in laravel 5.6 or later.
     *
     * @return void
     */
    protected function compatibleBlade()
    {
        $reflectionClass = new \ReflectionClass('\Illuminate\View\Compilers\BladeCompiler');
        if($reflectionClass->hasMethod('withoutDoubleEncoding')) {
            Blade::withoutDoubleEncoding();
        }
    }

    /**
     * Register the blade box directive.
     *
     * @return void
     */
    public function bladeDirectives()
    {
        Blade::directive('box', function($title) {
            return "<?php \$box = new \Base\Admin\Widgets\Box({$title}, '";
        });
        Blade::directive('endbox', function($expression) {
            return "'); echo \$box->render(); ?>";
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadAdminAuthConfig();
        $this->registerRouteMiddleware();
        $this->commands($this->commands);
        $this->macroRouter();
    }

    protected function loadAdminAuthConfig()
    {
        config(Arr::dot(config('backend.auth', []), 'auth.'));
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        foreach($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
        foreach($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }

    /**
     * Extends laravel router.
     */
    protected function macroRouter()
    {
        Router::macro('content', function($uri, $content, $options = []) {
            return $this->match(['GET', 'HEAD'], $uri, function(Content $layout) use ($content, $options) {
                return $layout
                    ->title(Arr::get($options, 'title', ' '))
                    ->description(Arr::get($options, 'desc', ' '))
                    ->body($content);
            });
        });
        Router::macro('component', function($uri, $component, $data = [], $options = []) {
            return $this->match(['GET', 'HEAD'], $uri, function(Content $layout) use ($component, $data, $options) {
                return $layout
                    ->title(Arr::get($options, 'title', ' '))
                    ->description(Arr::get($options, 'desc', ' '))
                    ->component($component, $data);
            });
        });
    }
}
