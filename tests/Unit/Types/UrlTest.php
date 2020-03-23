<?php

namespace Aviationcode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\Types\Url;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class UrlTest extends TestCase
{
    /** @test **/
    public function it_has_url_key()
    {
        $this->assertSame('url', (new Url(new Request()))->getKey());
    }

    /** @test **/
    public function it_builds_request_output()
    {
        $request = Request::createFromBase(SymfonyRequest::create('https://example.com/test-path?q=foo-bar&sort=desc'));

        $this->assertEquals([
            'original' => 'https://example.com/test-path?q=foo-bar&sort=desc',
            'domain' => 'example.com',
            'path' => 'test-path',
            'scheme' => 'https',
            'query' => 'q=foo-bar&sort=desc',
            'port' => 443,
        ], (new Url($request))->toArray());
    }

    /** @test **/
    public function it_adds_username_if_available()
    {
        $request = Request::createFromBase(
            SymfonyRequest::create('https://secret-user:secret-password@example.com/test-path?q=foo-bar')
        );

        $this->assertEquals([
            'original' => 'https://example.com/test-path?q=foo-bar',
            'domain' => 'example.com',
            'path' => 'test-path',
            'scheme' => 'https',
            'query' => 'q=foo-bar',
            'port' => 443,
            'username' => 'secret-user',
        ], (new Url($request))->toArray());
    }
}
