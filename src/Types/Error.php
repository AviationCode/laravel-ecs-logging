<?php

namespace AviationCode\EcsLogging\Types;

use Illuminate\Support\Fluent;

/**
 * Class Error
 */
class Error extends Fluent implements EcsField
{
    /**
     * {@inheritDoc}
     */
    public function __construct(\Throwable $throwable)
    {
        parent::__construct([
            'error' => [
                'type' => get_class($throwable),
                'message' => $throwable->getMessage(),
                'code' => $throwable->getCode(),
                'stack_trace' => $throwable->getTraceAsString(),
            ],
            'log' => [
                'origin' => [
                    'file' => [
                        'name' => $throwable->getFile(),
                        'line' => $throwable->getLine(),
                    ],
                ],
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'error';
    }
}
