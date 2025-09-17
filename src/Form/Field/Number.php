<?php

namespace Base\Admin\Form\Field;

use Base\Admin\Form\Field\Traits\HasNumberModifiers;

class Number extends Text
{
    use HasNumberModifiers;

    protected static $js = [
        '/vendor/base-admin/fields/number-input.js',
    ];

    protected $view = 'backend::form.number';

    public function render()
    {
        $this->defaultAttribute('type', 'number');
        $this->append("<i class='icon-plus plus'></i>");
        $this->prepend("<i class='icon-minus minus'></i>");
        $this->default($this->default);

        if (
            empty($this->attributes['readonly']) &&
            empty($this->attributes['disabled'])
        ) {
            $this->script = <<<JS
            new NumberInput(document.querySelector('{$this->getElementClassSelector()}'));
            JS;
        }

        $this->style('max-width', '120px');

        return parent::render();
    }
}
