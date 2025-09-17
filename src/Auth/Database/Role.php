<?php

namespace Base\Admin\Auth\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Base\Admin\Traits\DefaultDatetimeFormat;

class Role extends Model
{
    use DefaultDatetimeFormat;

    protected $fillable = ['name', 'slug'];

    /**
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('backend.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('backend.database.roles_table'));

        parent::__construct($attributes);
    }

    /**
     * A role belongs to many users.
     */
    public function administrators(): BelongsToMany
    {
        $pivotTable = config('backend.database.role_users_table');

        $relatedModel = config('backend.database.users_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'user_id');
    }

    /**
     * A role belongs to many permissions.
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('backend.database.role_permissions_table');

        $relatedModel = config('backend.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'permission_id');
    }

    /**
     * A role belongs to many menus.
     */
    public function menus(): BelongsToMany
    {
        $pivotTable = config('backend.database.role_menu_table');

        $relatedModel = config('backend.database.menu_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'menu_id');
    }

    /**
     * Check user has permission.
     */
    public function can(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Check user has no permission.
     */
    public function cannot(string $permission): bool
    {
        return ! $this->can($permission);
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->administrators()->detach();

            $model->permissions()->detach();
        });
    }
}
