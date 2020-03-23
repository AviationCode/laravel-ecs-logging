<?php

namespace AviationCode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\Types\Error;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    /** @test **/
    public function it_has_error_key()
    {
        $this->assertSame('error', (new Error(new \Exception()))->getKey());
    }

    /** @test **/
    public function it_formats_exception_as_error()
    {
        $throwable = new \Exception('Test Message');
        $error = new Error($throwable);

        $this->assertEquals([
            'error' => [
                'type' => get_class($throwable),
                'message' => 'Test Message',
                'code' => 0,
                'stack_trace' => $throwable->getTraceAsString(),
            ],
            'log' => [
                'origin' => [
                    'file' => [
                        'name' => $throwable->getFile(),
                        'line' => $throwable->getLine(),
                    ],
                ],
            ],
        ], $error->toArray());
    }
}
