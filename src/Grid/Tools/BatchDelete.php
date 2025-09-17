<?php

namespace Base\Admin\Grid\Tools;

use Base\Admin\Actions\BatchAction;

class BatchDelete extends BatchAction
{
    public $icon = 'icon-trash';

    public function __construct()
    {
        $this->name = trans('backend.batch_delete');
    }

    /**
     * Script of batch delete action.
     */
    public function script()
    {
        return <<<JS
        document.querySelector('{$this->getSelector()}').addEventListener("click",function(){
            let resource_url = '{$this->resource}/' + backend.grid.selected.join();
            backend.resource.batch_delete(resource_url);
        });
JS;
    }
}
