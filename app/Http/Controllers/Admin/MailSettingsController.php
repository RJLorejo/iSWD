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
         * Preserve existing SMTP values when Resend is selected.
         *
         * The current database schema requires SMTP fields even
         * when the selected provider is Resend.
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
         * An empty password means retain the existing password.
         * This also avoids overwriting the encrypted password
         * when the user switches between providers.
         */
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['is_enabled'] = $request->boolean('is_enabled');
        $validated['updated_by'] = $request->user()->id;

        try {
            /*
             * Save the settings.
             */
            if ($settings) {
                $settings->update($validated);
            } else {
                MailSetting::query()->create($validated);
            }

            /*
             * Apply the selected provider immediately.
             */
            $mailSettings->apply();

            return redirect()
                ->route('admin.settings.mail.index')
                ->with(
                    'success',
                    'Email settings saved successfully. Send a test email to verify the selected email provider.'
                );

        } catch (Throwable $exception) {
            /*
             * Keep technical details in the server log.
             * Never expose API keys or credentials in the UI.
             */
            report($exception);

            return back()
                ->withErrors([
                    'provider' =>
                        'The email settings could not be applied. Please review your provider configuration and try again.',
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

        if (!$settings || !$settings->is_enabled) {
            return back()
                ->withErrors([
                    'test_email' =>
                        'Email sending is not enabled. Save and enable your email provider settings first.',
                ])
                ->withInput();
        }

        $provider = strtolower($settings->provider ?? 'smtp');

        try {
            /*
             * Apply the currently saved provider configuration.
             */
            $mailSettings->apply();

            $mailerName = Config::get('mail.default');

            /*
             * Confirm that the Resend API key is available.
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
             * Send the test message.
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
                'success',
                'The test message was submitted successfully. Check the recipient inbox and Spam folder to confirm delivery.'
            );

        } catch (Throwable $exception) {
            /*
             * Keep technical details in the Railway logs.
             */
            report($exception);

            $message = $provider === 'resend'
                ? 'We could not send the test email through Resend. Check the Railway RESEND_API_KEY, sender verification, and Resend account restrictions.'
                : 'We could not send the test email. Check the SMTP username, App Password, host, port, and encryption.';

            return back()
                ->withErrors([
                    'test_email' => $message,
                ])
                ->withInput();
        }
    }
}