<?php

namespace Base\Admin\Grid\Tools;

use Base\Admin\Grid;

class CreateButton extends AbstractTool
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Create a new CreateButton instance.
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render()
    {
        if (! $this->grid->showCreateBtn()) {
            return '';
        }

        $new = trans('backend.new');

        return <<<HTML
        <a href="{$this->grid->getCreateUrl()}" class="btn btn-sm btn-success grid-create-btn" title="{$new}">
            <i class="icon-plus"></i><span class="hidden-xs">{$new}</span>
        </a>
        HTML;
    }
}
