<?php

namespace AviationCode\EcsLogging\Types;

use Carbon\Carbon;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Fluent;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Class Process
 */
class Process extends Fluent implements EcsField
{
    /**
     * @var Carbon
     */
    private static $startedAt;

    private static $pwd;
    private static $argsCount;
    private static $args = [];

    private static $code;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        if (is_null(static::$startedAt)) {
            static::init();
        }

        parent::__construct(array_filter([
            'args' => static::$args,
            'args_count' => static::$argsCount,
            'command_line' => implode(' ', static::$args),
            'executable' => static::$args[0],
            'exit_code' => static::$code,
            'start' => optional(static::$startedAt)->format('Y-m-d\TH:i:s.u\Z'),
            'uptime' => Carbon::now()->diffInSeconds(static::$startedAt),
            'working_directory' => getcwd(),
        ], function ($value) {
            return !is_null($value);
        }));
    }

    public static function onCommandStarting()
    {
        return function (CommandStarting $event) {
            self::init();

            if (Config::get('ecs-logging.formatter.commands.log_start', false)) {
                Log::info("Command '{$event->command}' starting");
            }
        };
    }

    public static function onCommandFinished()
    {
        return function (CommandFinished $event) {
            static::$code = $event->exitCode;

            if (Config::get('ecs-logging.formatter.commands.log_start', false)) {
                Log::info(vsprintf("Command '%s' finished (took %ds)", [
                    $event->command,
                    Carbon::now()->diffInSeconds(static::$startedAt)
                ]));
            }
        };
    }

    protected static function init(): void
    {
        $php = new PhpExecutableFinder();

        static::$args = collect($php->find(true))->concat($_SERVER['argv'])->toArray();
        static::$argsCount = $_SERVER['argc'];
        static::$pwd = getcwd();

        static::$startedAt = Carbon::now();
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'process';
    }
}
