<?php

namespace Base\Admin\Controllers;

use Illuminate\Support\Str;
use Base\Admin\Form;
use Base\Admin\Grid;
use Base\Admin\Show;

class PermissionController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('backend.permissions');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $permissionModel = config('backend.database.permissions_model');

        $grid = new Grid(new $permissionModel);

        $grid->column('id', 'ID')->sortable();
        $grid->column('slug', trans('backend.slug'));
        $grid->column('name', trans('backend.name'));

        $grid->column('http_path', trans('backend.route'))->display(function ($path) {
            return collect(explode("\n", $path))->map(function ($path) {
                $method = $this->http_method ?: ['ANY'];

                if (Str::contains($path, ':')) {
                    [$method, $path] = explode(':', $path);
                    $method = explode(',', $method);
                }

                $method = collect($method)->map(function ($name) {
                    return strtoupper($name);
                })->map(function ($name) {
                    return "<span class='badge bg-primary'>{$name}</span>";
                })->implode('&nbsp;');

                if (! empty(config('backend.route.prefix'))) {
                    $path = '/'.trim(config('backend.route.prefix'), '/').$path;
                }

                return "<div style='margin-bottom: 5px;'>$method<code>$path</code></div>";
            })->implode('');
        });

        $grid->column('created_at', trans('backend.created_at'));
        $grid->column('updated_at', trans('backend.updated_at'));

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
        $permissionModel = config('backend.database.permissions_model');

        $show = new Show($permissionModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('slug', trans('backend.slug'));
        $show->field('name', trans('backend.name'));

        $show->field('http_path', trans('backend.route'))->unescape()->as(function ($path) {
            return collect(explode("\r\n", $path))->map(function ($path) {
                $method = $this->http_method ?: ['ANY'];

                if (Str::contains($path, ':')) {
                    [$method, $path] = explode(':', $path);
                    $method = explode(',', $method);
                }

                $method = collect($method)->map(function ($name) {
                    return strtoupper($name);
                })->map(function ($name) {
                    return "<span class='badge bg-primary'>{$name}</span>";
                })->implode('&nbsp;');

                if (! empty(config('backend.route.prefix'))) {
                    $path = '/'.trim(config('backend.route.prefix'), '/').$path;
                }

                return "<div style='margin-bottom: 5px;'>$method<code>$path</code></div>";
            })->implode('');
        });

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

        $form = new Form(new $permissionModel);

        $form->display('id', 'ID');

        $form->text('slug', trans('backend.slug'))->rules('required');
        $form->text('name', trans('backend.name'))->rules('required');

        $form->multipleSelect('http_method', trans('backend.http.method'))
            ->options($this->getHttpMethodsOptions())
            ->help(trans('backend.all_methods_if_empty'));
        $form->textarea('http_path', trans('backend.http.path'));

        $form->display('created_at', trans('backend.created_at'));
        $form->display('updated_at', trans('backend.updated_at'));

        return $form;
    }

    /**
     * Get options of HTTP methods select field.
     *
     * @return array
     */
    protected function getHttpMethodsOptions()
    {
        $model = config('backend.database.permissions_model');

        return array_combine($model::$httpMethods, $model::$httpMethods);
    }
}
