<?php

namespace Aviationcode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\EcsLoggingServiceProvider;
use AviationCode\EcsLogging\Types\UserAgent;
use Jenssegers\Agent\AgentServiceProvider;
use Orchestra\Testbench\TestCase;

class UserAgentTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            EcsLoggingServiceProvider::class,
            AgentServiceProvider::class,
        ];
    }

    /** @test **/
    public function it_has_user_agent_key()
    {
        $this->assertSame('user_agent', (new UserAgent())->getKey());
    }

    /** @test **/
    public function it_builds_a_user_agent_object_from_header()
    {
        $userAgent = new UserAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 12_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1');

        $this->assertEquals([
            'device' => ['name' => 'iPhone'],
            'name' => 'Safari',
            'original' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1',
            'version' => '12.0',
            'os' => ['name' => 'iOS'],
        ], $userAgent->toArray());
    }

    /** @test **/
    public function it_builds_accepts_array_valid_attributes()
    {
        $userAgent = new UserAgent([
            'device' => ['name' => 'iPhone'],
            'name' => 'Safari',
            'original' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1',
            'version' => '12.0',
        ]);

        $this->assertEquals([
            'device' => ['name' => 'iPhone'],
            'name' => 'Safari',
            'original' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1',
            'version' => '12.0',
        ], $userAgent->toArray());
    }

    /** @test **/
    public function it_excludes_invalid_arguments()
    {
        $userAgent = new UserAgent([
            'invalid' => 'key',
            'device' =>  ['invalid' => 'key'],
        ]);

        $this->assertEquals([], $userAgent->toArray());
    }
}
