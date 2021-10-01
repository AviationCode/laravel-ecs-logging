<?php

declare(strict_types=1);

namespace AviationCode\EcsLogging\Tracing;

use Closure;
use Ramsey\Uuid\Uuid;

class Correlate
{
    private const DEFAULT_HEADER = 'X-Correlation-Id';

    private static ?string $id;

    private static ?Closure $generator;

    private static ?string $headerName;

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
    public static function setGenerator(Closure $generator): void
    {
        static::$generator = $generator;
    }

    public static function headerName(): string
    {
        return static::$headerName;
    }

    public static function setHeaderName(string $headerName): void
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
