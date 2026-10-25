<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use App\Services\MailSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class MailSettingsController extends Controller
{
    public function index(): View
    {
        $settings = MailSetting::query()->first();

        return view('admin.settings.mail', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = MailSetting::query()->first();

        $validated = $request->validate([
            'host' => [
                'required',
                'string',
                'max:255',
            ],

            'port' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],

            'encryption' => [
                'required',
                'in:tls,ssl,none',
            ],

            // SMTP authentication requires the complete email address
            // for Gmail and many other email providers.
            'username' => [
                'required',
                'email',
                'max:255',
            ],

            'password' => [
                $settings && $settings->password
                    ? 'nullable'
                    : 'required',
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
            'username.required' =>
                'Enter the complete SMTP email address.',

            'username.email' =>
                'Enter a valid SMTP email address, including @gmail.com if you use Gmail. Do not enter only the account name.',

            'password.required' =>
                'Enter an SMTP password or provider-approved App Password.',

            'from_address.email' =>
                'Enter a valid sender email address.',

            'host.required' =>
                'Enter your SMTP server host.',

            'port.required' =>
                'Enter the SMTP port.',
        ]);

        // Empty means retain the currently saved password.
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['provider'] = 'smtp';
        $validated['is_enabled'] = $request->boolean('is_enabled');
        $validated['updated_by'] = $request->user()->id;

        if ($settings) {
            $settings->update($validated);
        } else {
            $settings = MailSetting::query()->create($validated);
        }

        return redirect()
            ->route('admin.settings.mail.index')
            ->with(
                'success',
                'Email settings saved successfully. Send a test email to verify the SMTP connection before relying on email notifications.'
            );
    }

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
                        'Email sending is not enabled. Save and enable the SMTP settings first.',
                ])
                ->withInput();
        }

        try {
            // Apply the current database configuration.
            $mailSettings->apply();

            Mail::mailer('smtp')->raw(
                "Good day,\n\n"
                . "This is a test email from iSWD - Sagay Water District.\n\n"
                . "The system successfully submitted this test message "
                . "to the configured email transport.\n\n"
                . "If you received this message, the SMTP configuration "
                . "is able to send email to this recipient. This does not "
                . "guarantee delivery to every recipient's inbox.\n\n"
                . "Sincerely,\n"
                . "Sagay Water District\n"
                . "iSWD System Notification\n\n"
                . "This is an automated message. Please do not reply.",
                function ($message) use ($validated, $settings) {
                    $message
                        ->to($validated['test_email'])
                        ->subject('iSWD | SMTP Configuration Test')
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
            // Keep the technical details in the server log, not in the UI.
            report($exception);

            return back()
                ->withErrors([
                    'test_email' =>
                        'We could not send the test email. Check the SMTP username, App Password, host, port, and encryption. If the problem continues, contact the system administrator using the application logs.',
                ])
                ->withInput();
        }
    }
}