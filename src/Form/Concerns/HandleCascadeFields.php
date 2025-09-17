<?php

namespace Base\Admin\Form\Concerns;

use Base\Admin\Form\Field;

trait HandleCascadeFields
{
    public function cascadeGroup(\Closure $closure, array $dependency)
    {
        $this->pushField($group = new Field\CascadeGroup($dependency));

        call_user_func($closure, $this);

        $group->end();
    }
}
