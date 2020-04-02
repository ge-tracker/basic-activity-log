<?php

namespace Tests;

use GeTracker\BasicActivityLog\ActivityLogSupervisor;
use GeTracker\BasicActivityLog\Handlers\ActivityLogHandlerInterface;
use GeTracker\BasicActivityLog\Handlers\EloquentHandler;

class BasicActivityLogServiceProviderTest extends TestCase
{
    /** @test */
    public function it_should_resolve_supervisor(): void
    {
        $this->assertInstanceOf(ActivityLogSupervisor::class, app('basic-activity'));
    }

    /** @test */
    public function it_should_resolve_eloquent_handler(): void
    {
        $this->assertInstanceOf(EloquentHandler::class, app(ActivityLogHandlerInterface::class));
    }
}
