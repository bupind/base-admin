<?php

namespace Base\Admin\Grid\Displayers;

use Base\Admin\Admin;

class Upload extends AbstractDisplayer
{
    public function display($multiple = false)
    {
        return Admin::component('backend::grid.inline-edit.upload', [
            'key' => $this->getKey(),
            'name' => $this->getPayloadName(),
            'value' => $this->getValue(),
            'target' => "inline-upload-{$this->getKey()}",
            'resource' => $this->getResource(),
            'multiple' => $multiple,
        ]);
    }
}
