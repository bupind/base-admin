<?php

namespace Base\Admin\Actions;

use Base\Admin\Grid\Column;
use Illuminate\Http\Request;

abstract class RowAction extends GridAction
{
    public    $selectorPrefix = '.grid-row-action-';
    protected $row;
    protected $column;
    protected $asColumn       = false;
    protected $extraClasses   = [];

    public function setActionClass(string $class): self
    {
        $parts              = preg_split('/\s+/', trim($class)) ?: [];
        $this->extraClasses = array_values(array_unique(array_merge($this->extraClasses, $parts)));
        return $this;
    }

    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set row model.
     *
     * @param \Illuminate\Database\Eloquent\Model $row
     * @return $this
     */
    public function setRow($row)
    {
        $this->row = $row;
        return $this;
    }

    /**
     * @return $this
     */
    public function setColumn(Column $column)
    {
        $this->column = $column;
        return $this;
    }

    /**
     * Show this action as a column.
     *
     * @return $this
     */
    public function asColumn()
    {
        $this->asColumn = true;
        return $this;
    }

    /**
     * @return mixed
     */
    public function retrieveModel(Request $request)
    {
        if(!$key = $request->get('_key')) {
            return false;
        }
        $modelClass = str_replace('_', '\\', $request->get('_model'));
        if($this->modelUseSoftDeletes($modelClass)) {
            return $modelClass::withTrashed()->findOrFail($key);
        }
        return $modelClass::findOrFail($key);
    }

    public function render()
    {
        $linkClass = ($this->parent->getActionClass() != "Base\Admin\Grid\Displayers\Actions\Actions") ? 'dropdown-item' : '';
        $icon = $this->getIcon();
        $extra = $this->extraClassesString();
        if ($href = $this->href()) {
            return "<a href='{$href}' class='btn btn-info {$linkClass} {$extra}'>{$icon}<span class='label'>{$this->name()}</span></a>";
        }
        $this->addScript();
        $attributes = $this->formatAttributes();

        return sprintf(
            "<a data-_key='%s' href='javascript:void(0);' class='btn %s {$linkClass} {$extra}' {$attributes}>{$icon}<span class='label'>%s</span></a>",
            $this->getKey(),
            $extra,
            $this->getElementClass(),
            $this->asColumn ? $this->display($this->row($this->column->getName())) : $this->name()
        );
    }

    protected function extraClassesString(): string
    {
        return implode(' ', $this->extraClasses);
    }

    /**
     * @return string
     */
    public function href() { }

    /**
     * Get primary key value of current row.
     *
     * @return mixed
     */
    protected function getKey()
    {
        return $this->row->getKey();
    }

    public function display($value) { }

    /**
     * Set row model.
     *
     * @param mixed $key
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function row($key = null)
    {
        if(func_num_args() == 0) {
            return $this->row;
        }
        return $this->row->getAttribute($key);
    }
}
