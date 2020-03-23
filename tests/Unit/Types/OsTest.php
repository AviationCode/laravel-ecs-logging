<?php

namespace AviationCode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\Types\Os;
use PHPUnit\Framework\TestCase;

class OsTest extends TestCase
{
    /** @test **/
    public function it_has_os_key()
    {
        $this->assertSame('os', (new Os())->getKey());
    }

    /** @test **/
    public function it_creates_os_object()
    {
        $os = new Os([
            'family' => 'debian',
            'full' => 'Mac OS Mojave',
            'kernel' => '4.4.0-112-generic',
            'name' => 'Mac OS X',
            'platform' => 'darwin',
            'version' => '10.14.1',
        ]);

        $this->assertEquals([
            'family' => 'debian',
            'full' => 'Mac OS Mojave',
            'kernel' => '4.4.0-112-generic',
            'name' => 'Mac OS X',
            'platform' => 'darwin',
            'version' => '10.14.1',
        ], $os->toArray());
    }

    /** @test **/
    public function it_skips_invalid_options()
    {
        $this->assertEquals([], (new Os(['another' => 'value']))->toArray());
    }
}
