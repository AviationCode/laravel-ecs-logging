<?php

namespace AviationCode\EcsLogging\Types;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

/**
 * Class Os
 *
 * @property string $name
 */
class Os extends Fluent implements EcsField
{
    /**
     * @var array
     */
    protected $fillable = [
        'family',
        'full',
        'kernel',
        'name',
        'platform',
        'version',
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($attributes = [])
    {
        parent::__construct(Arr::only($attributes, $this->fillable));
    }

    public function getKey(): string
    {
        return 'os';
    }
}
