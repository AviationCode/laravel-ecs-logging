<?php

namespace AviationCode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\Tracing\Correlate;
use AviationCode\EcsLogging\Tracing\Message;
use AviationCode\EcsLogging\Types\Tracing;
use PHPUnit\Framework\TestCase;

class TracingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Message::reset();
        Correlate::reset();
    }

    /** @test **/
    public function it_has_tracing_key()
    {
        $this->assertSame('tracing', (new Tracing())->getKey());
    }

    /** @test **/
    public function it_builds_tracing_object()
    {
        Message::setGenerator(function () {
            return 'message-id';
        });

        Correlate::setGenerator(function () {
            return 'correlation-id';
        });

        $this->assertEquals([
            'trace' => ['id' => 'correlation-id'],
            'transaction' => ['id' => 'message-id'],
        ], (new Tracing())->toArray());
    }
}
