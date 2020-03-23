<?php

namespace AviationCode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\EcsLoggingServiceProvider;
use AviationCode\EcsLogging\Types\Service;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;
use Ramsey\Uuid\Uuid;

class ServiceTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [EcsLoggingServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ecs-logging.defaults.service.name', 'Test App');
    }

    /** @test **/
    public function it_has_service_key()
    {
        $this->assertSame('service', (new Service())->getKey());
    }

    /** @test **/
    public function it_set_default_values()
    {
        $service = (new Service())->toArray();

        $this->assertTrue(Uuid::isValid($service['ephemeral_id']));
        $this->assertSame(App::version(), $service['version']);
        $this->assertSame('Test App', $service['name']);
    }
}
