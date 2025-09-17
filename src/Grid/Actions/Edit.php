<?php

namespace Base\Admin\Grid\Actions;

use Base\Admin\Actions\RowAction;

class Edit extends RowAction
{
    public $icon = 'icon-pen';

    /**
     * @return array|null|string
     */
    public function name()
    {
        return __('backend.edit');
    }

    /**
     * @return string
     */
    public function href()
    {
        return "{$this->getResource()}/{$this->getKey()}/edit";
    }
}
