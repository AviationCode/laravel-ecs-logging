<?php

declare(strict_types=1);

namespace AviationCode\EcsLogging\Tracing;

use Closure;
use Ramsey\Uuid\Uuid;

/**
 * Class Message
 *
 * @package AviationCode\EcsLogging\Tracing
 */
class Message
{
    /** @var string */
    private static $id;

    /** @var Closure|null */
    private static $generator;

    /**
     * Returns the unique correlation id.
     *
     * @return string
     */
    public static function id(): string
    {
        if (! static::$id) {
            static::$id = static::generate();
        }

        return static::$id;
    }

    /**
     * Set the unique correlation id generator function.
     *
     * @param Closure $generator
     */
    public static function setGenerator(Closure $generator)
    {
        static::$generator = $generator;
    }

    /**
     * Reset the correlation instance (For testing only)
     */
    public static function reset(): void
    {
        static::$generator = null;
        static::$id = null;
    }

    private static function generate(): string
    {
        if (static::$generator instanceof Closure) {
            return (string) call_user_func(static::$generator);
        }

        return (string) Uuid::uuid4();
    }
}
