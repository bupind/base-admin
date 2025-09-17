<?php

namespace Base\Admin\Console;

use Base\Admin\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class AdminCommand extends Command
{
    public static $logo        = "";
    protected     $signature   = 'backend';
    protected     $description = 'List all admin commands';

    public function handle()
    {
        $this->line(static::$logo);
        $this->line(Admin::getLongVersion());
        $this->comment('');
        $this->comment('Available commands:');
        $this->listAdminCommands();
    }

    protected function listAdminCommands()
    {
        $commands = collect(Artisan::all())->mapWithKeys(function($command, $key) {
            if(Str::startsWith($key, 'admin:')) {
                return [$key => $command];
            }
            return [];
        })->toArray();
        $width    = $this->getColumnWidth($commands);
        /** @var Command $command */
        foreach($commands as $command) {
            $this->line(sprintf(" %-{$width}s %s", $command->getName(), $command->getDescription()));
        }
    }

    private function getColumnWidth(array $commands)
    {
        $widths = [];
        foreach($commands as $command) {
            $widths[] = static::strlen($command->getName());
            foreach($command->getAliases() as $alias) {
                $widths[] = static::strlen($alias);
            }
        }
        return $widths ? max($widths) + 2 : 0;
    }

    public static function strlen($string)
    {
        if(false === $encoding = mb_detect_encoding($string, null, true)) {
            return strlen($string);
        }
        return mb_strwidth($string, $encoding);
    }
}
