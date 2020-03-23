<?php

namespace Aviationcode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\Types\Http;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class HttpTest extends TestCase
{
    /** @test **/
    public function it_has_http_key()
    {
        $this->assertSame('http', (new Http())->getKey());
    }

    /** @test **/
    public function it_builds_http_object()
    {
        $this->assertEquals([
            'request' => [
                'method' => 'GET',
                'version' => 'HTTP/1.1',
            ]
        ], (new Http())->toArray());
    }

    /** @test **/
    public function it_can_add_status_code()
    {
        Route::get('/test', function () {
            return response('OK', 200);
        });

        $response = $this->get('/test');

        $this->assertEquals([
            'response' => [
                'status_code' => 200,
            ],
            'request' => [
                'method' => 'GET',
                'version' => 'HTTP/1.1',
            ]
        ], (new Http($response))->toArray());
    }
}
