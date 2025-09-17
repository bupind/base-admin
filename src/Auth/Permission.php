<?php

namespace Base\Admin\Auth;

use Base\Admin\Facades\Admin;
use Base\Admin\Middleware\Pjax;

class Permission
{
    /**
     * Check permission.
     *
     *
     * @return true
     */
    public static function check($permission)
    {
        if(static::isAdministrator()) {
            return true;
        }
        if(is_array($permission)) {
            collect($permission)->each(function($permission) {
                call_user_func([self::class, 'check'], $permission);
            });
            return;
        }
        if(Admin::user()->cannot($permission)) {
            static::error();
        }
    }

    public static function isAdministrator()
    {
        return Admin::user()->isRole('administrator');
    }

    /**
     * Send error response page.
     */
    public static function error()
    {
        $response = response(Admin::content()->withError(trans('backend.deny')));
        if(!request()->pjax() && request()->ajax()) {
            abort(403, trans('backend.deny'));
        }
        Pjax::respond($response);
    }

    /**
     * Roles allowed to access.
     *
     *
     * @return true
     */
    public static function allow($roles)
    {
        if(static::isAdministrator()) {
            return true;
        }
        if(!Admin::user()->inRoles($roles)) {
            static::error();
        }
    }

    /**
     * Don't check permission.
     *
     * @return bool
     */
    public static function free()
    {
        return true;
    }

    /**
     * Roles denied to access.
     *
     *
     * @return true
     */
    public static function deny($roles)
    {
        if(static::isAdministrator()) {
            return true;
        }
        if(Admin::user()->inRoles($roles)) {
            static::error();
        }
    }
}
