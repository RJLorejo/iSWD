<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use App\Services\MailSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class MailSettingsController extends Controller
{
    /**
     * Display email settings.
     */
    public function index(): View
    {
        $settings = MailSetting::query()->first();

        return view('admin.settings.mail', compact('settings'));
    }

    /**
     * Save and apply email provider settings.
     */
    public function update(
        Request $request,
        MailSettingsService $mailSettings
    ): RedirectResponse {

        $settings = MailSetting::query()->first();

        $validated = $request->validate([
            'provider' => [
                'required',
                'in:smtp,resend',
            ],

            'host' => [
                'required_if:provider,smtp',
                'nullable',
                'string',
                'max:255',
            ],

            'port' => [
                'required_if:provider,smtp',
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],

            'encryption' => [
                'required_if:provider,smtp',
                'nullable',
                'in:tls,ssl,none',
            ],

            'username' => [
                'required_if:provider,smtp',
                'nullable',
                'email',
                'max:255',
            ],

            'password' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'from_address' => [
                'required',
                'email',
                'max:255',
            ],

            'from_name' => [
                'required',
                'string',
                'max:255',
            ],

            'is_enabled' => [
                'nullable',
                'boolean',
            ],
        ], [
            'provider.required' =>
            'Select an email provider.',

            'provider.in' =>
            'Select either SMTP or Resend.',

            'username.required_if' =>
            'Enter the complete SMTP email address.',

            'username.email' =>
            'Enter a valid SMTP email address, including @gmail.com if you use Gmail.',

            'password.max' =>
            'The SMTP password is too long.',

            'from_address.required' =>
            'Enter the sender email address.',

            'from_address.email' =>
            'Enter a valid sender email address.',

            'host.required_if' =>
            'Enter your SMTP server host.',

            'port.required_if' =>
            'Enter the SMTP port.',

            'encryption.required_if' =>
            'Select the SMTP encryption type.',
        ]);

        $provider = $validated['provider'];

        /*
        |--------------------------------------------------------------------------
        | Preserve SMTP fields when Resend is selected
        |--------------------------------------------------------------------------
        */

        if ($provider === 'resend') {

            $validated['host'] = $settings?->host
                ?? 'smtp.gmail.com';

            $validated['port'] = $settings?->port
                ?? 587;

            $validated['encryption'] = $settings?->encryption
                ?? 'tls';

            $validated['username'] = $settings?->username
                ?? $validated['from_address'];
        }

        /*
        |--------------------------------------------------------------------------
        | Preserve existing SMTP password
        |--------------------------------------------------------------------------
        */

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['is_enabled'] = $request->boolean('is_enabled');
        $validated['updated_by'] = $request->user()->id;

        try {

            if ($settings) {
                $settings->update($validated);
            } else {
                MailSetting::query()->create($validated);
            }

            $mailSettings->apply();

            return redirect()
                ->route('admin.settings.mail.index')
                ->with(
                    'mail_settings_saved',
                    'Your email provider configuration has been saved and applied. Send a test email to confirm that your system can submit messages successfully.'
                );
        } catch (Throwable $exception) {

            report($exception);

            return back()
                ->withErrors([
                    'provider' =>
                    'The email settings could not be saved or applied. Please review your provider configuration and try again.',
                ])
                ->withInput($request->except('password'));
        }
    }

    /**
     * Send a test email using the currently selected provider.
     */
    public function test(
        Request $request,
        MailSettingsService $mailSettings
    ): RedirectResponse {

        $validated = $request->validate([
            'test_email' => [
                'required',
                'email',
                'max:255',
            ],
        ]);

        $settings = MailSetting::query()->first();

        /*
        |--------------------------------------------------------------------------
        | Check whether database email settings are enabled
        |--------------------------------------------------------------------------
        */

        if (!$settings || !$settings->is_enabled) {

            return back()->with(
                'mail_test_error',
                'Database email settings are not enabled. Please enable and save your email provider settings before sending a test email.'
            );
        }

        $provider = strtolower($settings->provider ?? 'smtp');

        try {

            /*
            |--------------------------------------------------------------------------
            | Apply saved provider configuration
            |--------------------------------------------------------------------------
            */

            $mailSettings->apply();

            $mailerName = Config::get('mail.default');

            /*
            |--------------------------------------------------------------------------
            | Verify Resend API key
            |--------------------------------------------------------------------------
            */

            if (
                $provider === 'resend'
                && !Config::get('services.resend.key')
            ) {

                throw new RuntimeException(
                    'The Resend API key is not configured.'
                );
            }

            $providerLabel = strtoupper($provider);

            /*
            |--------------------------------------------------------------------------
            | Send test email
            |--------------------------------------------------------------------------
            */

            Mail::mailer($mailerName)->raw(
                "Good day,\n\n"
                    . "This is a test email from iSWD - Sagay Water District.\n\n"
                    . "The system successfully submitted this test message "
                    . "using the configured {$providerLabel} email provider.\n\n"
                    . "If you received this message, the provider accepted "
                    . "the test email for delivery. This does not guarantee "
                    . "delivery to every recipient's inbox.\n\n"
                    . "Sincerely,\n"
                    . "Sagay Water District\n"
                    . "iSWD System Notification\n\n"
                    . "This is an automated message. Please do not reply.",

                function ($message) use (
                    $validated,
                    $settings,
                    $providerLabel
                ) {

                    $message
                        ->to($validated['test_email'])
                        ->subject(
                            "iSWD | {$providerLabel} Email Configuration Test"
                        )
                        ->from(
                            $settings->from_address,
                            $settings->from_name
                        );
                }
            );

            return back()->with(
                'mail_test_sent',
                'Your test message was accepted by the configured email provider. Check the recipient inbox, including the Spam folder, to confirm that it arrived.'
            );
        } catch (Throwable $exception) {

            report($exception);

            $message = $provider === 'resend'

                ? 'We could not send the test email through Resend. Check the Railway RESEND_API_KEY, sender verification, and Resend account restrictions.'

                : 'We could not send the test email. Check the SMTP username, SMTP password, host, port, encryption, and sender authorization.';

            return back()
                ->with('mail_test_error', $message)
                ->withInput($request->except('password'));
        }
    }
}
