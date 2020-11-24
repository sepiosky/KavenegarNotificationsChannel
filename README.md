# Kavenegar Notifications Channel for Laravel

This package makes it easy to send SMS notification using [Kavenegar API](https://kavenegar.com) (both [SMS webservice](https://kavenegar.com/register-webservice-sms.html) and [OTP webservice](https://kavenegar.com/sms/verification)) with Laravel.


## Contents

- [Installation](#installation)
- [Usage](#usage)
  - [Plain Text SMS Notification](#plain-text-sms-notification)
  - [OTP SMS Notification](#otp-sms-notification)
  - [Routing Messages](#routing-messages)
  - [Available Message methods](#available-message-methods)
  - [Handling Response](#handling-response)


## Installation

You can install the package via composer:

```bash
composer require sepiosky/kavenegar-notifications-channel
```

Then, add your Kavenegar API credentials to `config/services.php`:
(note that `sender` is only used in SMS webservice(not OTP). it also is optional)

```php
// config/services.php
'kavenegar' => [
    'key' => env('KAVENEGAR_API_KEY'),
    'sender' => env('KAVENEGAR_SENDER')
],
```

And register Kavenegar service provider in `config/app.php` (in Laravel 5.5 and later this will be done automatically):

```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\Kavenegar\KavenegarServiceProvider::class,
    ...
],
```

## Usage

after installing you can use Kavenegar in your Notifications by registering `KavenegarChannel` in `via()` method:

### Plain Text SMS Notification

the `method` attibute of `KavenegarMessage` is `sms` by default:

```php
use NotificationChannels\Kavenegar\KavenegarChannel;
use NotificationChannels\Kavenegar\KavenegarMessage;
use Illuminate\Notifications\Notification;

class WelcomeMessage extends Notification
{
    public function via($notifiable)
    {
        return [KavenegarChannel::class];
    }

    public function toSMS($notifiable)
    {
        return KavenegarMessage::create()
            ->method('sms')
            //its optional since KavenegarMessage has it deafult method set sms
            ->to($notifiable->phone)
            ->Message('Dear '.$notifiable->username.'! Thanks for joining us');
    }
}
```

### OTP SMS Notification

For this type of notifications you should set `method('otp')` on returning `KavenegarMessage`:

```php
use NotificationChannels\Kavenegar\KavenegarChannel;
use NotificationChannels\Kavenegar\KavenegarMessage;
use Illuminate\Notifications\Notification;

class VerifyAccount extends Notification
{
    public function via($notifiable)
    {
        return [KavenegarChannel::class];
    }

    public function toSMS($notifiable)
    {
        $url = env('APP_URL');
        return KavenegarMessage::create()
            ->method('otp')->to($notifiable->phone)
            ->token('1425')->template('registerVerifyTemplate')
            ->token2('please')->token10($url);
    }
}
```

note that only `token` and `template` are required (you should create and verify your template on your Kavenegar pannel first) and other tokens are optional.


### Routing Messages

You can either provide phone number of the receptor to the `to($phone)` method like shown in examples or add a `routeNotificationForSms()` method in your notifiable model:

```php
/**
 * Route notifications for the Kavenegar channel.
 *
 * @return int
 */
public function routeNotificationForSms()
{
    return $this->phone_number;
}
```

### Available Message methods

- `method($method)`: (string) Method of message . (sms/otp)
- `to($phone)`: (integer) Receptor's phone number.
- `template($template)`: (string) Template name of your message (Required for OTP messages)
- `token($token)`: (string) First token of your template (equired for OTP messages)
- `token2($token)`, `token3($token)`, `token10($token)`, `token20($token)`,: (string) Optional tokens for OTP messages
- `message($message)`: (string) Body of your message (Required for SMS messages)

For API's input formats please refer the [Kavenegar REST API's docs](https://kavenegar.com/rest.html).

### Handling Response

You can make use of the [notification events](https://laravel.com/docs/8.x/notifications#notification-events) to handle the response from Kavenegar. On success, your event listener will recieve a array object with various fields as appropriate to the notification type.

For a complete list of response fields, please refer the [Kavenegar REST API's docs](https://kavenegar.com/rest.html).
