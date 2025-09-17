<?php

namespace Base\Admin\Widgets\Navbar;

use Illuminate\Contracts\Support\Renderable;
use Base\Admin\Admin;

class RefreshButton implements Renderable
{
    public function render()
    {
        return Admin::component('backend::components.refresh-btn');
    }
}
