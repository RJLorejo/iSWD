<?php

namespace App\Services;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class MailSettingsService
{
    /**
     * Apply the saved database-backed email configuration.
     *
     * Supports SMTP and Resend while retaining the existing
     * .env configuration when database settings are disabled.
     */
    public function apply(): void
    {
        if (!Schema::hasTable('mail_settings')) {
            return;
        }

        $settings = MailSetting::query()->first();

        if (!$settings || !$settings->is_enabled) {
            return;
        }

        $provider = strtolower($settings->provider ?? 'smtp');

        if ($provider === 'resend') {
            if (!config('services.resend.key')) {
                throw new RuntimeException(
                    'Resend API key is not configured.'
                );
            }

            Config::set('mail.default', 'resend');

            Config::set('mail.from.address', $settings->from_address);
            Config::set('mail.from.name', $settings->from_name);

            Mail::purge('resend');

            return;
        }

        if ($provider !== 'smtp') {
            throw new RuntimeException(
                'Unsupported email provider: ' . $provider
            );
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

        Mail::purge('smtp');
    }
}