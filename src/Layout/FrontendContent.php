<?php

namespace Base\Admin\Layout;

use Closure;
use Illuminate\Contracts\Support\Renderable;

class FrontendContent implements Renderable
{
    protected string $title       = '';
    protected string $description = '';
    protected array  $css_files   = [];
    protected string $css         = '';
    protected ?array $view        = null;
    protected string $layout      = 'frontend::index';

    public function __construct(?Closure $callback = null)
    {
        if($callback instanceof Closure) {
            $callback($this);
        }
    }

    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function cssFile(string $css_file): self
    {
        $this->css_files[] = $css_file;
        return $this;
    }

    public function css(string $css): self
    {
        $this->css .= $css;
        return $this;
    }

    public function layout(string $view): self
    {
        $this->layout = $view;
        return $this;
    }

    public function render(?string $view = null, array $data = []): string
    {
        if($this->view === null && $view) {
            $this->view($view, $data);
        }
        if($this->view) {
            $viewName = $this->view['view'];
            $exists   = view()->exists($viewName);
        }
        return view($this->layout, [
            'title'       => $this->title,
            'description' => $this->description,
            'css'         => $this->css,
            'css_files'   => $this->css_files,
            '_view_'      => $this->view,
        ])->render();
    }

    public function view(string $view, array $data = []): self
    {
        $normalizedView = str_replace(['/', '\\'], '.', $view);
        if(!view()->exists($normalizedView)) {
            throw new \InvalidArgumentException("View [$normalizedView] not found");
        }
        $this->view = [
            'view' => $normalizedView,
            'data' => $data,
        ];
        return $this;
    }
}
