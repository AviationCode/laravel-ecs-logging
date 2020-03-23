<?php

namespace Aviationcode\EcsLogging\Tests\Unit\Monolog;

use AviationCode\EcsLogging\EcsLoggingServiceProvider;
use AviationCode\EcsLogging\Monolog\EcsFormatter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;

class EcsFormatterTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [EcsLoggingServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('ecs-logging.defaults.event.dataset', 'app.log');
    }

    /** @test **/
    public function it_contains_timestamps()
    {
        Carbon::setTestNow(Carbon::create(2020, 03, 22, 17, 15, 10));
        $formatter = new EcsFormatter();

        $result = json_decode($formatter->format([
            'datetime' => Carbon::now(),
            'level_name' => 'info',
            'channel' => 'ecs',
        ]), true);

        $this->assertTrue(json_last_error() === JSON_ERROR_NONE);
        $this->assertArrayHasKey('@timestamp', $result);
        $this->assertEquals('2020-03-22T17:15:10.000000Z', $result['@timestamp']);

        $this->assertArrayHasKey('log', $result);
        tap($result['log'], function ($log) {
            $this->assertEquals('info', $log['level']);
            $this->assertEquals('ecs', $log['logger']);
        });
    }

    /** @test **/
    public function it_contains_message()
    {
        $formatter = new EcsFormatter();

        $result = json_decode($formatter->format([
            'datetime' => Carbon::now(),
            'level_name' => 'info',
            'channel' => 'ecs',
            'message' => 'Test log',
        ]), true);

        $this->assertEquals('Test log', $result['message']);
    }

    /** @test **/
    public function it_converts_exceptions_to_a_error()
    {
        $formatter = new EcsFormatter();

        $result = json_decode($formatter->format([
            'datetime' => Carbon::now(),
            'level_name' => 'info',
            'channel' => 'ecs',
            'context' => [
                'exception' => $exception = new \Exception('This is an exception.'),
            ],
        ]), true);

        $this->assertEquals($exception->getFile(), $result['log']['origin']['file']['name']);
        $this->assertEquals($exception->getLine(), $result['log']['origin']['file']['line']);

        $this->assertEquals(get_class($exception), $result['error']['type']);
        $this->assertEquals($exception->getMessage(), $result['error']['message']);
        $this->assertSame($exception->getCode(), $result['error']['code']);
        $this->assertSame($exception->getTraceAsString(), $result['error']['stack_trace']);
    }

    /** @test **/
    public function it_adds_event()
    {
        $formatter = new EcsFormatter();

        $result = json_decode($formatter->format([
            'datetime' => Carbon::now(),
            'level_name' => 'info',
            'channel' => 'ecs',
            'message' => 'Test log',
        ]), true);

        $this->assertEquals('app.log', $result['event']['dataset']);
    }

    /** @test **/
    public function it_removes_disabled_features()
    {
        Config::set('ecs-logging.features.error', false);
        $formatter = new EcsFormatter();

        $result = json_decode($formatter->format([
            'datetime' => Carbon::now(),
            'level_name' => 'info',
            'channel' => 'ecs',
            'message' => 'Test log',
            'context' => ['exception' => new \Exception('Ignored')]
        ]), true);

        $this->assertArrayNotHasKey('error', $result);
    }

    /** @test **/
    public function it_adds_extra_context_as_labels()
    {
        $formatter = new EcsFormatter();

        $result = json_decode($formatter->format([
            'datetime' => Carbon::now(),
            'level_name' => 'info',
            'channel' => 'ecs',
            'message' => 'Test log',
            'context' => [
                'tag' => 'this is extra',
                'ExtraKey' => true,
                'My\Namespace\Extra' => true,
                'dot.extra.key' => true,
                'space extra key' => true,
            ]
        ]), true);

        $this->assertEquals('this is extra', $result['labels']['tag']);
        $this->assertArrayHasKey('extrakey', $result['labels']);
        $this->assertArrayHasKey('my_namespace_extra', $result['labels']);
        $this->assertArrayHasKey('dot_extra_key', $result['labels']);
        $this->assertArrayHasKey('space_extra_key', $result['labels']);
    }
}
