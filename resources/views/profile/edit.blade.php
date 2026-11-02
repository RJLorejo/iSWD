@extends($layout)

@section('title', 'Edit Profile')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        {{-- PAGE HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Edit Profile</h1>
                <p class="mt-1 text-sm text-gray-500">Update your personal information and account security in one place.</p>
            </div>

            <a href="{{ route('profile.show') }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:w-auto">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                Back to Profile
            </a>
        </div>

        {{-- VALIDATION ERRORS --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 sm:p-5" role="alert"
                aria-labelledby="profile-errors-title">
                <div class="flex items-start gap-3">
                    <i class="fas fa-circle-exclamation mt-0.5 text-red-600" aria-hidden="true"></i>
                    <div class="min-w-0">
                        <p id="profile-errors-title" class="font-semibold text-red-800">Please correct the following errors:
                        </p>
                        <ul class="mt-2 list-inside list-disc space-y-1 break-words text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- PERSONAL INFORMATION --}}
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-5 sm:px-6">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </span>
                        <div>
                            <h2 class="font-semibold text-gray-900">Personal Information</h2>
                            <p class="mt-1 text-sm text-gray-500">Keep your contact details and profile photo up to date.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                    <div>
                        <label for="first_name" class="mb-2 block text-sm font-medium text-gray-700">First Name <span
                                class="text-red-500">*</span></label>
                        <input id="first_name" name="first_name" type="text"
                            value="{{ old('first_name', $user->first_name) }}" required maxlength="100"
                            autocomplete="given-name"
                            class="block w-full rounded-xl border px-3.5 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 {{ $errors->has('first_name') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('first_name')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="middle_name" class="mb-2 block text-sm font-medium text-gray-700">Middle Name <span
                                class="font-normal text-gray-400">(Optional)</span></label>
                        <input id="middle_name" name="middle_name" type="text"
                            value="{{ old('middle_name', $user->middle_name) }}" maxlength="100"
                            autocomplete="additional-name"
                            class="block w-full rounded-xl border border-gray-300 px-3.5 py-3 text-sm text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                        @error('middle_name')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="mb-2 block text-sm font-medium text-gray-700">Last Name <span
                                class="text-red-500">*</span></label>
                        <input id="last_name" name="last_name" type="text"
                            value="{{ old('last_name', $user->last_name) }}" required maxlength="100"
                            autocomplete="family-name"
                            class="block w-full rounded-xl border px-3.5 py-3 text-sm text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 {{ $errors->has('last_name') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('last_name')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="suffix" class="mb-2 block text-sm font-medium text-gray-700">Suffix <span
                                class="font-normal text-gray-400">(Optional)</span></label>
                        <input id="suffix" name="suffix" type="text" value="{{ old('suffix', $user->suffix) }}"
                            maxlength="20" placeholder="e.g. Jr., III"
                            class="block w-full rounded-xl border border-gray-300 px-3.5 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                        @error('suffix')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-gray-700">Email Address <span
                                class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                            required maxlength="255" autocomplete="email"
                            class="block w-full rounded-xl border px-3.5 py-3 text-sm text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700">
                            Phone Number
                            <span class="font-normal text-gray-400">(Optional)</span>
                        </label>

                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <i class="fas fa-mobile-screen-button text-gray-400" aria-hidden="true"></i>
                            </div>

                            <input id="phone" name="phone" type="tel" inputmode="numeric"
                                autocomplete="tel-national" maxlength="11" minlength="11" pattern="09[0-9]{9}"
                                placeholder="09XXXXXXXXX" value="{{ old('phone', $user->phone) }}"
                                aria-describedby="phone-help phone-error"
                                class="block w-full rounded-xl border bg-white py-3 pl-11 pr-12 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 {{ $errors->has('phone') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-gray-300' }}">

                            <div id="phone-status"
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                                <i class="fas fa-circle-check hidden text-emerald-500" aria-hidden="true"></i>
                            </div>
                        </div>

                        <p id="phone-help" class="mt-2 text-xs leading-5 text-gray-500">
                            Enter an 11-digit Philippine mobile number beginning with 09.
                            <span id="phone-counter" class="ml-1 font-medium text-gray-400">0/11 digits</span>
                        </p>

                        @error('phone')
                            <p id="phone-error" class="mt-1.5 text-sm font-medium text-red-600" role="alert">
                                <i class="fas fa-circle-exclamation mr-1" aria-hidden="true"></i>
                                {{ $message }}
                            </p>
                        @else
                            <p id="phone-error" class="mt-1.5 hidden text-sm font-medium text-red-600" role="alert"></p>
                        @enderror
                    </div>


                    {{-- AVATAR --}}
                    <div class="sm:col-span-2">
                        <label for="avatar" class="mb-2 block text-sm font-medium text-gray-700">Profile Picture <span
                                class="font-normal text-gray-400">(Optional)</span></label>
                        <div
                            class="flex flex-col gap-4 rounded-xl border border-dashed border-gray-300 p-4 sm:flex-row sm:items-center">
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&size=160&name=' . urlencode($user->full_name) }}"
                                alt="Current profile picture"
                                class="h-20 w-20 shrink-0 rounded-full border border-gray-200 object-cover">
                            <div class="min-w-0 flex-1">
                                <input id="avatar" name="avatar" type="file"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                    class="block w-full text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-sky-50 file:px-4 file:py-2 file:font-semibold file:text-sky-700 hover:file:bg-sky-100">
                                <p class="mt-2 text-xs leading-5 text-gray-500">JPG, PNG, or WEBP. Maximum file size: 2 MB.
                                </p>
                                @error('avatar')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- PASSWORD --}}
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-5 sm:px-6">
                    <div class="flex items-center gap-3">
                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                            <i class="fas fa-shield-halved" aria-hidden="true"></i>
                        </span>
                        <div>
                            <h2 class="font-semibold text-gray-900">Account Security</h2>
                            <p class="mt-1 text-sm text-gray-500">Leave these fields blank if you do not want to change
                                your password.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                    <div class="sm:col-span-2">
                        <label for="current_password" class="mb-2 block text-sm font-medium text-gray-700">Current
                            Password</label>
                        <div class="relative">
                            <input id="current_password" name="current_password" type="password"
                                autocomplete="current-password"
                                class="block w-full rounded-xl border px-3.5 py-3 pr-12 text-sm text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 {{ $errors->has('current_password') ? 'border-red-400' : 'border-gray-300' }}">
                            <button type="button" data-password-toggle="current_password"
                                aria-label="Show current password" aria-pressed="false"
                                class="absolute inset-y-0 right-0 inline-flex items-center px-4 text-gray-500 transition hover:text-sky-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-gray-700">New Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                class="block w-full rounded-xl border px-3.5 py-3 pr-12 text-sm text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}">
                            <button type="button" data-password-toggle="password" aria-label="Show new password"
                                aria-pressed="false"
                                class="absolute inset-y-0 right-0 inline-flex items-center px-4 text-gray-500 transition hover:text-sky-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700">Confirm
                            New Password</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password"
                                class="block w-full rounded-xl border border-gray-300 px-3.5 py-3 pr-12 text-sm text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                            <button type="button" data-password-toggle="password_confirmation"
                                aria-label="Show password confirmation" aria-pressed="false"
                                class="absolute inset-y-0 right-0 inline-flex items-center px-4 text-gray-500 transition hover:text-sky-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <p class="text-xs leading-5 text-gray-500 sm:col-span-2">
                        For security, changing your password requires your current password. Use at least 8 characters,
                        including uppercase and lowercase letters and a number.
                    </p>
                </div>
            </section>

            {{-- SINGLE SUBMIT ACTION --}}
            <div
                class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('profile.show') }}"
                    class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:w-auto">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:w-auto">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle visibility for each password field.
            document.querySelectorAll('[data-password-toggle]').forEach(function(button) {
                button.addEventListener('click', function() {
                    const input = document.getElementById(button.dataset.passwordToggle);
                    const icon = button.querySelector('i');
                    if (!input || !icon) return;

                    const showPassword = input.type === 'password';
                    input.type = showPassword ? 'text' : 'password';
                    icon.classList.toggle('fa-eye', !showPassword);
                    icon.classList.toggle('fa-eye-slash', showPassword);
                    button.setAttribute('aria-pressed', String(showPassword));
                    button.setAttribute('aria-label', (showPassword ? 'Hide ' : 'Show ') + input
                        .labels?.[0]?.textContent.trim().replace(/\s*\(.*?\)\s*$/, '')
                        .toLowerCase());
                });
            });


            // Philippine mobile number validation and input formatting.
            const phoneInput = document.getElementById('phone');

            if (phoneInput) {
                const counter = document.getElementById('phone-counter');
                const error = document.getElementById('phone-error');
                const statusIcon = document.querySelector('#phone-status i');

                function validatePhone() {
                    const value = phoneInput.value;
                    const valid = value === '' || /^09[0-9]{9}$/.test(value);

                    if (counter) {
                        counter.textContent = `${value.length}/11 digits`;
                        counter.classList.toggle('text-emerald-600', value.length === 11 && valid);
                        counter.classList.toggle('text-gray-400', value.length !== 11 || !valid);
                    }

                    if (statusIcon) {
                        statusIcon.classList.toggle('hidden', !(value.length === 11 && valid));
                    }

                    if (value !== '' && !valid) {
                        error.textContent = value.length < 11 ?
                            'Phone number must contain exactly 11 digits.' :
                            'Enter a valid Philippine mobile number beginning with 09.';
                        error.classList.remove('hidden');
                        phoneInput.setAttribute('aria-invalid', 'true');
                    } else {
                        error.textContent = '';
                        error.classList.add('hidden');
                        phoneInput.removeAttribute('aria-invalid');
                    }

                    return valid;
                }

                phoneInput.addEventListener('input', function() {
                    // Remove all non-numeric characters and limit to 11 digits.
                    this.value = this.value.replace(/\D/g, '').slice(0, 11);
                    validatePhone();
                });

                phoneInput.addEventListener('blur', validatePhone);

                // Prevent form submission when an entered number is invalid.
                phoneInput.form?.addEventListener('submit', function(event) {
                    if (!validatePhone()) {
                        event.preventDefault();
                        phoneInput.focus();
                    }
                });

                // Validate the existing saved value on page load.
                validatePhone();
            }

            // Ask for confirmation before sending the combined profile form.
            const profileForm = document.querySelector('form[action="{{ route('profile.update') }}"]');
            if (profileForm) {
                profileForm.addEventListener('submit', function(event) {
                    const hasPasswordChange = ['current_password', 'password', 'password_confirmation']
                        .some(function(name) {
                            const field = profileForm.querySelector('[name="' + name + '"]');
                            return field && field.value.trim() !== '';
                        });

                    const message = hasPasswordChange ?
                        'You are about to save your profile changes and update your password. Continue?' :
                        'Are you sure you want to save your profile changes?';

                    if (!window.confirm(message)) {
                        event.preventDefault();
                    }
                });
            }
        });
    </script>
@endsection
