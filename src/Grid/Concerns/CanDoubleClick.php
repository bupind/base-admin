<?php

namespace Base\Admin\Grid\Concerns;

use Base\Admin\Admin;

trait CanDoubleClick
{
    /**
     * Double-click grid row to jump to the edit page.
     *
     * @return $this
     */
    public function enableDblClick()
    {
        $script = <<<SCRIPT
        document.body.addEventListener('dblclick', function (e) {
            tr = e.target.closest("tr");
            if (tr && tr.dataset.key){
                var url = "{$this->resource()}/"+tr.dataset.key+"/edit";
                backend.ajax.navigate(url);
            }
        });
SCRIPT;
        Admin::script($script);

        return $this;
    }
}
