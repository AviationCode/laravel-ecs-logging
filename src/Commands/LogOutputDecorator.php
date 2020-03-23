<?php

namespace AviationCode\EcsLogging\Commands;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Log;

class LogOutputDecorator extends OutputStyle
{
    private const TAG_REGEX = '/<[^<>]*>([^<>]*)<\/[^<>]*>/';
    private const IGNORE = '/[\*]{3,}|^>$/';

    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        if (!is_iterable($messages)) {
            $messages = [$messages];
        }

        foreach ($messages as $message) {
            $message = trim($message);

            if (preg_match(self::IGNORE, $message)) {
                continue;
            }

            Log::info(preg_replace(static::TAG_REGEX, '$1', $message));
        }

        parent::writeln($messages, $type);
    }

    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        if (!is_iterable($messages)) {
            $messages = [$messages];
        }

        foreach ($messages as $message) {
            $message = trim($message);

            if (preg_match(self::IGNORE, $message)) {
                continue;
            }

            Log::info(preg_replace(static::TAG_REGEX, '$1', $message));
        }

        parent::write($messages, $newline, $type);
    }
}
