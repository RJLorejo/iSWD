@extends('admin.layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl space-y-6">

            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">
                    System Administration
                </p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">
                    Notification Settings
                </h1>
                <p class="mt-2 text-sm text-slate-600">
                    Configure the email service used by iSWD for verification codes,
                    password resets, and other system notifications.
                </p>
            </div>

            @if (session('success'))
                <div role="status" aria-live="polite"
                    class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>

                    <div>
                        <p class="font-semibold"> Completed successfully</p>
                        <p class="mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div role="alert" aria-live="assertive"
                    class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>

                    <div>
                        <p class="font-semibold">Something went wrong</p>
                        <p class="mt-1">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-semibold">Please check the following:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                <div class="flex items-start gap-3">
                    <div class="rounded-xl bg-white p-3 text-blue-700 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M3 8l9 6 9-6M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-semibold text-slate-900">
                            Email delivery configuration
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-slate-700">
                            Choose SMTP or Resend as the email delivery provider.
                            Save your settings and send a test message before
                            relying on the configuration for system notifications.
                        </p>
                        <p class="mt-2 text-sm text-slate-600">
                            SMTP passwords are encrypted in the database.
                            The Resend API key is stored separately in the
                            hosting environment and is never displayed here.
                        </p>
                    </div>
                </div>
            </div>

            <form id="mail-settings-form" method="POST" action="{{ route('admin.settings.mail.update') }}"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @csrf
                @method('PUT')

                @php
                    $selectedProvider = old('provider', $settings->provider ?? 'smtp');
                @endphp

                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <h2 class="text-lg font-semibold text-slate-900">
                        Email provider settings
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Select your provider and configure its sender identity.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">

                    {{-- Provider selector --}}
                    <div class="sm:col-span-2">
                        <label for="provider" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Email provider
                        </label>

                        <select id="provider" name="provider" required
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="smtp" @selected($selectedProvider === 'smtp')>
                                SMTP
                            </option>
                            <option value="resend" @selected($selectedProvider === 'resend')>
                                Resend
                            </option>
                        </select>

                        <p class="mt-1 text-xs text-slate-500">
                            SMTP uses your configured mail server. Resend sends
                            email through its API and is suitable for Railway hosting.
                        </p>
                    </div>

                    {{-- SMTP information --}}
                    <div id="smtp-information" class="sm:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-sm font-semibold text-slate-900">
                            SMTP configuration
                        </h3>
                        <p class="mt-1 text-sm leading-6 text-slate-600">
                            Enter your provider's SMTP host, port, encryption,
                            username, and approved password or app password.
                        </p>
                    </div>

                    {{-- SMTP fields --}}
                    <div id="smtp-fields" class="contents">

                        <div>
                            <label for="host" class="mb-1.5 block text-sm font-medium text-slate-700">
                                SMTP host
                            </label>
                            <input id="host" name="host" type="text" maxlength="255"
                                value="{{ old('host', $settings->host ?? 'smtp.gmail.com') }}" placeholder="smtp.gmail.com"
                                autocomplete="off" data-smtp-required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="port" class="mb-1.5 block text-sm font-medium text-slate-700">
                                SMTP port
                            </label>
                            <input id="port" name="port" type="number" min="1" max="65535"
                                value="{{ old('port', $settings->port ?? 587) }}" data-smtp-required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="encryption" class="mb-1.5 block text-sm font-medium text-slate-700">
                                Encryption
                            </label>
                            <select id="encryption" name="encryption" data-smtp-required
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <option value="tls" @selected(old('encryption', $settings->encryption ?? 'tls') === 'tls')>
                                    TLS / STARTTLS — port 587
                                </option>
                                <option value="ssl" @selected(old('encryption', $settings->encryption ?? 'tls') === 'ssl')>
                                    SSL / implicit TLS — port 465
                                </option>
                                <option value="none" @selected(old('encryption', $settings->encryption ?? 'tls') === 'none')>
                                    None — provider must support it
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700">
                                SMTP username
                            </label>
                            <input id="username" name="username" type="email" maxlength="255"
                                value="{{ old('username', $settings->username ?? '') }}" placeholder="your@example.com"
                                autocomplete="off" data-smtp-required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">
                                SMTP password / app password
                            </label>
                            <input id="password" name="password" type="password" maxlength="2000"
                                autocomplete="new-password"
                                placeholder="{{ $settings ? 'Leave blank to keep saved password' : 'Enter SMTP password' }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <p class="mt-1 text-xs text-slate-500">
                                Leave blank to retain the saved password.
                                Never enter your regular Google account password.
                            </p>
                        </div>

                    </div>

                    {{-- Resend information --}}
                    <div id="resend-information"
                        class="hidden sm:col-span-2 rounded-xl border border-violet-200 bg-violet-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="rounded-lg bg-white p-2 text-violet-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M4 7h16M4 12h16M4 17h16" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-violet-950">
                                    Resend API configuration
                                </h3>
                                <p class="mt-1 text-sm leading-6 text-violet-900">
                                    Resend uses an API key instead of SMTP credentials.
                                    The API key must be configured in Railway under the
                                    Laravel service's environment variables.
                                </p>
                                <p class="mt-2 text-sm font-semibold text-violet-950">
                                    Variable name: RESEND_API_KEY
                                </p>
                                <p class="mt-1 text-xs leading-5 text-violet-800">
                                    Do not paste your API key into this page.
                                    Use a sender address authorized by Resend.
                                    The testing sender may have recipient restrictions.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Sender identity --}}
                    <div>
                        <label for="from_address" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Sender email address
                        </label>
                        <input id="from_address" name="from_address" type="email" required maxlength="255"
                            value="{{ old('from_address', $settings->from_address ?? '') }}"
                            placeholder="notifications@example.com"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <p id="sender-help" class="mt-1 text-xs text-slate-500">
                            Use an email address authorized by your SMTP provider.
                        </p>
                    </div>

                    <div>
                        <label for="from_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Sender display name
                        </label>
                        <input id="from_name" name="from_name" type="text" required maxlength="255"
                            value="{{ old('from_name', $settings->from_name ?? 'iSWD - Sagay Water District') }}"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>

                    {{-- Enable email --}}
                    <div class="sm:col-span-2">
                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                            <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $settings ? $settings->is_enabled : true))
                                class="mt-1 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">

                            <span>
                                <span class="block text-sm font-semibold text-slate-900">
                                    Enable database email settings
                                </span>
                                <span class="mt-1 block text-sm text-slate-500">
                                    When enabled, the selected provider and saved
                                    sender settings take precedence over the
                                    environment mail configuration.
                                </span>
                            </span>
                        </label>
                    </div>

                </div>

                <div
                    class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-xs leading-5 text-slate-500">
                        Save the settings first, then send a test email.
                    </p>
                    <button id="save-mail-settings" type="submit"
                        class="inline-flex min-w-[190px] items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70">

                        <svg id="save-mail-spinner" class="hidden h-4 w-4 animate-spin"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>

                        <span id="save-mail-button-text">Save Email Settings</span>
                    </button>
                </div>
            </form>

            {{-- Test email --}}
            <form id="test-mail-form" method="POST" action="{{ route('admin.settings.mail.test') }}"
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                @csrf

                <h2 class="text-lg font-semibold text-slate-900">
                    Send a test email
                </h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Save and enable your email provider first. Then send a test
                    message to an inbox you can access.
                </p>

                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                    <div class="flex-1">
                        <label for="test_email" class="sr-only">
                            Test recipient email address
                        </label>
                        <input id="test_email" name="test_email" type="email" required maxlength="255"
                            value="{{ old('test_email') }}" placeholder="your-test-inbox@example.com"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>

                    <button id="send-test-mail-button" type="submit" @disabled(!$settings || !$settings->is_enabled)
                        class="inline-flex min-w-[170px] items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">

                        <svg id="test-mail-spinner" class="hidden h-4 w-4 animate-spin"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>

                        <span id="test-mail-button-text">Send Test Email</span>
                    </button>
                </div>
            </form>

            {{-- SMTP instructions --}}
            <section id="smtp-instructions" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-semibold text-slate-900">
                    SMTP setup instructions
                </h2>

                <div class="mt-4 space-y-4 text-sm leading-6 text-slate-600">
                    <div>
                        <h3 class="font-semibold text-slate-900">
                            Gmail / Google Workspace
                        </h3>
                        <ul class="mt-1 list-inside list-disc space-y-1">
                            <li>SMTP host: <code>smtp.gmail.com</code></li>
                            <li>Port: <code>587</code></li>
                            <li>Encryption: TLS / STARTTLS</li>
                            <li>Username: the authorized sending email address</li>
                            <li>Password: an app password, if supported and enabled for the account</li>
                        </ul>
                        <p class="mt-2">
                            Google account security requirements apply. If an app
                            password is unavailable, use an approved alternative
                            such as an authorized mail provider or supported API.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-slate-900">
                            Before deploying online
                        </h3>
                        <ul class="mt-1 list-inside list-disc space-y-1">
                            <li>Check that your hosting provider permits outbound SMTP.</li>
                            <li>Use a sender address authorized by the provider.</li>
                            <li>Test registration OTP and password-reset OTP after configuration.</li>
                            <li>Never commit SMTP credentials to GitHub.</li>
                        </ul>
                    </div>
                </div>
            </section>

            {{-- Resend instructions --}}
            <section id="resend-instructions"
                class="hidden rounded-2xl border border-violet-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-semibold text-slate-900">
                    Resend setup instructions
                </h2>

                <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                    <p>
                        Resend sends messages through its API, so the application
                        does not need an SMTP connection.
                    </p>
                    <ol class="list-inside list-decimal space-y-2">
                        <li>Configure <code>RESEND_API_KEY</code> in the Railway Laravel service.</li>
                        <li>Use a sender address authorized by your Resend account.</li>
                        <li>Save and enable Resend in this page.</li>
                        <li>Send a test email to verify the provider configuration.</li>
                        <li>Test consumer registration and password-reset OTP delivery.</li>
                    </ol>
                    <p class="rounded-lg bg-amber-50 p-3 text-amber-900">
                        <strong>Important:</strong> Resend's testing sender
                        <code>onboarding@resend.dev</code> may only send to
                        verified recipients associated with your Resend account.
                        For consumer emails to arbitrary addresses, configure
                        a sender domain verified by Resend.
                    </p>
                </div>
            </section>

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
                <strong>Security reminder:</strong>
                Access to this page should remain limited to Administrators.
                Keep your Laravel APP_KEY stable until any required key rotation
                has been planned safely. Database-encrypted passwords cannot be
                decrypted if the key used to encrypt them is lost.
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provider = document.getElementById('provider');

            const smtpInformation = document.getElementById('smtp-information');
            const smtpFields = document.getElementById('smtp-fields');
            const smtpInstructions = document.getElementById('smtp-instructions');

            const resendInformation = document.getElementById('resend-information');
            const resendInstructions = document.getElementById('resend-instructions');

            const senderHelp = document.getElementById('sender-help');

            const smtpRequiredFields = document.querySelectorAll('[data-smtp-required]');

            const settingsForm = document.getElementById('mail-settings-form');
            const saveButton = document.getElementById('save-mail-settings');
            const saveSpinner = document.getElementById('save-mail-spinner');
            const saveButtonText = document.getElementById('save-mail-button-text');

            /*
            |--------------------------------------------------------------------------
            | Provider selection
            |--------------------------------------------------------------------------
            */

            function updateProviderFields() {
                const isResend = provider.value === 'resend';

                smtpInformation.classList.toggle('hidden', isResend);
                smtpFields.classList.toggle('hidden', isResend);
                smtpInstructions.classList.toggle('hidden', isResend);

                resendInformation.classList.toggle('hidden', !isResend);
                resendInstructions.classList.toggle('hidden', !isResend);

                smtpRequiredFields.forEach(function(field) {
                    field.required = !isResend;
                    field.disabled = isResend;
                });

                senderHelp.textContent = isResend ?
                    'Use a sender address authorized or verified in your Resend account.' :
                    'Use an email address authorized by your SMTP provider.';
            }

            provider.addEventListener('change', updateProviderFields);

            updateProviderFields();

            /*
            |--------------------------------------------------------------------------
            | Save button loading state
            |--------------------------------------------------------------------------
            | The form still uses normal browser submission.
            | If JavaScript is unavailable, Laravel receives the regular form request.
            */

            if (settingsForm && saveButton) {
                settingsForm.addEventListener('submit', function() {
                    // Prevent duplicate submissions.
                    if (saveButton.disabled) {
                        return;
                    }

                    saveButton.disabled = true;

                    saveSpinner.classList.remove('hidden');

                    saveButtonText.textContent = 'Saving settings...';

                    saveButton.classList.remove('hover:bg-blue-800');
                });
            }

            /*
        |--------------------------------------------------------------------------
        | Test email loading state
        |--------------------------------------------------------------------------
        | Keep standard form submission as fallback.
        */

            const testMailForm = document.getElementById('test-mail-form');
            const testMailButton = document.getElementById('send-test-mail-button');
            const testMailSpinner = document.getElementById('test-mail-spinner');
            const testMailButtonText = document.getElementById('test-mail-button-text');

            if (testMailForm && testMailButton) {
                testMailForm.addEventListener('submit', function() {
                    if (testMailButton.disabled) {
                        return;
                    }

                    testMailButton.disabled = true;

                    testMailSpinner.classList.remove('hidden');

                    testMailButtonText.textContent = 'Sending test email...';

                    testMailButton.classList.remove('hover:bg-slate-700');
                });
            }
        });
    </script>
@endsection
