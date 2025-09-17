<?php

namespace Base\Admin\Grid\Tools;

use Base\Admin\Admin;
use Base\Admin\Grid;

class ExportButton extends AbstractTool
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Create a new Export button instance.
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Render Export button.
     *
     * @return string
     */
    public function render()
    {
        if (! $this->grid->showExportBtn()) {
            return '';
        }
        $page = request('page', 1);

        return Admin::component('backend::components.export-btn', [
            'page' => $page,
            'grid' => $this->grid,
        ]);
    }
}
