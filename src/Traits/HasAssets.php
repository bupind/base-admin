<?php

namespace Base\Admin\Traits;
trait HasAssets
{
    /**
     * @var array
     */
    public static $script = [];
    /**
     * @var array
     */
    public static $deferredScript = [];
    /**
     * @var array
     */
    public static $style = [];
    /**
     * @var array
     */
    public static $css = [];
    /**
     * @var array
     */
    public static $js = [];
    /**
     * @var array
     */
    public static $html = [];
    /**
     * @var array
     */
    public static $headerJs = [];
    /**
     * @var string
     */
    public static $manifest = 'vendor/base/minify-manifest.json';
    /**
     * @var array
     */
    public static $manifestData = [];
    /**
     * @var array
     */
    public static $min = [
        'js'  => 'vendor/base/base.min.js',
        'css' => 'vendor/base/base.min.css',
    ];
    /**
     * @var array
     */
    public static $baseCss = [
        // first libraries
        'vendor/base/nprogress/nprogress.css',
        'vendor/base/sweetalert2/sweetalert2.min.css',
        'vendor/base/toastify-js/toastify.css',
        'vendor/base/flatpickr/flatpicker-custom.css',
        'vendor/base/choicesjs/styles/choices.min.css',
        'vendor/base/sortablejs/nestable.css',
        'vendor/base/local/css/styles.css',
    ];
    /**
     * @var array
     */
    public static $baseJs = [
        'vendor/base/bootstrap5/bootstrap.bundle.min.js',
        'vendor/base/nprogress/nprogress.js',
        'vendor/base/axios/axios.min.js',
        'vendor/base/sweetalert2/sweetalert2.min.js',
        'vendor/base/toastify-js/toastify.js',
        'vendor/base/flatpickr/flatpickr.min.js',
        'vendor/base/choicesjs/scripts/choices.min.js',
        'vendor/base/sortablejs/Sortable.min.js',
        'vendor/base/local/js/polyfills.js',
        'vendor/base/local/js/helpers.js',
        'vendor/base/local/js/backend.js',
        'vendor/base/local/js/actions.js',
        'vendor/base/local/js/grid.js',
        'vendor/base/local/js/grid-inline-edit.js',
        'vendor/base/local/js/form.js',
        'vendor/base/local/js/toastr.js',
        'vendor/base/local/js/resource.js',
        'vendor/base/local/js/tree.js',
        'vendor/base/local/js/selectable.js',
    ];
    /**
     * @var array
     */
    public static $minifyIgnoresCss = [];
    public static $minifyIgnoresJs  = [];

    /**
     * Add js or get all js.
     *
     * @param null $js
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function headerJs($js = null)
    {
        if(!is_null($js)) {
            return self::$headerJs = array_merge(self::$headerJs, (array)$js);
        }
        return view('backend::partials.js', ['js' => array_unique(static::$headerJs)]);
    }

    public static function component($component, $data = [])
    {
        $string = view($component, $data)->render();
        $dom    = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $string);
        libxml_use_internal_errors(false);
        if($head = $dom->getElementsByTagName('head')->item(0)) {
            foreach($head->childNodes as $child) {
                if($child instanceof \DOMElement) {
                    if($child->tagName == 'style' && !empty($child->nodeValue)) {
                        static::style($child->nodeValue);
                        continue;
                    }
                    if($child->tagName == 'link' && $child->hasAttribute('href')) {
                        static::css($child->getAttribute('href'));
                    }
                    if($child->tagName == 'script') {
                        if($child->hasAttribute('src')) {
                            static::js($child->getAttribute('src'));
                        } else {
                            static::script(';(function () {' . $child->nodeValue . '})();');
                        }
                        continue;
                    }
                }
            }
        }
        $render = '';
        if($body = $dom->getElementsByTagName('body')->item(0)) {
            foreach($body->childNodes as $child) {
                if($child instanceof \DOMElement) {
                    if($child->tagName == 'style' && !empty($child->nodeValue)) {
                        static::style($child->nodeValue);
                        continue;
                    }
                    if($child->tagName == 'script' && !empty($child->nodeValue)) {
                        static::script(';(function () {' . $child->nodeValue . '})();');
                        continue;
                    }
                    if($child->tagName == 'template') {
                        if($child->getAttribute('render') == 'true') {
                            // this will render the template tags right into the dom. Don't think we want this
                            $html = '';
                            foreach($child->childNodes as $childNode) {
                                $html .= $child->ownerDocument->saveHTML($childNode);
                            }
                        } else {
                            // this leaves the template tags in place, so they won't get rendered right away
                            $sub_doc = new \DOMDocument;
                            $sub_doc->appendChild($sub_doc->importNode($child, true));
                            $html = $sub_doc->saveHTML();
                        }
                        $html && static::html($html);
                        continue;
                    }
                }
                $render .= $body->ownerDocument->saveHTML($child);
            }
        }
        return trim($render);
    }

    /**
     * @param string $style
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function style($style = '')
    {
        if(!empty($style)) {
            return self::$style = array_merge(self::$style, (array)$style);
        }
        $style = collect(static::$style)
            ->unique()
            ->map(function($line) {
                return preg_replace('/\s+/', ' ', $line);
            });
        return view('backend::partials.style', compact('style'));
    }

    public static function css($css = null, $minify = true)
    {
        static::ignoreMinify('css', $css, $minify);
        if(!is_null($css)) {
            return self::$css = array_merge(self::$css, (array)$css);
        }
        if(!$css = static::getMinifiedCss()) {
            $css = array_merge(static::$css, static::baseCss());
        }
        $css = array_merge($css, static::$minifyIgnoresCss); // add minified ignored files
        $css = array_filter(array_unique($css));
        return view('backend::partials.css', compact('css'));
    }

    /**
     * @param string $assets
     * @param bool   $ignore
     */
    public static function ignoreMinify($type, $assets, $ignore = true)
    {
        if(!$ignore) {
            if($type == 'css') {
                static::$minifyIgnoresCss[] = $assets;
            } else {
                static::$minifyIgnoresJs[] = $assets;
            }
        }
    }

    /**
     * @return bool|mixed
     */
    protected static function getMinifiedCss()
    {
        if(!config('backend.minify_assets') || !file_exists(public_path(static::$manifest))) {
            return false;
        }
        return static::getManifestData('css');
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected static function getManifestData($key)
    {
        if(!empty(static::$manifestData)) {
            return static::$manifestData[$key];
        }
        static::$manifestData = json_decode(
            file_get_contents(public_path(static::$manifest)),
            true
        );
        return static::$manifestData[$key];
    }

    /**
     * @param null $css
     * @param bool $minify
     * @return array|null
     */
    public static function baseCss($css = null, $minify = true)
    {
        static::ignoreMinify('css', $css, $minify);
        if(!is_null($css)) {
            return static::$baseCss = $css;
        }
        $skin = config('backend.skin', 'skin-blue-light');
        // array_unshift(static::$baseCss, "vendor/base/AdminLTE/dist/css/skins/{$skin}.min.css");
        return static::$baseCss;
    }

    /**
     * Add js or get all js.
     *
     * @param null $js
     * @param bool $minify
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function js($js = null, $minify = true)
    {
        static::ignoreMinify('js', $js, $minify);
        if(!is_null($js)) {
            return self::$js = array_merge(self::$js, (array)$js);
        }
        if(!$js = static::getMinifiedJs()) {
            $js = array_merge(static::baseJs(), static::$js);
        }
        $js = array_merge($js, static::$minifyIgnoresJs); // add minified ignored files
        $js = array_filter(array_unique($js));
        return view('backend::partials.js', compact('js'));
    }

    /**
     * @return bool|mixed
     */
    protected static function getMinifiedJs()
    {
        if(!config('backend.minify_assets') || !file_exists(public_path(static::$manifest))) {
            return false;
        }
        return static::getManifestData('js');
    }

    /**
     * @param null $js
     * @param bool $minify
     * @return array|null
     */
    public static function baseJs($js = null, $minify = true)
    {
        static::ignoreMinify('js', $js, $minify);
        if(!is_null($js)) {
            return static::$baseJs = $js;
        }
        return static::$baseJs;
    }

    /**
     * @param string $script
     * @param bool   $deferred
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function script($script = '', $deferred = false)
    {
        if(!empty($script)) {
            if($deferred) {
                return self::$deferredScript = array_merge(self::$deferredScript, (array)$script);
            }
            return self::$script = array_merge(self::$script, (array)$script);
        }
        $script = collect(static::$script)
            ->merge(static::$deferredScript)
            ->unique()
            ->map(function($line) {
                return $line;
                // @see https://stackoverflow.com/questions/19509863/how-to-remove-js-comments-using-php
                $pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/';
                $line    = preg_replace($pattern, '', $line);
                return preg_replace('/\s+/', ' ', $line);
            });
        return view('backend::partials.script', compact('script'));
    }

    /**
     * @param string $html
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function html($html = '')
    {
        if(!empty($html)) {
            return self::$html = array_merge(self::$html, (array)$html);
        }
        return view('backend::partials.html', ['html' => array_unique(self::$html)]);
    }
}
