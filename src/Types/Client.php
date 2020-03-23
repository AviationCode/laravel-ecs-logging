<?php

namespace AviationCode\EcsLogging\Types;

use Illuminate\Http\Request;
use Illuminate\Support\Fluent;

/**
 * Class Client
 *
 * @property string $ip
 */
class Client extends Fluent implements EcsField
{
    /**
     * {@inheritDoc}
     */
    public function __construct(Request $request)
    {
        parent::__construct([
            'ip' => $request->ip(),
        ]);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'client';
    }
}
