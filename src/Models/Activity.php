<?php

namespace GeTracker\BasicActivityLog\Models;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class Activity extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_log';

    protected $guarded = ['id'];

    /**
     * Get the user that the activity belongs to.
     *
     * @return object
     */
    public function user()
    {
        return $this->belongsTo($this->getAuthModelName(), 'user_id');
    }

    /**
     * Resolve Auth model name
     *
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     * @throws RuntimeException
     */
    public function getAuthModelName()
    {
        // User defined model in config
        if (config('basic-activitylog.userModel')) {
            return config('basic-activitylog.userModel');
        }

        // Laravel 5.2+
        if (!is_null(config('auth.providers.users.model'))) {
            return config('auth.providers.users.model');
        }

        throw new RuntimeException('could not determine the model name for users');
    }
}
