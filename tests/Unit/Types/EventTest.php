<?php

namespace Aviationcode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\EcsLoggingServiceProvider;
use AviationCode\EcsLogging\Monolog\EcsFormatter;
use AviationCode\EcsLogging\Types\Event;
use Carbon\Carbon;
use Orchestra\Testbench\TestCase;

class EventTest extends TestCase
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
    public function it_has_event_key()
    {
        $this->assertSame('event', (new Event())->getKey());
    }

    /** @test **/
    public function it_builds_default_dataset()
    {
        $this->assertEquals(['dataset' => 'app.log'], (new Event())->toArray());
    }

    /** @test **/
    public function it_sets_valid_attributes()
    {
        $event = new Event([
            'action' => 'user-password-change',
            'category' => 'authentication',
            'code' => 4648,
            'outcome' => \AviationCode\EcsLogging\Types\Event::OUTCOME_SUCCESS,
            'type' => 'user',
            'created' => Carbon::create(2020, 03, 22, 17, 20, 10),
            'dataset' => 'app.auth',
            'duration' => 150000,
            'start' => Carbon::create(2020, 03, 22, 17, 15, 10),
            'end' => Carbon::create(2020, 03, 22, 17, 18, 20),
            'id' => 'abc123',
            'kind' => 'event',
            'module' => 'app',
        ]);

        $this->assertEquals([
            'action' => 'user-password-change',
            'category' => 'authentication',
            'code' => 4648,
            'outcome' => 'success',
            'type' => 'user',
            'created' => '2020-03-22T17:20:10.000000Z',
            'dataset' => 'app.auth',
            'duration' => 150000,
            'start' => '2020-03-22T17:15:10.000000Z',
            'end' => '2020-03-22T17:18:20.000000Z',
            'id' => 'abc123',
            'kind' => 'event',
            'module' => 'app',
        ], (new EcsFormatter())->normalize($event->toArray()));
    }

    /** @test **/
    public function it_ignores_invalid_options()
    {
        $event = new Event(['invalid-key' => 'foobar']);

        $this->assertEquals([
            'dataset' => 'app.log',
        ], $event->toArray());
    }
}
