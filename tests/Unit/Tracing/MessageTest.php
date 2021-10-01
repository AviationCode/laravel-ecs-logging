<?php

namespace AviationCode\EcsLogging\Tests\Unit\Tracing;

use AviationCode\EcsLogging\Tracing\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Message::reset();
    }

    /** @test **/
    public function it_reuses_same_id()
    {
        $id = Message::id();

        $this->assertNotNull($id);
        $this->assertSame($id, Message::id());
    }

    /** @test **/
    public function it_can_swap_generator_functions()
    {
        Message::setGenerator(fn () => 'foo-bar');

        $this->assertSame('foo-bar', Message::id());
    }
}
