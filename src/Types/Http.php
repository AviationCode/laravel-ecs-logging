<?php

namespace AviationCode\EcsLogging\Types;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Fluent;

/**
 * Class Http
 *
 * @property string $ip
 */
class Http extends Fluent implements EcsField
{
    /**
     * {@inheritDoc}
     *
     * @param array|Response $attributes
     */
    public function __construct($attributes = [])
    {
        if (is_object($attributes)) {
            $attributes = ['response' => ['status_code' => $attributes->getStatusCode()]];
        }

        parent::__construct(array_merge_recursive([
            'request' => [
                'method' => Request::method(),
                'version' => Request::getProtocolVersion(),
            ],
        ], $attributes));
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'http';
    }
}
