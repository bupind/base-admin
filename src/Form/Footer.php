<?php

namespace Base\Admin\Form;

use Illuminate\Contracts\Support\Renderable;

class Footer implements Renderable
{
    public    $fixedFooter = false;
    protected $view        = 'backend::form.footer';
    protected $builder;
    protected $buttons     = ['reset', 'submit'];
    protected $defaultCheck;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function disableReset(bool $disable = true)
    {
        if($disable) {
            array_delete($this->buttons, 'reset');
        } elseif(!in_array('reset', $this->buttons)) {
            array_push($this->buttons, 'reset');
        }
        return $this;
    }

    public function disableSubmit(bool $disable = true)
    {
        if($disable) {
            array_delete($this->buttons, 'submit');
        } elseif(!in_array('submit', $this->buttons)) {
            array_push($this->buttons, 'submit');
        }
        return $this;
    }

    public function fixedFooter($set = true)
    {
        $this->fixedFooter = $set;
        return $this;
    }

    /**
     * Render footer.
     *
     * @return string
     */
    public function render()
    {
        $data = [
            'width'       => $this->builder->getWidth(),
            'buttons'     => $this->buttons,
            'fixedFooter' => $this->fixedFooter,
        ];
        return view($this->view, $data)->render();
    }
}
