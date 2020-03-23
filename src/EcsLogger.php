<?php

namespace AviationCode\EcsLogging;

use AviationCode\EcsLogging\Monolog\EcsFormatter;
use AviationCode\EcsLogging\Types\Process;
use AviationCode\EcsLogging\Types\Service;
use AviationCode\EcsLogging\Types\Tracing;
use Illuminate\Log\ParsesLogConfiguration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Traits\ForwardsCalls;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface;

/**
 * Class EcsLogger
 *
 * @package AviationCode\EcsLogging
 *
 * @mixin Monolog
 */
class EcsLogger implements LoggerInterface
{
    use ParsesLogConfiguration;
    use ForwardsCalls;

    /**
     * @var Monolog
     */
    private $monolog;

    public function __construct(array $config)
    {
        $handler = new RotatingFileHandler(
            $config['path'],
            $config['days'] ?? 7,
            $this->level($config),
            $config['bubble'] ?? true,
            $config['permission'] ?? null,
            $config['locking'] ?? false
        );

        $handler
            ->setFormatter(new EcsFormatter())
            ->pushProcessor(function ($record) {
                return $this->enrichRecord($record);
            });

        $this->monolog = new Logger($this->parseChannel($config), [$handler]);
    }

    private function enrichRecord($record)
    {
        $record['context']['service'] = new Service();
        $record['context']['tracing'] = new Tracing();

        $record['context']['env'] = App::environment();

        if ($user = Auth::user()) {
            $record['context']['user'] = $user;
        }

        if (App::runningInConsole()) {
            $record['context']['process'] = new Process();

            return $record;
        }

        $record['context']['client'] = Request::instance();
        $record['context']['user_agent'] = Request::userAgent();
        $record['context']['url'] = Request::instance();

        return $record;
    }

    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = array())
    {
        $this->monolog->emergency($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = array())
    {
        $this->monolog->alert($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = array())
    {
        $this->monolog->critical($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = array())
    {
        $this->monolog->error($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = array())
    {
        $this->monolog->warning($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = array())
    {
        $this->monolog->notice($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = array())
    {
        $this->monolog->info($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = array())
    {
        $this->monolog->debug($message, $context);
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array())
    {
        $this->monolog->log($level, $message, $context);
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->monolog, $method, $parameters);
    }

    /**
     * @inheritDoc
     */
    protected function getFallbackChannelName()
    {
        return 'ecs';
    }
}
