<?php

namespace App\Services;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailSettingsService
{
    /**
     * Apply saved SMTP settings.
     *
     * When database settings are unavailable or disabled,
     * Laravel retains its existing .env-based configuration.
     */
    public function apply(): void
    {
        $settings = MailSetting::query()->first();

        if (!$settings || !$settings->is_enabled) {
            return;
        }

        Config::set('mail.default', 'smtp');

        Config::set('mail.mailers.smtp', array_merge(
            Config::get('mail.mailers.smtp', []),
            [
                'transport' => 'smtp',
                'scheme' => $settings->encryption === 'ssl'
                    ? 'smtps'
                    : 'smtp',
                'url' => null,
                'host' => $settings->host,
                'port' => $settings->port,
                'username' => $settings->username,
                'password' => $settings->password,
                'timeout' => 15,
            ]
        ));

        Config::set('mail.from.address', $settings->from_address);
        Config::set('mail.from.name', $settings->from_name);

        // Ensure Laravel does not reuse an old SMTP transport.
        Mail::purge('smtp');
    }
}
