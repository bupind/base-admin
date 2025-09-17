<?php

namespace Base\Admin\Controllers;

use Illuminate\Routing\Controller;
use Base\Admin\Form;
use Base\Admin\Layout\Column;
use Base\Admin\Layout\Content;
use Base\Admin\Layout\Row;
use Base\Admin\Tree;
use Base\Admin\Widgets\Box;

class MenuController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->title(trans('backend.menu'))
            ->description(trans('backend.list'))
            ->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());

                $row->column(6, function (Column $column) {
                    $form = new \Base\Admin\Widgets\Form;
                    $form->action(admin_url('auth/menu'));

                    $menuModel = config('backend.database.menu_model');
                    $permissionModel = config('backend.database.permissions_model');
                    $roleModel = config('backend.database.roles_model');

                    $form->select('parent_id', trans('backend.parent_id'))->options($menuModel::selectOptions());
                    $form->text('title', trans('backend.title'))->rules('required');
                    $form->icon('icon', trans('backend.icon'))->default('fa-bars')->rules('required')->help($this->iconHelp());
                    $form->text('uri', trans('backend.uri'));
                    $form->multipleSelect('roles', trans('backend.roles'))->options($roleModel::all()->pluck('name', 'id'));
                    if ((new $menuModel)->withPermission()) {
                        $form->select('permission', trans('backend.permission'))->options($permissionModel::pluck('name', 'slug'));
                    }
                    $form->hidden('_token')->default(csrf_token());

                    $column->append((new Box(trans('backend.new'), $form))->style('success'));
                });
            });
    }

    /**
     * Redirect to edit page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        return redirect()->route('backend.auth.menu.edit', ['menu' => $id]);
    }

    /**
     * @return \Base\Admin\Tree
     */
    protected function treeView()
    {
        $menuModel = config('backend.database.menu_model');

        $tree = new Tree(new $menuModel);

        $tree->disableCreate();

        $tree->branch(function ($branch) {
            $payload = "<i class='{$branch['icon']}'></i>&nbsp;<strong>{$branch['title']}</strong>";

            if (! isset($branch['children'])) {
                if (url()->isValidUrl($branch['uri'])) {
                    $uri = $branch['uri'];
                } else {
                    $uri = admin_url($branch['uri']);
                }

                $payload .= "&nbsp;&nbsp;&nbsp;<a href=\"$uri\" class=\"dd-nodrag\">$uri</a>";
            }

            return $payload;
        });

        return $tree;
    }

    /**
     * Edit interface.
     *
     * @param  string  $id
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->title(trans('backend.menu'))
            ->description(trans('backend.edit'))
            ->row($this->form()->edit($id));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $menuModel = config('backend.database.menu_model');
        $permissionModel = config('backend.database.permissions_model');
        $roleModel = config('backend.database.roles_model');

        $form = new Form(new $menuModel);

        $form->display('id', 'ID');

        $form->select('parent_id', trans('backend.parent_id'))->options($menuModel::selectOptions());
        $form->text('title', trans('backend.title'))->rules('required');
        $form->icon('icon', trans('backend.icon'))->default('fa-bars')->rules('required')->help($this->iconHelp());
        $form->text('uri', trans('backend.uri'));
        $form->multipleSelect('roles', trans('backend.roles'))->options($roleModel::all()->pluck('name', 'id'));
        if ($form->model()->withPermission()) {
            $form->select('permission', trans('backend.permission'))->options($permissionModel::pluck('name', 'slug'));
        }

        $form->display('created_at', trans('backend.created_at'));
        $form->display('updated_at', trans('backend.updated_at'));

        return $form;
    }

    /**
     * Help message for icon field.
     *
     * @return string
     */
    protected function iconHelp()
    {
        return 'For more icons please see <a href="http://fontawesome.io/icons/" target="_blank">http://fontawesome.io/icons/</a>';
    }
}
