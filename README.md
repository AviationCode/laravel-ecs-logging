# Laravel Elastic Common Scheme (ECS) Logging 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aviationcode/laravel-ecs-logging.svg?style=flat-square)](https://packagist.org/packages/aviationcode/laravel-ecs-logging)
[![Build Status](https://img.shields.io/travis/aviationcode/laravel-ecs-logging/master.svg?style=flat-square)](https://travis-ci.org/aviationcode/laravel-ecs-logging)
[![Total Downloads](https://img.shields.io/packagist/dt/aviationcode/laravel-ecs-logging.svg?style=flat-square)](https://packagist.org/packages/aviationcode/laravel-ecs-logging)

This package adds ECS (Elastic Common Scheme) format to your laravel application allowing to log your standard logs to elastic.

## Installation

You can install the package via composer:

```bash
composer require aviationcode/laravel-ecs-logging
```

It's recommended to require `jenssegers/agent` which will add user agent logging support.

```bash
composer require jenssegers/agent
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --provider="AviationCode\EcsLogging\EcsLoggingServiceProvider" --tag="config"
```

Register log driver in `config/logging.php`

```php
return [
    'channels' => [
        // ... Other channels

        'ecs' => [
            'driver' => 'ecs',
            'path' => storage_path('logs/ecs/laravel.json'),
            'level' => 'debug',
            'days' => 14,
        ],
    ]
];
```

If you want to use this driver as the only logging method define `LOG_CHANNEL=ecs` in your `.env` or add the `ecs` channel into your stack driver.

All `Log::xxx()` calls get logged into json file. This file can be picked up by filebeat which sends it to your logstash or elasticsearch instance.

### Configure filebeat

Add the following to your `/etc/filebeat/filebeat.yml` file

```yaml
filebeat.inputs:
  - type: log
    enabled: true
    paths:
      - /path-to-your-laravel-app/storage/logs/ecs/*.json
    json:
      message_key: message
      keys_under_root: true
      overwrite_keys: true
```

## Usage


### Event

[Event](https://www.elastic.co/guide/en/ecs/current/ecs-event.html) defines something that happened, this could be a single point in time or lasting a certain period.
In order to log an event you can add this log context.

```php
Log::info('Password changed for John Doe<john.doe@example.com>', [
    'event' => [
        'action' => 'user-password-change',
        'code' => 4648,
        'outcome' => \AviationCode\EcsLogging\Types\Event::OUTCOME_SUCCESS,
        'type' => 'user',
    ],
]);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email ken.andries.1992@gmail.com instead of using the issue tracker.

## Credits

- [Ken Andries](https://github.com/DouglasDC3)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
