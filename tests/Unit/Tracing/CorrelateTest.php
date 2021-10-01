<?php

namespace AviationCode\EcsLogging\Tests\Unit\Tracing;

use AviationCode\EcsLogging\Tracing\Correlate;
use PHPUnit\Framework\TestCase;

class CorrelateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Correlate::reset();
    }

    /** @test **/
    public function it_reuses_same_id()
    {
        $id = Correlate::id();

        $this->assertNotNull($id);
        $this->assertSame($id, Correlate::id());
    }

    /** @test **/
    public function it_can_swap_generator_functions()
    {
        Correlate::setGenerator(fn(): string => 'foo-bar');

        $this->assertSame('foo-bar', Correlate::id());
    }
}
