
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
            <div role="status"
                 class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert"
                 class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
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
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M3 8l9 6 9-6M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                    </svg>
                </div>

                <div>
                    <h2 class="font-semibold text-slate-900">
                        SMTP configuration
                    </h2>
                    <p class="mt-1 text-sm leading-6 text-slate-700">
                        SMTP is the service Laravel uses to send email. Save your
                        provider's settings, enable email, and send a test message
                        before relying on the configuration for notifications.
                    </p>
                    <p class="mt-2 text-sm text-slate-600">
                        Credentials are encrypted in the database. The saved
                        password is never displayed in this form.
                    </p>
                </div>
            </div>
        </div>

        <form method="POST"
              action="{{ route('admin.settings.mail.update') }}"
              class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @csrf
            @method('PUT')

            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="text-lg font-semibold text-slate-900">
                    Email provider settings
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Configure the server, credentials, and sender identity.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">

                <div class="sm:col-span-2">
                    <label for="provider"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        Provider
                    </label>
                    <input id="provider"
                           type="text"
                           value="SMTP"
                           disabled
                           class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-sm text-slate-600">
                    <p class="mt-1 text-xs text-slate-500">
                        SMS providers will be configured separately in a future phase.
                    </p>
                </div>

                <div>
                    <label for="host"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        SMTP host
                    </label>
                    <input id="host"
                           name="host"
                           type="text"
                           required
                           maxlength="255"
                           value="{{ old('host', $settings->host ?? 'smtp.gmail.com') }}"
                           placeholder="smtp.gmail.com"
                           autocomplete="off"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>

                <div>
                    <label for="port"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        SMTP port
                    </label>
                    <input id="port"
                           name="port"
                           type="number"
                           min="1"
                           max="65535"
                           required
                           value="{{ old('port', $settings->port ?? 587) }}"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>

                <div>
                    <label for="encryption"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        Encryption
                    </label>
                    <select id="encryption"
                            name="encryption"
                            required
                            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="tls"
                            @selected(old('encryption', $settings->encryption ?? 'tls') === 'tls')>
                            TLS / STARTTLS — port 587
                        </option>
                        <option value="ssl"
                            @selected(old('encryption', $settings->encryption ?? 'tls') === 'ssl')>
                            SSL / implicit TLS — port 465
                        </option>
                        <option value="none"
                            @selected(old('encryption', $settings->encryption ?? 'tls') === 'none')>
                            None — provider must support it
                        </option>
                    </select>
                </div>

                <div>
                    <label for="username"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        SMTP username
                    </label>
                    <input id="username"
                           name="username"
                           type="text"
                           required
                           maxlength="255"
                           value="{{ old('username', $settings->username ?? '') }}"
                             placeholder="your@example.com"
                           autocomplete="off"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>

                <div>
                    <label for="password"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        SMTP password / app password
                    </label>
                    <input id="password"
                           name="password"
                           type="password"
                           maxlength="2000"
                           autocomplete="new-password"
                           placeholder="{{ $settings ? 'Leave blank to keep saved password' : 'Enter SMTP password' }}"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <p class="mt-1 text-xs text-slate-500">
                        Never enter your regular Google account password.
                        Use a provider-approved credential.
                    </p>
                </div>

                <div>
                    <label for="from_address"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        Sender email address
                    </label>
                    <input id="from_address"
                           name="from_address"
                           type="email"
                           required
                           maxlength="255"
                           value="{{ old('from_address', $settings->from_address ?? '') }}"
                           placeholder="notifications@example.com"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>

                <div>
                    <label for="from_name"
                           class="mb-1.5 block text-sm font-medium text-slate-700">
                        Sender display name
                    </label>
                    <input id="from_name"
                           name="from_name"
                           type="text"
                           required
                           maxlength="255"
                           value="{{ old('from_name', $settings->from_name ?? 'iSWD - Sagay Water District') }}"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                        <input type="checkbox"
                               name="is_enabled"
                               value="1"
                               @checked(old('is_enabled', $settings ? $settings->is_enabled : true))
                               class="mt-1 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">

                        <span>
                            <span class="block text-sm font-semibold text-slate-900">
                                Enable database SMTP settings
                            </span>
                            <span class="mt-1 block text-sm text-slate-500">
                                When enabled, the saved settings take precedence
                                over the SMTP configuration in .env.
                            </span>
                        </span>
                    </label>
                </div>

            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="text-xs leading-5 text-slate-500">
                    Save the settings first, then send a test email.
                </p>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Save SMTP Settings
                </button>
            </div>
        </form>

        <form method="POST"
              action="{{ route('admin.settings.mail.test') }}"
              class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            @csrf

            <h2 class="text-lg font-semibold text-slate-900">
                Send a test email
            </h2>
            <p class="mt-1 text-sm leading-6 text-slate-600">
                Save and enable your SMTP settings first. Then send a test
                message to an inbox you can access.
            </p>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <div class="flex-1">
                    <label for="test_email"
                           class="sr-only">
                        Test recipient email address
                    </label>
                    <input id="test_email"
                           name="test_email"
                           type="email"
                           required
                           maxlength="255"
                           value="{{ old('test_email') }}"
                           placeholder="your-test-inbox@example.com"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>

                <button type="submit"
                        @disabled(!$settings || !$settings->is_enabled)
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Send Test Email
                </button>
            </div>
        </form>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
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

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
            <strong>Security reminder:</strong>
            Access to this page should remain limited to Administrators.
            Keep your Laravel APP_KEY stable until any required key rotation
            has been planned safely. Database-encrypted passwords cannot be
            decrypted if the key used to encrypt them is lost.
        </div>

    </div>
</div>
@endsection
