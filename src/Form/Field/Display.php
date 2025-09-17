<?php

namespace Base\Admin\Form\Field;

use Base\Admin\Form\Field;

class Display extends Field
{
    public function prepare($value)
    {
        return $this->original();
    }
}
