<?php

namespace Base\Admin\Controllers;

use App\Http\Controllers\Controller;
use Base\Admin\Facades\Admin;
use Base\Admin\Layout\Content;
use Base\Admin\Widgets\LogViewer;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request, $file = null)
    {
        if($file === null) {
            $file = (new LogViewer())->getLastModifiedLog();
        }
        return Admin::content(function(Content $content) use ($file, $request) {
            $offset = $request->get('offset');
            $viewer = new LogViewer($file);
            $content->body(view('backend::logs', [
                'logs'                          => $viewer->fetch($offset),
                'logFiles'                      => $viewer->getLogFiles(),
                'fileName'                      => $viewer->file,
                'end'                           => $viewer->getFilesize(),
                'tailPath'                      => route('log-viewer-tail', ['log' => $viewer->file]),
                'prevUrl'                       => $viewer->getPrevPageUrl(),
                'nextUrl'                       => $viewer->getNextPageUrl(),
                'filePath'                      => $viewer->getFilePath(),
                'size'                          => static::bytesToHuman($viewer->getFilesize()),
                'bypass_protected_urls_find'    => $viewer->bypass_protected_urls_find,
                'bypass_protected_urls_replace' => $viewer->bypass_protected_urls_replace,
            ]));
            $content->header($viewer->getFilePath());
        });
    }

    protected static function bytesToHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function tail(Request $request, $file)
    {
        $offset = $request->get('offset');
        $viewer = new LogViewer($file);
        [$pos, $logs] = $viewer->tail($offset);
        return compact('pos', 'logs');
    }
}
