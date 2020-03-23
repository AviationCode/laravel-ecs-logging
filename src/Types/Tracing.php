<?php

namespace AviationCode\EcsLogging\Types;

use AviationCode\EcsLogging\Tracing\Correlate;
use AviationCode\EcsLogging\Tracing\Message;
use Illuminate\Support\Fluent;

/**
 * Class Tracing
 */
class Tracing extends Fluent implements EcsField
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct([
            'trace' => ['id' => Correlate::id()],
            'transaction' => ['id' => Message::id()],
        ]);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'tracing';
    }
}
