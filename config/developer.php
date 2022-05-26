<?php

return [
    /**
     * The database table name.
     */
    'table' => 'developer_logs',

    /**
     * The model used to manage logs.
     */
    'model' => \Binarcode\LaravelDeveloper\Models\DeveloperLog::class,

    /**
     * Indicate whether to allow sending slack notifications and persist them.
     */
    'slack_dev_enabled' => env('SLACK_DEV_LOG_ENABLED', true),

    /**
     * The slack webhook to send notifications.
     */
    'slack_dev_hook' => env('SLACK_DEV_HOOK'),

    /**
     * The url to the web where you may display the exception.
     *
     * For instance you can use nova, so the url will look like: /nova/resources/exception-logs/{id}
     *
     * We will replace the uuid with the exception log uuid.
     */
    'developer_log_base_url' => env('DEVELOPER_LOG_BASE_URL'),

    /**
     * The default notification class used to send notifications.
     */
    'notification' => \Binarcode\LaravelDeveloper\Notifications\DevNotification::class,

    'auth' => [
        'bearer' => 'testing',
    ],

    /**
     * If this is true, you should ensure you have telescope installed and the package will store exceptions in the telescope as well.
     */
    'telescope' => env('DEV_TELESCOPE'),
];
