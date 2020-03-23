<?php

namespace Aviationcode\EcsLogging\Tests\Unit\Types;

use AviationCode\EcsLogging\EcsLoggingServiceProvider;
use AviationCode\EcsLogging\Types\User;
use Illuminate\Foundation\Auth\User as UserModel;
use Orchestra\Testbench\TestCase;

class UserTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [EcsLoggingServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('ecs-logging.formatter.user', [
            'hash' => false,
            'domain' => false,
            'email' => 'email',
            'full_name' => 'name',
            'name' => 'email',
        ]);
    }

    /** @test **/
    public function it_has_user_key()
    {
        $this->assertSame('user', (new User(new UserModel([])))->getKey());
    }

    /** @test **/
    public function it_logs_user_object()
    {
        $user = new UserModel();
        $user->setRawAttributes([
            'id' => 123,
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $this->assertEquals([
            'id' => 123,
            'email' => 'test@example.com',
            'name' => 'test@example.com',
            'full_name' => 'Test User',
        ], (new User($user))->toArray());
    }

    /** @test **/
    public function it_logs_user_object_hashed()
    {
        $this->app['config']->set('ecs-logging.formatter.user.hash', true);

        $user = new UserModel();
        $user->setRawAttributes([
            'id' => 123,
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $attributes = [
            'id' => 123,
            'email' => 'test@example.com',
            'name' => 'test@example.com',
            'full_name' => 'Test User',
        ];
        ksort($attributes);

        $hash = hash('sha256', json_encode($attributes));

        $this->assertEquals(['hash' => $hash], (new User($user))->toArray());
    }

    /** @test **/
    public function it_can_use_a_custom_hash_method()
    {
        $this->app['config']->set('ecs-logging.formatter.user.hash', 'getKey');

        $user = new UserModel();
        $user->setRawAttributes([
            'id' => 123,
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $this->assertEquals(['hash' => 123], (new User($user))->toArray());
    }
}
