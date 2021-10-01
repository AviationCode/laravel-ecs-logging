<?php

namespace AviationCode\EcsLogging\Types;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Fluent;

/**
 * Class User
 */
class User extends Fluent implements EcsField
{
    /**
     * {@inheritDoc}
     */
    public function __construct($user)
    {
        $config = Config::get('ecs-logging.formatter.user', []);

        $attributes = ['id' => $user->getKey()];

        foreach ($config as $key => $attribute) {
            if ($attribute) {
                $attributes[$key] = $user->{$attribute};
            }
        }

        if ($config['hash'] === true) {
            ksort($attributes);
            $attributes = ['hash' => \hash('sha256', json_encode(array_filter($attributes)))];
        }

        if (is_string($config['hash'])) {
            $attributes = ['hash' => $user->{$config['hash']}()];
        }

        parent::__construct($attributes);
    }

    public function getKey(): string
    {
        return 'user';
    }
}
