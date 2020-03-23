<?php

namespace AviationCode\EcsLogging\Tests\Unit\Http\Middleware;

use AviationCode\EcsLogging\Http\Middleware\CorrelationHeader;
use AviationCode\EcsLogging\Tracing\Correlate;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class CorrelationHeaderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Correlate::reset();
    }

    /** @test **/
    public function it_generates_and_returns_correlation_id()
    {
        Route::middleware(CorrelationHeader::class)->any('/_test/request', function() {
            return 'OK';
        });

        Correlate::setGenerator(function () {
            return 'FooBar';
        });

        $response = $this->get('/_test/request');

        $response->assertHeader('X-Correlation-Id', 'FooBar');
    }

    /** @test **/
    public function it_keeps_correlation_id_through_request_cycle()
    {
        Route::middleware(CorrelationHeader::class)->any('/_test/request', function() {
            return 'OK';
        });

        $response = $this->withHeader('X-Correlation-Id', 'FooBar')->get('/_test/request');

        $response->assertHeader('X-Correlation-Id', 'FooBar');
    }
}
