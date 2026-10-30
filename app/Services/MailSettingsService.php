<?php

namespace App\Services;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class MailSettingsService
{
    /**
     * Apply saved SMTP settings.
     *
     * If the mail_settings table does not exist, or database
     * settings are disabled, retain the .env mail configuration.
     */
    public function apply(): void
    {
        // Prevent database queries before the migration creates the table.
        if (!Schema::hasTable('mail_settings')) {
            return;
        }

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