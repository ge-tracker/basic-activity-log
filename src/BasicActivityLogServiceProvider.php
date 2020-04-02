<?php

namespace GeTracker\BasicActivityLog;

use GeTracker\BasicActivityLog\Handlers\ActivityLogHandlerInterface;
use GeTracker\BasicActivityLog\Handlers\EloquentHandler;
use Illuminate\Support\ServiceProvider;

class BasicActivityLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        // Publish a config file
        $this->publishes([
            __DIR__ . '/../../config/basic-basic-activitylog.php' => config_path('basic-basic-activitylog.php'),
        ], 'config');

        if (!$this->migrationHasAlreadyBeenPublished()) {
            // Publish migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../migrations/create_activity_log_table.stub' => database_path("/migrations/{$timestamp}_create_activity_log_table.php"),
            ], 'migrations');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind(
            'basic-activity',
            ActivityLogSupervisor::class
        );

        $this->app->bind(
            ActivityLogHandlerInterface::class,
            EloquentHandler::class
        );
    }

    /**
     * @return bool
     */
    protected function migrationHasAlreadyBeenPublished(): bool
    {
        $files = glob(database_path('/migrations/*_create_activity_log_table.php'));
        return count($files) > 0;
    }
}
