<?php

namespace Base\Admin;

use Base\Admin\Auth\Database\Menu;
use Base\Admin\Controllers\AuthController;
use Base\Admin\Layout\Content;
use Base\Admin\Traits\HasAssets;
use Base\Admin\Widgets\Navbar;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class Admin
{
    use HasAssets;

    public const VERSION = '1.0.0';
    public static    $metaTitle;
    public static    $favicon;
    public static    $extensions       = [];
    protected static $bootingCallbacks = [];
    protected static $bootedCallbacks  = [];
    protected        $navbar;
    protected        $menu             = [];

    public static function getLongVersion()
    {
        return sprintf('base-admin <comment>version</comment> <info>%s</info>', self::VERSION);
    }

    public static function setTitle($title)
    {
        self::$metaTitle = $title;
    }

    public static function extend($name, $class)
    {
        static::$extensions[$name] = $class;
    }

    public static function booting(callable $callback)
    {
        static::$bootingCallbacks[] = $callback;
    }

    public static function booted(callable $callback)
    {
        static::$bootedCallbacks[] = $callback;
    }

    public static function asset($asset)
    {
        return url('/vendor/base/' . $asset);
    }

    public static function js_trans()
    {
        $lang_array = json_encode(__('backend'));
        return '<script>var admin_lang_arr = ' . $lang_array . '</script>';
    }

    public function grid($model, Closure $callable)
    {
        return new Grid($this->getModel($model), $callable);
    }

    public function getModel($model)
    {
        if($model instanceof Model) {
            return $model;
        }
        if(is_string($model) && class_exists($model)) {
            return $this->getModel(new $model);
        }
        throw new InvalidArgumentException("$model is not a valid model");
    }

    public function form($model, Closure $callable)
    {
        return new Form($this->getModel($model), $callable);
    }

    public function tree($model, ?Closure $callable = null)
    {
        return new Tree($this->getModel($model), $callable);
    }

    public function show($model, $callable = null)
    {
        return new Show($this->getModel($model), $callable);
    }

    public function content(?Closure $callable = null)
    {
        return new Content($callable);
    }

    public function menuLinks($menu = [])
    {
        if(empty($menu)) {
            $menu = $this->menu();
        }
        $links = [];
        foreach($menu as $item) {
            if(!empty($item['children'])) {
                $links = array_merge($links, $this->menuLinks($item['children']));
            } else {
                $links[] = Arr::only($item, ['title', 'uri', 'icon']);
            }
        }
        return $links;
    }

    public function menu()
    {
        if(!empty($this->menu)) {
            return $this->menu;
        }
        $menuClass = config('backend.database.menu_model');
        /** @var Menu $menuModel */
        $menuModel = new $menuClass;
        return $this->menu = $menuModel->toTree();
    }

    public function title()
    {
        return self::$metaTitle ? self::$metaTitle : config('backend.title');
    }

    public function favicon($favicon = null)
    {
        if(is_null($favicon)) {
            return static::$favicon;
        }
        static::$favicon = $favicon;
    }

    public function user()
    {
        return static::guard()->user();
    }

    public function guard()
    {
        return Auth::guard(static::guardName());
    }

    public function guardName()
    {
        return config('backend.auth.guard') ?: 'backend';
    }

    public function navbar(?Closure $builder = null)
    {
        if(is_null($builder)) {
            return $this->getNavbar();
        }
        call_user_func($builder, $this->getNavbar());
    }

    public function getNavbar()
    {
        if(is_null($this->navbar)) {
            $this->navbar = new Navbar;
        }
        return $this->navbar;
    }

    public function registerAuthRoutes()
    {
        $this->routes();
    }

    public function routes()
    {
        $attributes = [
            'prefix'     => config('backend.route.prefix'),
            'middleware' => config('backend.route.middleware'),
        ];
        app('router')->group($attributes, function($router) {
            /* @var \Illuminate\Support\Facades\Route $router */
            $router->namespace('\Base\Admin\Controllers')->group(function($router) {
                /* @var \Illuminate\Routing\Router $router */
                $router->resource('auth/users', 'UserController')->names('backend.auth.users');
                $router->resource('auth/roles', 'RoleController')->names('backend.auth.roles');
                $router->resource('auth/permissions', 'PermissionController')->names('backend.auth.permissions');
                $router->resource('auth/menu', 'MenuController', ['except' => ['create']])->names('backend.auth.menu');
                $router->resource('auth/logs', 'LogController', ['only' => ['index', 'destroy']])
                    ->names('backend.auth.logs');
                $router->post('_handle_form_', 'HandleController@handleForm')->name('backend.handle-form');
                $router->post('_handle_action_', 'HandleController@handleAction')->name('backend.handle-action');
                $router->get('_handle_selectable_', 'HandleController@handleSelectable')
                    ->name('backend.handle-selectable');
                $router->get('_handle_renderable_', 'HandleController@handleRenderable')
                    ->name('backend.handle-renderable');
            });
            $authController = config('backend.auth.controller', AuthController::class);
            /* @var \Illuminate\Routing\Router $router */
            $router->get('auth/login', $authController . '@getLogin')->name('backend.login');
            $router->post('auth/login', $authController . '@postLogin');
            $router->get('auth/logout', $authController . '@getLogout')->name('backend.logout');
            $router->get('auth/setting', $authController . '@getSetting')->name('backend.setting');
            $router->put('auth/setting', $authController . '@putSetting');
        });
    }

    public function bootstrap()
    {
        $this->fireBootingCallbacks();
        require config('backend.bootstrap', admin_path('bootstrap.php'));
        $this->addAdminAssets();
        $this->fireBootedCallbacks();
    }

    protected function fireBootingCallbacks()
    {
        foreach(static::$bootingCallbacks as $callable) {
            call_user_func($callable);
        }
    }

    protected function addAdminAssets()
    {
        $assets = Form::collectFieldAssets();
        self::css($assets['css']);
        self::js($assets['js']);
    }

    protected function fireBootedCallbacks()
    {
        foreach(static::$bootedCallbacks as $callable) {
            call_user_func($callable);
        }
    }
}
