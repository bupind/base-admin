<?php

namespace Base\Admin\Controllers;

use Illuminate\Routing\Controller;
use Base\Admin\Layout\Content;
use Base\Admin\Traits\HasCustomHooks;

class AdminController extends Controller
{
    use HasCustomHooks;
    use HasResourceActions;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Title';

    /**
     * Set description for following 4 action pages.
     *
     * @var array
     */
    protected $description = [
        //        'index'  => 'Index',
        //        'show'   => 'Show',
        //        'edit'   => 'Edit',
        //        'create' => 'Create',
    ];

    /**
     * Get content title.
     *
     * @return string
     */
    protected function title()
    {
        return $this->title;
    }

    /**
     * Index interface.
     *
     *
     * @return Content
     */
    public function index(Content $content)
    {
        $grid = $this->grid();
        if ($this->hasHooks('alterGrid')) {
            $grid = $this->callHooks('alterGrid', $grid);
        }

        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('backend.list'))
            ->body($grid);
    }

    /**
     * Show interface.
     *
     * @param  mixed  $id
     * @return Content
     */
    public function show($id, Content $content)
    {
        $detail = $this->detail($id);
        if ($this->hasHooks('alterDetail')) {
            $detail = $this->callHooks('alterDetail', $detail);
        }

        return $content
            ->title($this->title())
            ->description($this->description['show'] ?? trans('backend.show'))
            ->body($detail);
    }

    /**
     * Edit interface.
     *
     * @param  mixed  $id
     * @return Content
     */
    public function edit($id, Content $content)
    {
        $form = $this->form();
        if ($this->hasHooks('alterForm')) {
            $form = $this->callHooks('alterForm', $form);
        }

        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('backend.edit'))
            ->body($form->edit($id));
    }

    /**
     * Create interface.
     *
     *
     * @return Content
     */
    public function create(Content $content)
    {
        $form = $this->form();
        if ($this->hasHooks('alterForm')) {
            $form = $this->callHooks('alterForm', $form);
        }

        return $content
            ->title($this->title())
            ->description($this->description['create'] ?? trans('backend.create'))
            ->body($form);
    }
}
