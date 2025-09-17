<?php

namespace Base\Admin\Auth\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Base\Admin\Traits\DefaultDatetimeFormat;
use Base\Admin\Traits\ModelTree;

/**
 * Class Menu.
 *
 * @property int $id
 *
 * @method where($parent_id, $id)
 */
class Menu extends Model
{
    use DefaultDatetimeFormat;
    use ModelTree {
        ModelTree::boot as treeBoot;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'order', 'title', 'icon', 'uri', 'permission'];

    /**
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('backend.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('backend.database.menu_table'));

        parent::__construct($attributes);
    }

    /**
     * A Menu belongs to many roles.
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('backend.database.role_menu_table');

        $relatedModel = config('backend.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'menu_id', 'role_id');
    }

    public function allNodes(): array
    {
        $connection = config('backend.database.connection') ?: config('database.default');
        $orderColumn = DB::connection($connection)->getQueryGrammar()->wrap($this->orderColumn);

        $byOrder = 'ROOT ASC,'.$orderColumn;

        $query = static::query();

        if (config('backend.check_menu_roles') !== false) {
            $query->with('roles');
        }

        return $query->selectRaw('*, '.$orderColumn.' ROOT')->orderByRaw($byOrder)->get()->toArray();
    }

    /**
     * determine if enable menu bind permission.
     *
     * @return bool
     */
    public function withPermission()
    {
        return (bool) config('backend.menu_bind_permission');
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        static::treeBoot();

        static::deleting(function ($model) {
            $model->roles()->detach();
        });
    }
}
