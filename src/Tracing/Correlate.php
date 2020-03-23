<?php

declare(strict_types=1);

namespace AviationCode\EcsLogging\Tracing;

use Closure;
use Ramsey\Uuid\Uuid;

class Correlate
{
    private const DEFAULT_HEADER = 'X-Correlation-Id';

    /** @var string */
    private static $id;

    /** @var Closure|null */
    private static $generator;

    /** @var string */
    private static $headerName;

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
     * @return string
     */
    public static function headerName(): string
    {
        return static::$headerName;
    }

    /**
     * @param string $headerName
     */
    public static function setHeaderName(string $headerName)
    {
        static::$headerName = $headerName;
    }

    /**
     * Reset the correlation instance (For testing only)
     */
    public static function reset(): void
    {
        static::$generator = null;
        static::$id = null;
        static::$headerName = self::DEFAULT_HEADER;
    }

    private static function generate(): string
    {
        if (static::$generator instanceof Closure) {
            return (string) call_user_func(static::$generator);
        }

        return (string) Uuid::uuid4();
    }
}
