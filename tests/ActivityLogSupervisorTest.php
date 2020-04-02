<?php

namespace Tests;

use GeTracker\BasicActivityLog\ActivityLogSupervisor;
use GeTracker\BasicActivityLog\BasicActivityLogServiceProvider;
use GeTracker\BasicActivityLog\Handlers\EloquentHandler;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Auth\Guard;
use Mockery;

class ActivityLogSupervisorTest extends TestCase
{
    /** @var EloquentHandler|\Mockery\LegacyMockInterface|\Mockery\MockInterface Ac */
    protected $logHandler;

    /** @var ActivityLogSupervisor */
    protected $activityLogSupervisor;

    /** @var Repository|\Mockery\LegacyMockInterface|\Mockery\MockInterface */
    protected $config;

    /** @var Guard|\Mockery\LegacyMockInterface|\Mockery\MockInterface */
    protected $auth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logHandler = Mockery::mock(EloquentHandler::class);
        $this->config = Mockery::mock(Repository::class);
        $this->auth = Mockery::mock(Guard::class);

        $this->config->shouldReceive('get')->andReturn(false);

        $this->activityLogSupervisor = new ActivityLogSupervisor(
            $this->logHandler,
            $this->config,
            $this->auth
        );

        $this->activityLogSupervisor->log()
    }

    /**
     * @test
     */
    public function it_normalizes_an_empty_user_id_when_noone_is_logged_in(): void
    {
        $this->auth->shouldReceive('check')->andReturn(false);

        $normalizedUserId = $this->activityLogSupervisor->normalizeUserId('');

        $this->assertSame('', $normalizedUserId);
    }

    /**
     * @test
     */
    public function it_normalizes_an_empty_user_id_when_someone_is_logged_in(): void
    {
        $user = json_decode(json_encode(['id' => 123]), false);

        $this->auth->shouldReceive('check')->andReturn(true);
        $this->auth->shouldReceive('user')->andReturn($user);

        $normalizedUserId = $this->activityLogSupervisor->normalizeUserId('');

        $this->assertSame(123, $normalizedUserId);
    }

    /**
     * @test
     */
    public function it_normalizes_a_numeric_user_id(): void
    {
        $normalizedUserId = $this->activityLogSupervisor->normalizeUserId(123);

        $this->assertSame(123, $normalizedUserId);
    }
}
