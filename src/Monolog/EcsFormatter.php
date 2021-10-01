<?php

namespace AviationCode\EcsLogging\Monolog;

use AviationCode\EcsLogging\Types\EcsField;
use AviationCode\EcsLogging\Types\Event;
use Illuminate\Support\Facades\Config;
use Monolog\Formatter\NormalizerFormatter;

class EcsFormatter extends NormalizerFormatter
{
    /**
     * @var bool[]
     */
    protected array $features = [];

    /**
     * @var string[]
     */
    protected array $types = [];

    /**
     * EcsFormatter constructor.
     */
    public function __construct()
    {
        parent::__construct('Y-m-d\TH:i:s.u\Z');

        $this->features = Config::get('ecs-logging.features', []);
        $this->types = Config::get('ecs-logging.formatter.types', []);
    }

    /**
     * {@inheritdoc}
     *
     * @link https://www.elastic.co/guide/en/ecs/1.5/ecs-log.html
     * @link https://www.elastic.co/guide/en/ecs/1.5/ecs-base.html
     * @link https://www.elastic.co/guide/en/ecs/current/ecs-tracing.html
     */
    public function format(array $record): string
    {
        $message = [
            '@timestamp' => $this->normalize($record['datetime']),
            'log' => [
                'level'  => $record['level_name'],
                'logger' => $record['channel'],
            ],
        ];

        if (isset($record['message'])) {
            $message['message'] = $record['message'];
        }

        if (isset($record['context']['exception'])) {
            $record['context']['error'] = $record['context']['exception'];
            unset($record['context']['exception']);
        }

        if (!isset($record['context']['event'])) {
            $record['context']['event'] = new Event();
        }

        foreach ($record['context'] as $field => $value) {
            if (isset($this->features[$field]) && $this->features[$field] === false) {
                continue;
            }

            if (isset($this->types[$field]) && !$value instanceof EcsField) {
                $value = new $this->types[$field]($value);
            }

            if ($value instanceof EcsField) {
                $message[$value->getKey()] = $value->toArray();
                unset($record['context'][$field]);

                continue;
            }

            $value = $this->normalize($value);
            $message['labels'][strtolower(str_replace(['.', ' ', '*', '\\'], '_', trim($field)))] = $value;
        }

        // Handle exceptional cases
        if (isset($message['error'])) {
            $message['log'] = array_merge($message['log'], $message['error']['log']);
            $message['error'] = $message['error']['error'];
        }

        return $this->toJson($message) . "\n";
    }

    /**
     * Expose for testing.
     *
     * @param mixed $data
     * @param int $depth
     * @return array|bool|int|string|null
     */
    public function normalize($data, int $depth = 0)
    {
        return parent::normalize($data, $depth);
    }
}
