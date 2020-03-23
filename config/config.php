<?php

return [
    /*
     * You can enable or disable certain feature.
     */
    'features' => [
        /*
         * Logs the IP of the request
         */
        'client' => true,
        /*
         * Logs exceptions and exception source.
         */
        'error' => true,
        /*
         * Log events mainly event action, id and outcome.
         */
        'event' => true,
        /*
         * Log service information like application instance, version, name.
         */
        'service' => true,
        /*
         * Log tracing and correlation information.
         */
        'tracing' => true,
        /*
         * Log requested url.
         */
        'url' => true,
        /*
         * Log user information if a authenticated user exists.
         */
        'user' => true,
        /*
         * Log user agent information on a request. Requires `jenssegers/agent`.
         */
        'user_agent' => true,
        /*
         * Log command and console information.
         */
        'process' => true,
    ],

    'formatter' => [
        'types' => [
            'client' => \AviationCode\EcsLogging\Types\Client::class,
            'error' => \AviationCode\EcsLogging\Types\Error::class,
            'event' => \AviationCode\EcsLogging\Types\Event::class,
            'process' => \AviationCode\EcsLogging\Types\Process::class,
            'service' => \AviationCode\EcsLogging\Types\Service::class,
            'tracing' => \AviationCode\EcsLogging\Types\Tracing::class,
            'url' => \AviationCode\EcsLogging\Types\Url::class,
            'user' => \AviationCode\EcsLogging\Types\User::class,
            'user_agent' => \AviationCode\EcsLogging\Types\UserAgent::class,
        ],

        'commands' => [
            /*
             * When enabled output will be send to the ecs-log instead of standard out
             */
            'redirect_output' => true,

            /*
             * When enabled it makes a log statement when and which command has been started.
             */
            'log_start' => true,

            /*
             * When enabled it makes a log statement when a command is finished.
             */
            'log_end' => true,
        ],

        'user' => [
            /*
             * If Hash is set to true it will not log the users information (email, full_name, name, id ...)
             * Additional hash can be set to string we will call a method with the given name on the user object
             * It should return a unique hash
             */
            'hash' => false,
            'domain' => false,
            'email' => 'email',
            'full_name' => 'name',
            'name' => 'email',
        ]
    ],

    'defaults' => [
        'event' => [
            'dataset' => \Illuminate\Support\Str::slug(env('APP_NAME')) . '.log',
        ],
        'service' => [
            'id' => null,
            'name' => \Illuminate\Support\Str::slug(env('APP_NAME')),
            'type' => null,
            'version' => \Illuminate\Support\Facades\App::version(),
        ],
    ]
];
