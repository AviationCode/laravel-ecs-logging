<?php

namespace AviationCode\EcsLogging\Types;

use Illuminate\Http\Request;
use Illuminate\Support\Fluent;

/**
 * Class Url
 */
class Url extends Fluent implements EcsField
{
    /**
     * {@inheritDoc}
     */
    public function __construct(Request $request)
    {
        $attributes = [
            'domain' => $request->getHost(),
            'original' => $request->fullUrl(),
            'path' => $request->decodedPath(),
            'scheme' => $request->getScheme(),
            'port' => $request->getPort(),
            'query' => $request->getQueryString(),
        ];

        if ($request->getUser()) {
            $attributes['username'] = $request->getUser();
        }

        parent::__construct($attributes);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'url';
    }
}
