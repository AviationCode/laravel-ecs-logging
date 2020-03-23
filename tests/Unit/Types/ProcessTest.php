<?php

namespace AviationCode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\EcsLoggingServiceProvider;
use AviationCode\EcsLogging\Types\Process;
use Carbon\Carbon;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Process\PhpExecutableFinder;

class ProcessTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [EcsLoggingServiceProvider::class];
    }

    /** @test **/
    public function it_has_process_key()
    {
        $commandStart = Process::onCommandStarting();
        $commandStart(\Mockery::spy(CommandStarting::class));

        $this->assertSame('process', (new Process())->getKey());
    }

    /** @test **/
    public function it_creates_start_process_log()
    {
        Carbon::setTestNow($now = Carbon::today());
        $commandStart = Process::onCommandStarting();
        $commandStart(\Mockery::spy(CommandStarting::class));
        $php = new PhpExecutableFinder();

        $process = new Process();

        $this->assertEquals([
            'args' => collect($php->find(true))->concat($_SERVER['argv'])->toArray(),
            'args_count' => $_SERVER['argc'],
            'command_line' => collect($php->find(true))->concat($_SERVER['argv'])->implode(' '),
            'executable' => $php->find(true),
            'start' => $now->format('Y-m-d\TH:i:s.u\Z'),
            'uptime' => 0,
            'working_directory' => getcwd(),
        ], $process->toArray());
    }

    /** @test **/
    public function it_creates_stop_process_log()
    {
        Carbon::setTestNow($now = Carbon::today());
        $commandStart = Process::onCommandStarting();
        $commandStart(\Mockery::spy(CommandStarting::class));
        $commandFinished = Process::onCommandFinished();
        $commandFinished(\Mockery::spy(CommandFinished::class));
        $php = new PhpExecutableFinder();

        $process = new Process();

        $this->assertEquals([
            'args' => collect($php->find(true))->concat($_SERVER['argv'])->toArray(),
            'args_count' => $_SERVER['argc'],
            'command_line' => collect($php->find(true))->concat($_SERVER['argv'])->implode(' '),
            'executable' => $php->find(true),
            'start' => $now->format('Y-m-d\TH:i:s.u\Z'),
            'uptime' => 0,
            'working_directory' => getcwd(),
        ], $process->toArray());
    }
}
