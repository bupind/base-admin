<?php

namespace Base\Admin\Controllers;

use Illuminate\Support\Facades\Hash;
use Base\Admin\Form;
use Base\Admin\Grid;
use Base\Admin\Show;

class UserController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('backend.administrator');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $userModel = config('backend.database.users_model');

        $grid = new Grid(new $userModel);

        $grid->column('id', 'ID')->sortable();
        $grid->column('username', trans('backend.username'));
        $grid->column('name', trans('backend.name'));
        $grid->column('roles', trans('backend.roles'))->pluck('name')->label();
        $grid->column('created_at', trans('backend.created_at'));
        $grid->column('updated_at', trans('backend.updated_at'));

        $grid->actions(function (Grid\Displayers\Actions\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param  mixed  $id
     * @return Show
     */
    protected function detail($id)
    {
        $userModel = config('backend.database.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('username', trans('backend.username'));
        $show->field('name', trans('backend.name'));
        $show->field('roles', trans('backend.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->field('permissions', trans('backend.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->field('created_at', trans('backend.created_at'));
        $show->field('updated_at', trans('backend.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $userModel = config('backend.database.users_model');
        $permissionModel = config('backend.database.permissions_model');
        $roleModel = config('backend.database.roles_model');

        $form = new Form(new $userModel);

        $userTable = config('backend.database.users_table');
        $connection = config('backend.database.connection');

        $form->display('id', 'ID');
        $form->text('username', trans('backend.username'))
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},username,{{id}}"]);

        $form->text('name', trans('backend.name'))->rules('required');
        $form->image('avatar', trans('backend.avatar'));
        $form->password('password', trans('backend.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('backend.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('backend.roles'))->options($roleModel::all()->pluck('name', 'id'));
        $form->multipleSelect('permissions', trans('backend.permissions'))->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('backend.created_at'));
        $form->display('updated_at', trans('backend.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });

        return $form;
    }
}
