<?php

namespace Base\Admin\Grid\Filter;

class EndsWith extends Like
{
    protected $exprFormat = '%{value}';
}
