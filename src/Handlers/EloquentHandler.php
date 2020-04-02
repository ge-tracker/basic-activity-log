<?php

namespace GeTracker\BasicActivityLog\Handlers;

use Carbon\Carbon;
use GeTracker\BasicActivityLog\Models\Activity;

class EloquentHandler implements ActivityLogHandlerInterface
{
    /**
     * Log activity in an Eloquent model.
     *
     * @param string $text
     * @param        $userId
     * @param array  $attributes
     *
     * @return bool
     */
    public function log($text, $userId = '', $attributes = [])
    {
        Activity::create(
            [
                'text'       => $text,
                'user_id'    => ($userId === '' ? null : $userId),
                'ip_address' => $attributes['ipAddress'],
            ]
        );

        return true;
    }

    /**
     * Clean old log records.
     *
     * @param int $maxAgeInMonths
     *
     * @return bool
     */
    public function cleanLog($maxAgeInMonths)
    {
        $minimumDate = Carbon::now()->subMonths($maxAgeInMonths);
        Activity::where('created_at', '<=', $minimumDate)->delete();

        return true;
    }
}
