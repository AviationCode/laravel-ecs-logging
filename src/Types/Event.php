<?php

namespace AviationCode\EcsLogging\Types;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Fluent;

/**
 * Class Event
 *
 * @property string $dataset
 */
class Event extends Fluent implements EcsField
{
    public const OUTCOME_SUCCESS = 'success';
    public const OUTCOME_FAILURE = 'failure';
    public const OUTCOME_UNKNOWN = 'unknown';

    /**
     * @var array
     */
    protected $fillable = [
        'action',
        'category',
        'code',
        'created',
        'dataset',
        'duration',
        'end',
        'hash',
        'id',
        'ingested',
        'kind',
        'module',
        'original',
        'outcome',
        'provider',
        'reference',
        'risk_score',
        'risk_score_norm',
        'sequence',
        'severity',
        'start',
        'type',
        'url',
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($attributes = [])
    {
        if (!isset($attributes['dataset'])) {
            $attributes['dataset'] = Config::get('ecs-logging.defaults.event.dataset', 'app.log');
        }

        parent::__construct(Arr::only($attributes, $this->fillable));
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'event';
    }
}
