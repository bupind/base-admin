<?php

namespace Base\Admin\Controllers;

use Illuminate\Support\Arr;
use Base\Admin\Admin;

class Dashboard
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function title()
    {
        return view('backend::dashboard.title');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function environment()
    {
        $envs = [
            ['name' => 'PHP version',       'value' => 'PHP/'.PHP_VERSION],
            ['name' => 'Laravel version',   'value' => app()->version()],
            ['name' => 'CGI',               'value' => php_sapi_name()],
            ['name' => 'Uname',             'value' => php_uname()],
            ['name' => 'Server',            'value' => Arr::get($_SERVER, 'SERVER_SOFTWARE')],

            ['name' => 'Cache driver',      'value' => config('cache.default')],
            ['name' => 'Session driver',    'value' => config('session.driver')],
            ['name' => 'Queue driver',      'value' => config('queue.default')],

            ['name' => 'Timezone',          'value' => config('app.timezone')],
            ['name' => 'Locale',            'value' => config('app.locale')],
            ['name' => 'Env',               'value' => config('app.env')],
            ['name' => 'URL',               'value' => config('app.url')],
        ];

        return view('backend::dashboard.environment', compact('envs'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function extensions()
    {
        $extensions = [
            'helpers' => [
                'name' => 'base-admin-ext/helpers',
                'link' => 'https://github.com/base-admin-org/helpers',
                'icon' => 'cogs',
            ],
            'log-viewer' => [
                'name' => 'base-admin-ext/log-viewer',
                'link' => 'https://github.com/base-admin-org/log-viewer',
                'icon' => 'database',
            ],
            'backup' => [
                'name' => 'base-admin-ext/backup',
                'link' => 'https://github.com/base-admin-org/backup',
                'icon' => 'copy',
            ],
            'config' => [
                'name' => 'base-admin-ext/config',
                'link' => 'https://github.com/base-admin-org/config',
                'icon' => 'toggle-on',
            ],
            'api-tester' => [
                'name' => 'base-admin-ext/api-tester',
                'link' => 'https://github.com/base-admin-org/api-tester',
                'icon' => 'sliders-h',
            ],
            'media-manager' => [
                'name' => 'base-admin-ext/media-manager',
                'link' => 'https://github.com/base-admin-org/media-manager',
                'icon' => 'file',
            ],
            'scheduling' => [
                'name' => 'base-admin-ext/scheduling',
                'link' => 'https://github.com/base-admin-org/scheduling',
                'icon' => 'clock',
            ],
            'reporter' => [
                'name' => 'base-admin-ext/reporter',
                'link' => 'https://github.com/base-admin-org/reporter',
                'icon' => 'bug',
            ],
            'redis-manager' => [
                'name' => 'base-admin-ext/redis-manager',
                'link' => 'https://github.com/base-admin-org/redis-manager',
                'icon' => 'flask',
            ],
            'grid-sortable' => [
                'name' => 'base-admin-ext/grid-sortable',
                'link' => 'https://github.com/base-admin-org/grid-sortable',
                'icon' => 'arrows-alt-v',
            ],
        ];

        foreach ($extensions as &$extension) {
            $name = explode('/', $extension['name']);
            $extension['installed'] = array_key_exists(end($name), Admin::$extensions);
        }

        return view('backend::dashboard.extensions', compact('extensions'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function dependencies()
    {
        $json = file_get_contents(base_path('composer.json'));

        $dependencies = json_decode($json, true)['require'];

        return Admin::component('backend::dashboard.dependencies', compact('dependencies'));
    }
}
