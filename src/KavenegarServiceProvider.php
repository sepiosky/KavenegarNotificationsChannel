<?php

namespace NotificationChannels\Kavenegar;

use Illuminate\Support\ServiceProvider;
use Kavenegar\KavenegarApi;

/**
 * Class KavenegarServiceProvider.
 */
class KavenegarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(KavenegarChannel::class)
            ->needs(KavenegarApi::class)
            ->give(function () {
                return new KavenegarApi(config('services.kavenegar.key'));
            });
    }
}