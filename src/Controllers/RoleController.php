<?php

namespace Base\Admin\Controllers;

use Base\Admin\Form;
use Base\Admin\Grid;
use Base\Admin\Show;

class RoleController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('backend.roles');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $roleModel = config('backend.database.roles_model');

        $grid = new Grid(new $roleModel);

        $grid->column('id', 'ID')->sortable();
        $grid->column('slug', trans('backend.slug'));
        $grid->column('name', trans('backend.name'));

        $grid->column('permissions', trans('backend.permission'))->pluck('name')->label();

        $grid->column('created_at', trans('backend.created_at'));
        $grid->column('updated_at', trans('backend.updated_at'));

        $grid->actions(function (Grid\Displayers\Actions\Actions $actions) {
            if ($actions->row->slug == 'administrator') {
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
        $roleModel = config('backend.database.roles_model');

        $show = new Show($roleModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('slug', trans('backend.slug'));
        $show->field('name', trans('backend.name'));
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
        $permissionModel = config('backend.database.permissions_model');
        $roleModel = config('backend.database.roles_model');

        $form = new Form(new $roleModel);

        $form->display('id', 'ID');

        $form->text('slug', trans('backend.slug'))->rules('required');
        $form->text('name', trans('backend.name'))->rules('required');
        $form->listbox('permissions', trans('backend.permissions'))->options($permissionModel::all()->pluck('name', 'id'))->height(300);

        $form->display('created_at', trans('backend.created_at'));
        $form->display('updated_at', trans('backend.updated_at'));

        return $form;
    }
}
