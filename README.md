# Lightweight package help your development eyes.


<p align="center">
<img src="https://raw.githubusercontent.com/BinarCode/laravel-developer/master/static/Laravel_Developer.png">
</p>

<p align="center">
<a href="https://github.com/binarcode/laravel-developer/actions"><img src="https://github.com/BinarCode/laravel-developer/workflows/Tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/binarcode/laravel-developer"><img src="https://img.shields.io/packagist/dt/binarcode/laravel-developer" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/binarcode/laravel-developer"><img src="https://img.shields.io/packagist/v/binarcode/laravel-developer" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/binarcode/laravel-developer"><img src="https://img.shields.io/packagist/l/binarcode/laravel-developer" alt="License"></a>
</p>

## Installation

You can install the package via composer:

```bash
composer require binarcode/laravel-developer
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Binarcode\LaravelDeveloper\LaravelDeveloperServiceProvider" --tag="developer-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Binarcode\LaravelDeveloper\LaravelDeveloperServiceProvider" --tag="developer-config"
```

This is the contents of the published config file:

```php
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
```

## Usage

### Send exception to slack

The simplies way to use the package is to send an exception to the slack:

```php
use Binarcode\LaravelDeveloper\LaravelDeveloper;

LaravelDeveloper::exceptionToDevSlack(
    new \Exception('wew')
);
```

### Send message to slack

Use this to send any message to your dev slack: 

```php
use Binarcode\LaravelDeveloper\LaravelDeveloper;

LaravelDeveloper::messageToDevSlack('Hey, we have troubles ;-)');
```

### Send anything to slack

Obviously, you can send any kind of message to the slack channel. The `toDevSlack` method accept an instance of `DevNotificationDto`:

```php
use Binarcode\LaravelDeveloper\LaravelDeveloper;
use Binarcode\LaravelDeveloper\Dtos\DevNotificationDto;

LaravelDeveloper::toDevSlack(
    DevNotificationDto::makeWithMessage('hey')
);
```

### Persist exception

If you want to persist the exception into the database, in any place you want to catch and log an exception, you can do something like this: 

```php
use Binarcode\LaravelDeveloper\Models\ExceptionLog;

try {
    // Your custom code
} catch (\Throwable $e) {
    ExceptionLog::makeFromException($e)->save();
}
```

Under the hood, the package will store an entry in the `exception_logs` of your database with the exception.

### Slack notification

If you want to send the notification to the slack webhook, just call the
`notifyDevs()` method on your `ExceptionLog` instance.

This will send a slack notification to the incoming webhook you have specified in the `developer.slack_dev_hook` configuration file. 

### Passing other payload

You can specify payload to your exception, so it will be stored along with the exception. In the example bellow we will catch a `Cashier` exception and will log it:

```php
use Laravel\Cashier\Exceptions\PaymentFailure;
use Binarcode\LaravelDeveloper\Models\ExceptionLog;

try {
    // Your custom code
} catch (PaymentFailure $e) {
ExceptionLog::makeFromException($e, $e->payment->asStripePaymentIntent())->notifyDevs();
}
```

The second `$e->payment->asStripePaymentIntent()` object MUST implement the `\JsonSerializable()` interface.

You can add more payloads by using:

```php
ExceptionLog::makeFromException($e)
->addPayload($payload1)
->addPayload($payload2)
->notifyDevs();
```

### Using a custom notification

However, Laravel Developer package provides you the `Binarcode\LaravelDeveloper\Notifications\DevNotification` notification, you are free to use a completely new one by configuring the `developer.notification` configuration: 
 
 ```php
'notification': CustomNotification::class,
```
 
If you want to take the full control over the notification sending, you can add this in one of your service providers:

```php
use Binarcode\LaravelDeveloper\LaravelDeveloper;
use Binarcode\LaravelDeveloper\Notifications\DevNotification;
use Illuminate\Support\Facades\Notification;

LaravelDeveloper::notifyUsing(function (DevNotification $argument) {
    // Here you can do anything you want(even send an email), for instance we provide here
    // an example of how you can send an anonymous notification.
    Notification::route('slack', config('developer.slack_dev_hook'))->notify(
        new CustomNotification($argument->notificationDto)
    );
});
```


### Log pruning

Without pruning, the `exception_logs` table can accumulate records very quickly. To mitigate this, you should schedule the `dev:prune` Artisan command to run daily:

```shell script
$schedule->command('dev:prune')->daily();
```

By default, all entries older than 24 hours will be pruned. You may use the hours option when calling the command to determine how long to retain Developer data. For example, the following command will delete all records created over 48 hours ago:


```shell script
$schedule->command('dev:prune --hours=48')->daily();
```


## Profiling

As a developer sometimes you have to measure the memory usage or time consumed for such action. Laravel Developer helps you to do so: 

```php
measure_memory(function() {
    // some code
});
```

Or: 

```php
$memory = \Binarcode\LaravelDeveloper\Profiling\ServerMemory::measure();

// some code memory consuming

$memory->stop();

$memory->getMemory();
```

And time measure:

```php
measure_timing(function() {
    // some code
})
```

Or: 

```php
$timing = \Binarcode\LaravelDeveloper\Profiling\ServerTiming::startWithoutKey();

sleep(1);

$timing->stopAllUnfinishedEvents();

$timing->getDuration();
```


## Dev auth

Each `api` has authentication, and testing it via HTTP Client (ie postman) we spend a lot of time to login users and copy the token, put in the next request and so on. Well now Laravel Developer provides an easy way to authenticate users in `local` env using `testing` token:

```php
// app/Http/Kernel.php

'api' => [
    //...
    \Binarcode\LaravelDeveloper\Middleware\DevAuthMiddleware::class,
]
```

And send the request with the `Authorization` header value `testing`. 

Note: Make sure the `DevAuthMiddleware` is placed before the `api` middleware.

### Customize resolved user

By default, the first entry (usually user) from your config model `app.providers.users.model` will be used, however, you can customize that.

In any of yours service providers, or in the same place you inject the `DevAuthMiddleware` you can provide a callback which resolves the user instance:

```php
use App\Models\User;
use Binarcode\LaravelDeveloper\Middleware\DevAuthMiddleware;

'middleware' => [
    DevAuthMiddleware::resolveUserUsing(function() {
        return User::first();
    });
    'api',
],
```

### Changing Bearer

If you're using laravel sanctum, and want to explicitely use / generate a Bearer for the resolved user, you can use the `\Binarcode\LaravelDeveloper\Middleware\DevAuthMiddleware::class` instead, which follow the same syntax as the `DevAuthMiddleware`.

## Tests macros

### dumpWithoutTrace

If you're annoying to always scroll up to message in your test when `->dump()` the response, you can use `->dumpWithoutTrace()` instead, it will return you back everything except the long array of trace.

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Eduard Lupacescu](https://github.com/binaryk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
