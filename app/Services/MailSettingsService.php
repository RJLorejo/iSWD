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
     * Supported providers:
     * - SMTP
     * - Resend
     * - Brevo HTTPS API
     *
     * If database settings are disabled, the existing
     * environment-based mail configuration remains unchanged.
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

        /*
        |--------------------------------------------------------------------------
        | Resend API
        |--------------------------------------------------------------------------
        */

        if ($provider === 'resend') {
            if (!config('services.resend.key')) {
                throw new RuntimeException(
                    'Resend API key is not configured.'
                );
            }

            Config::set('mail.default', 'resend');

            Config::set(
                'mail.from.address',
                $settings->from_address
            );

            Config::set(
                'mail.from.name',
                $settings->from_name
            );

            Mail::purge('resend');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Brevo HTTPS API
        |--------------------------------------------------------------------------
        */

        if ($provider === 'brevo') {
    $apiKey = config('services.brevo.key');

    // Allow the application to run even when Brevo is not configured locally.
    // Actual email sending will still require a valid API key.
    if (!is_string($apiKey) || trim($apiKey) === '') {
        return;
    }

    Config::set('mail.default', 'brevo');
    Config::set('mail.mailers.brevo.key', $apiKey);
    Config::set('mail.from.address', $settings->from_address);
    Config::set('mail.from.name', $settings->from_name);

    Mail::purge('brevo');

    return;
}

        /*
        |--------------------------------------------------------------------------
        | SMTP
        |--------------------------------------------------------------------------
        */

        if ($provider === 'smtp') {
            Config::set('mail.default', 'smtp');

            Config::set(
                'mail.mailers.smtp',
                array_merge(
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
                )
            );

            Config::set(
                'mail.from.address',
                $settings->from_address
            );

            Config::set(
                'mail.from.name',
                $settings->from_name
            );

            Mail::purge('smtp');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Unsupported Provider
        |--------------------------------------------------------------------------
        */

        throw new RuntimeException(
            'Unsupported email provider: ' . $provider
        );
    }
}
