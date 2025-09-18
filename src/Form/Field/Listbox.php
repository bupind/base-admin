<?php

namespace Base\Admin\Form\Field;

/**
 * Class ListBox.
 *
 * @see https://github.com/istvan-ujjmeszaros/bootstrap-duallistbox
 */
class Listbox extends MultipleSelect
{
    protected $settings = [];
    /*
    protected static $css = [
        '/vendor/base/dual-listbox/dual-listbox.css',
        // overwritten bootstrap styles
    ];
    */

    protected static $js = [
        '/vendor/base/dual-listbox/dual-listbox-custom.js',
    ];

    public function settings(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);

        return $this;
    }

    /**
     * Set listbox height.
     *
     * @param  int  $height
     * @return Listbox
     */
    public function height($height = 200)
    {
        $this->settings(['minHeight' => $height]);

        return $this;
    }

    public function render()
    {
        $this->style('width', '100%');

        $settings = array_merge([
            'availableTitle' => trans('backend.listbox.title_available'),
            'selectedTitle' => trans('backend.listbox.title_selected'),
            'minHeight' => 200,
        ], $this->settings);

        $settings = json_encode($settings);

        $this->script .= <<<SCRIPT
        let dualListbox = new DualListbox("{$this->getElementClassSelector()}",$settings);


SCRIPT;

        // $this->attribute('data-value', implode(',', (array) $this->value()));

        return parent::render();
    }
}
