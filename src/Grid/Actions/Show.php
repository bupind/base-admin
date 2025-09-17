<?php

namespace Base\Admin\Grid\Actions;

use Base\Admin\Actions\RowAction;

class Show extends RowAction
{
    public $icon = 'icon-eye';

    /**
     * @return array|null|string
     */
    public function name()
    {
        return __('backend.show');
    }

    /**
     * @return string
     */
    public function href()
    {
        return "{$this->getResource()}/{$this->getKey()}";
    }
}
