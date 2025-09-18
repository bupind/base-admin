<?php

namespace Base\Admin\Form;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class Tools implements Renderable
{
    protected $form;
    protected $tools = ['list'];
    protected $appends;
    protected $prepends;

    public function __construct(Builder $builder)
    {
        $this->form     = $builder;
        $this->appends  = new Collection;
        $this->prepends = new Collection;
    }

    /**
     * Prepend a tool.
     *
     * @param mixed $tool
     * @return $this
     */
    public function prepend($tool)
    {
        $this->prepends->push($tool);
        return $this;
    }

    /**
     * Get parent form of tool.
     *
     * @return Builder
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * Add a tool.
     *
     * @param string $tool
     * @return $this
     *
     * @deprecated use append instead.
     */
    public function add($tool)
    {
        return $this->append($tool);
    }

    /**
     * Append a tools.
     *
     * @param mixed $tool
     * @return $this
     */
    public function append($tool)
    {
        $this->appends->push($tool);
        return $this;
    }

    /**
     * Disable back button.
     *
     * @return $this
     *
     * @deprecated
     */
    public function disableBackButton() { }

    /**
     * Disable list button.
     *
     * @return $this
     *
     * @deprecated Use disableList instead.
     */
    public function disableListButton()
    {
        return $this->disableList();
    }

    /**
     * Disable `list` tool.
     *
     * @return $this
     */
    public function disableList(bool $disable = true)
    {
        if($disable) {
            array_delete($this->tools, 'list');
        } elseif(!in_array('list', $this->tools)) {
            array_push($this->tools, 'list');
        }
        return $this;
    }

    /**
     * Render list button.
     *
     * @return string
     */
    protected function renderList()
    {
        $text = trans('backend.list');
        return <<<HTML
            <div class="btn-group">
                <a href="{$this->getListPath()}" class="btn btn-sm btn-default btn-light me-2" title="{$text}"><i class="icon-list"></i><span class="hidden-xs">&nbsp;{$text}</span></a>
            </div>
            HTML;
    }

    /**
     * Get request path for resource list.
     *
     * @return string
     */
    protected function getListPath()
    {
        return $this->form->getResource();
    }

    /**
     * Render list button.
     *
     * @return string
     */
    protected function renderView()
    {
        $view = trans('backend.view');
        return <<<HTML
            <div class="btn-group">
                <a href="{$this->getViewPath()}" class="btn btn-sm btn-primary me-2" title="{$view}">
                    <i class="icon-eye"></i><span class="hidden-xs"> {$view}</span>
                </a>
            </div>
            HTML;
    }

    /**
     * Get request path for delete.
     *
     * @return string
     */
    protected function getViewPath()
    {
        $key = $this->form->getResourceId();
        if($key) {
            return $this->getListPath() . '/' . $key;
        } else {
            return $this->getListPath();
        }
    }

    /**
     * Render `delete` tool.
     *
     * @return string
     */
    protected function renderDelete()
    {
        $trans = [
            'delete' => trans('backend.delete'),
        ];
        return <<<HTML
            <div class="btn-group">
                <a  onclick="backend.resource.delete(event,this)" data-url="{$this->getDeletePath()}" data-list_url="{$this->getListPath()}" class="btn btn-sm btn-danger delete" title="{$trans['delete']}">
                    <i class="icon-trash"></i><span class="hidden-xs">  {$trans['delete']}</span>
                </a>
            </div>
            HTML;
    }

    /**
     * Get request path for edit.
     *
     * @return string
     */
    protected function getDeletePath()
    {
        return $this->getViewPath();
    }

    /**
     * Render custom tools.
     *
     * @param Collection $tools
     * @return mixed
     */
    protected function renderCustomTools($tools)
    {
        if($this->form->isCreating()) {
            $this->disableView();
            $this->disableDelete();
        }
        if(empty($tools)) {
            return '';
        }
        return $tools->map(function($tool) {
            if($tool instanceof Renderable) {
                return $tool->render();
            }
            if($tool instanceof Htmlable) {
                return $tool->toHtml();
            }
            return (string)$tool;
        })->implode(' ');
    }

    /**
     * Disable `edit` tool.
     *
     * @return $this
     */
    public function disableView(bool $disable = true)
    {
        if($disable) {
            array_delete($this->tools, 'view');
        } elseif(!in_array('view', $this->tools)) {
            array_push($this->tools, 'view');
        }
        return $this;
    }

    /**
     * Disable `delete` tool.
     *
     * @return $this
     */
    public function disableDelete(bool $disable = true)
    {
        if($disable) {
            array_delete($this->tools, 'delete');
        } elseif(!in_array('delete', $this->tools)) {
            array_push($this->tools, 'delete');
        }
        return $this;
    }

    /**
     * Render tools.
     *
     * @return string
     */
    public function render()
    {
        $output = $this->renderCustomTools($this->prepends);
        foreach($this->tools as $tool) {
            $renderMethod = 'render' . ucfirst($tool);
            $output       .= $this->$renderMethod();
        }
        return $output . $this->renderCustomTools($this->appends);
    }
}
