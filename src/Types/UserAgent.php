<?php

namespace AviationCode\EcsLogging\Types;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Jenssegers\Agent\Facades\Agent;

/**
 * Class UserAgent
 */
class UserAgent extends Fluent implements EcsField
{
    /**
     * @var array
     */
    protected $fillable = [
        'device',
        'original',
        'name',
        'version',
        'os',
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($attributes = [])
    {
        if (is_string($attributes)) {
            $attributes = ['original' => $attributes];

            if (class_exists(Agent::class)) {
                Agent::setUserAgent($attributes['original']);

                $attributes['device']['name'] = Agent::device();
                $attributes['name'] = Agent::browser();
                $attributes['version'] = Agent::version(Agent::browser());
                $attributes['os'] = (new Os(['name' => Agent::platform()]))->toArray();
            }
        }

        if (isset($attributes['device'])) {
            $attributes['device'] = Arr::only($attributes['device'], ['name']);
        }

        parent::__construct(array_filter(Arr::only($attributes, $this->fillable)));
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'user_agent';
    }
}
