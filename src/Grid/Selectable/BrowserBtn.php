<?php

namespace Base\Admin\Grid\Selectable;

use Illuminate\Contracts\Support\Renderable;

class BrowserBtn implements Renderable
{
    public function render()
    {
        $text = admin_trans('backend.choose');

        $html = <<<HTML
<a href="javascript:void(0)" class="btn btn-primary btn-sm pull-left select-relation">
    <i class="icon-folder-open"></i>
    &nbsp;&nbsp;{$text}
</a>
HTML;

        return $html;
    }
}
