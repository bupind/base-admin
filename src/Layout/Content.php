<?php

namespace Base\Admin\Layout;

use Base\Admin\Facades\Admin;
use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

class Content implements Renderable
{
    protected $body_classes = [];
    protected $title        = ' ';
    protected $description  = ' ';
    protected $breadcrumb   = [];
    protected $css_files    = [];
    protected $css          = '';
    protected $rows         = [];
    protected $view;

    public function __construct(?\Closure $callback = null)
    {
        if($callback instanceof Closure) {
            $callback($this);
        }
    }

    public function header($header = '')
    {
        return $this->title($header);
    }

    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    public function description($description = '')
    {
        $this->description = $description;
        return $this;
    }

    public function breadcrumb(...$breadcrumb)
    {
        $this->validateBreadcrumb($breadcrumb);
        $this->breadcrumb = (array)$breadcrumb;
        return $this;
    }

    protected function validateBreadcrumb(array $breadcrumb)
    {
        foreach($breadcrumb as $item) {
            if(!is_array($item) || !Arr::has($item, 'text')) {
                throw new \Exception('Breadcrumb format error!');
            }
        }
        return true;
    }

    public function addBodyClass($class)
    {
        if(is_array($class)) {
            $this->body_classes = array_merge($this->body_classes, $class);
        } else {
            $this->body_classes[] = $class;
        }
        return $this;
    }

    public function css_file(string $css_file)
    {
        $this->css_files[] = $css_file;
        return $this;
    }

    public function css(string $css)
    {
        $this->css .= $css;
        return $this;
    }

    public function view($view, $data = [])
    {
        $this->view = compact('view', 'data');
        return $this;
    }

    /**
     * @param string $view
     * @param array $data
     */
    public function component($view, $data = [])
    {
        return $this->body(Admin::component($view, $data));
    }

    /**
     * Alias of method row.
     *
     * @param mixed $content
     * @return $this
     */
    public function body($content)
    {
        return $this->row($content);
    }

    /**
     * Add one row for content body.
     *
     *
     * @return $this
     */
    public function row($content)
    {
        if($content instanceof Closure) {
            $row = new Row;
            call_user_func($content, $row);
            $this->addRow($row);
        } else {
            $this->addRow(new Row($content));
        }
        return $this;
    }

    /**
     * Add Row.
     */
    protected function addRow(Row $row)
    {
        $this->rows[] = $row;
    }

    /**
     * @return $this
     */
    public function dump($var)
    {
        return $this->row(admin_dump(...func_get_args()));
    }

    /**
     * Set success message for content.
     *
     * @param string $title
     * @param string $message
     * @return $this
     */
    public function withSuccess($title = '', $message = '')
    {
        admin_success($title, $message);
        return $this;
    }

    /**
     * Set error message for content.
     *
     * @param string $title
     * @param string $message
     * @return $this
     */
    public function withError($title = '', $message = '')
    {
        admin_error($title, $message);
        return $this;
    }

    /**
     * Set warning message for content.
     *
     * @param string $title
     * @param string $message
     * @return $this
     */
    public function withWarning($title = '', $message = '')
    {
        admin_warning($title, $message);
        return $this;
    }

    /**
     * Set info message for content.
     *
     * @param string $title
     * @param string $message
     * @return $this
     */
    public function withInfo($title = '', $message = '')
    {
        admin_info($title, $message);
        return $this;
    }

    /**
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        $items = [
            'body_classes' => implode(' ', $this->body_classes),
            'header'       => $this->title,
            'description'  => $this->description,
            'breadcrumb'   => $this->breadcrumb,
            'css'          => $this->css,
            'css_files'    => $this->css_files,
            '_content_'    => $this->build(),
            '_view_'       => $this->view,
            '_user_'       => $this->getUserData(),
        ];
        return view('backend::content', $items)->render();
    }

    public function build()
    {
        ob_start();
        foreach($this->rows as $row) {
            $row->build();
        }
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    /**
     * @return array
     */
    protected function getUserData()
    {
        if(!$user = Admin::user()) {
            return [];
        }
        return Arr::only($user->toArray(), ['id', 'username', 'email', 'name', 'avatar']);
    }
}
