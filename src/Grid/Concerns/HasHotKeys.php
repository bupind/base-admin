<?php

namespace Base\Admin\Grid\Concerns;

use Base\Admin\Admin;

trait HasHotKeys
{
    protected function addHotKeyScript()
    {
        $filterID = $this->getFilter()->getFilterID();

        $refreshMessage = __('backend.refresh_succeeded');

        $script = <<<'SCRIPT'

            backend.grid.hotkeys();


SCRIPT;

        Admin::script($script);
    }

    public function enableHotKeys()
    {
        $this->addHotKeyScript();

        return $this;
    }
}
