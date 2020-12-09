<?php

return [
    /**
     * The slack incoming webhook to send notifications.
     */
    'slack_dev_hook' => env('SLACK_DEV_HOOK'),

    /**
     * The url to the web where you may display the exception.
     *
     * For instance you can use nova, so the url will look like: /nova/resources/exception-logs/{uuid}
     *
     * We will replace the uuid with the exception log uuid.
     */
    'exception_log_base_url' => env('DEV_EXCEPTION_LOG_BASE_URL'),

    /**
     * The default notification class used to send notifications.
     */
    'notification' => \Binarcode\LaravelDeveloper\Notifications\DevNotification::class,
];
