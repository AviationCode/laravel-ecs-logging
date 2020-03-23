<?php

namespace Aviationcode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\Types\Client;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /** @test **/
    public function it_has_client_key()
    {
        $this->assertSame('client', (new Client(new Request()))->getKey());
    }

    /** @test **/
    public function it_builds_client_object()
    {
        $request = Request::createFromBase(
            \Symfony\Component\HttpFoundation\Request::create('/')
        );

        $this->assertEquals(['ip' => '127.0.0.1'], (new Client($request))->toArray());
    }
}
