<?php

namespace GeTracker\BasicActivityLog;

use Config;
use GeTracker\BasicActivityLog\Handlers\BeforeHandler;
use GeTracker\BasicActivityLog\Handlers\DefaultLaravelHandler;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Auth\Guard;
use Request;

class ActivityLogSupervisor
{
    /**
     * @var array logHandlers
     */
    protected $logHandlers = [];

    protected $auth;

    protected $config;

    /**
     * Create the logsupervisor using a default Handler
     * Also register Laravels Log Handler if needed.
     *
     * @param Handlers\ActivityLogHandlerInterface $logHandler
     * @param Repository                           $config
     * @param Guard                                $auth
     */
    public function __construct(
        Handlers\ActivityLogHandlerInterface $logHandler,
        Repository $config,
        Guard $auth
    ) {
        $this->config = $config;

        $this->logHandlers[] = $logHandler;

        if ($this->config->get('basic-activitylog.alsoLogInDefaultLog')) {
            $this->logHandlers[] = new DefaultLaravelHandler();
        }

        $this->auth = $auth;
    }

    /**
     * Log some activity to all registered log handlers.
     *
     * @param $text
     * @param string|int $userId
     *
     * @return bool
     */
    public function log($text, $userId = '')
    {
        $userId = $this->normalizeUserId($userId);

        if (! $this->shouldLogCall($text, $userId)) {
            return false;
        }

        $ipAddress = Request::getClientIp();

        foreach ($this->logHandlers as $logHandler) {
            $logHandler->log($text, $userId, compact('ipAddress'));
        }

        return true;
    }

    /**
     * Clean out old entries in the log.
     *
     * @return bool
     */
    public function cleanLog()
    {
        foreach ($this->logHandlers as $logHandler) {
            $logHandler->cleanLog(
                $this->config->get('basic-activitylog.deleteRecordsOlderThanMonths')
            );
        }

        return true;
    }

    /**
     * Normalize the user id.
     *
     * @param object|int $userId
     *
     * @return int|string
     */
    public function normalizeUserId($userId)
    {
        if (is_numeric($userId)) {
            return $userId;
        }

        if (is_object($userId)) {
            return $userId->id;
        }

        if ($this->auth->check()) {
            return $this->auth->user()->id;
        }

        if (is_numeric($this->config->get('basic-activitylog.defaultUserId'))) {
            return $this->config->get('basic-activitylog.defaultUserId');
        }

        return '';
    }

    /**
     * Determine if this call should be logged.
     *
     * @param $text
     * @param $userId
     *
     * @return bool
     */
    protected function shouldLogCall($text, $userId)
    {
        $beforeHandler = $this->config->get('basic-activitylog.beforeHandler');

        if ($beforeHandler === null || $beforeHandler === '') {
            return true;
        }

        return app($beforeHandler)->shouldLog($text, $userId);
    }
}
