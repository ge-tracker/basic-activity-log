<?php

namespace GeTracker\BasicActivityLog;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        foreach (static::getRecordActivityEvents() as $eventName) {
            static::$eventName(function (LogsActivityInterface $model) use ($eventName) {

                $message = $model->getActivityDescriptionForEvent($eventName);

                if ($message !== '') {
                    /** @var ActivityLogSupervisor $activty */
                    $activty = app('basic-activity');
                    $activty->log($message);
                }
            });
        }
    }

    /**
     * Set the default events to be recorded if the $recordEvents
     * property does not exist on the model.
     *
     * @return array
     */
    protected static function getRecordActivityEvents()
    {
        return static::$recordEvents ?? [
                'created', 'updated', 'deleting', 'deleted',
            ];
    }
}
