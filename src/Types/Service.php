<?php

namespace AviationCode\EcsLogging\Types;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Fluent;
use Ramsey\Uuid\Uuid;

/**
 * Class Service
 */
class Service extends Fluent implements EcsField
{
    /**
     * {@inheritDoc}
     */
    public function __construct($attributes = [])
    {
        if (!isset($attributes['ephemeral_id'])) {
            $attributes['ephemeral_id'] = Uuid::uuid4()->toString();
        }

        if (!isset($attributes['id'])) {
            $attributes['id'] = Config::get('ecs-logging.defaults.service.id');
        }

        if (!isset($attributes['name'])) {
            $attributes['name'] = Config::get('ecs-logging.defaults.service.name');
        }

        if (!isset($attributes['type'])) {
            $attributes['type'] = Config::get('ecs-logging.defaults.service.type');
        }

        if (!isset($attributes['version'])) {
            $attributes['version'] = Config::get('ecs-logging.defaults.service.version');
        }

        parent::__construct(array_filter($attributes));
    }

    public function getKey(): string
    {
        return 'service';
    }
}
