<?php

namespace Base\Admin\Form\Field;

use Base\Admin\Form\Field;

class Nullable extends Field
{
    public function __construct() {}

    public function __call($method, $parameters)
    {
        return $this;
    }
}
