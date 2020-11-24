<?php

namespace NotificationChannels\Kavenegar;

use Illuminate\Notifications\Notification;
use Kavenegar\KavenegarApi;

/**
 * Class KavenegarChannel.
 */
class KavenegarChannel
{
    /**
     * @var KavenegarApi
     */
    protected $api;

    /**
     * Channel constructor.
     *
     * @param KavenegarApi $api
     */
    public function __construct(KavenegarApi $api)
    {
        $this->api = $api;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed        $notifiable
     * @param  Notification $notification
     * @return null|array
     * @throws Exception|HttpException|ApiException
     */
    public function send($notifiable, Notification $notification): ?array
    {
        $message = $notification->toSMS($notifiable);
        if ($message->hasNoReceptor()) {
            if (! $to = $notifiable->routeNotificationFor('sms')) {
                return null;
            }

            $message->to($to);
        }
        $method = $message->getMethod();
        if( $method == 'sms' ) {
            return $this->sendSMS($message);
        } else if( $method == 'otp' ) {
            return $this->sendOTP($message);
        }

    }

    /**
     * Send SMS notificatioon.
     *
     * @param  KavenegarMessage $message
     * @return null|array
     * @throws Exception|HttpException|ApiException
     */
    protected function sendSMS($message) {
        $payload = $message->toArray();
        $sender = config('services.kavenegar.sender');
        return $this->api->Send($sender, $payload['receptor'], $payload['message']);
    }


    /**
     * Send OTP notificatioon.
     *
     * @param  KavenegarMessage $message
     * @return null|array
     * @throws Exception|HttpException|ApiException
     */
    protected function sendOTP($message) {
        $payload = $message->toArray();

        $params = [
            $payload['receptor'],
            $payload['token'],
            $payload['token2'],
            $payload['token3'],
            $payload['template'],
        ];
        if( $message->hasExtraTokens() ) {
            array_push($params, ...['sms',$payload['token10'],$payload['token20']]);
        }

        return $this->api->VerifyLookup(...$params);
    }

}