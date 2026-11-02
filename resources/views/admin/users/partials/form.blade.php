@php
    $user = $user ?? null;

    $selectedRole = old('role', $user && $user->roles->isNotEmpty() ? $user->roles->first()->name : '');

    $currentAvatar = optional($user)->avatar ? asset('storage/' . $user->avatar) : null;
@endphp

<div class="space-y-8">

    {{-- EMPLOYEE INFORMATION --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-5 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                    <i class="fa-solid fa-user"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-900">Employee Information</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Enter the employee's personal and identification information.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">

            <x-form.input label="Employee ID" name="employee_id" :value="old('employee_id', optional($user)->employee_id)" required />

            <x-form.input label="First Name" name="first_name" :value="old('first_name', optional($user)->first_name)" required />

            <x-form.input label="Middle Name" name="middle_name" :value="old('middle_name', optional($user)->middle_name)" />

            <x-form.input label="Last Name" name="last_name" :value="old('last_name', optional($user)->last_name)" required />

            <x-form.input label="Suffix" name="suffix" :value="old('suffix', optional($user)->suffix)" />

        </div>
    </section>


    {{-- CONTACT INFORMATION --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-5 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <i class="fa-solid fa-address-book"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-900">Contact Information</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Employee contact information used for their system account.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">

            <x-form.input label="Email Address" type="email" name="email" :value="old('email', optional($user)->email)" placeholder="employee@example.com" required />

            {{-- PHONE NUMBER --}}
            <div>
                <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700">
                    Phone Number
                    <span class="text-xs font-normal text-slate-400">(Optional)</span>
                </label>

                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i class="fa-solid fa-phone"></i>
                    </div>

                    <input id="phone" name="phone" type="tel" inputmode="numeric" autocomplete="tel-national"
                        maxlength="11" minlength="11" pattern="09[0-9]{9}"
                        value="{{ old('phone', optional($user)->phone) }}" placeholder="09XXXXXXXXX"
                        aria-describedby="phone-help phone-error phone-counter"
                        class="block w-full rounded-xl border bg-white py-3 pl-10 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-100 {{ $errors->has('phone') ? 'border-red-400' : 'border-slate-300' }}">

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                        <i id="phone-status-icon" class="fa-solid hidden text-emerald-500"></i>
                    </div>
                </div>

                <div class="mt-2 flex items-center justify-between gap-2">
                    <p id="phone-help" class="text-xs text-slate-500">
                        Enter 11 digits beginning with 09.
                    </p>
                    <span id="phone-counter" class="shrink-0 text-xs font-medium text-slate-400">
                        0/11
                    </span>
                </div>

                <p id="phone-client-error" class="mt-1.5 hidden text-xs font-medium text-red-600"></p>

                @error('phone')
                    <p id="phone-error" class="mt-1.5 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </section>


    {{-- ORGANIZATION & ACCESS --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-5 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700">
                    <i class="fa-solid fa-building-user"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-900">Organization & Access</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Assign the employee's department, position, and system role.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6 lg:grid-cols-3">

            <x-form.select label="Department" name="department_id" :options="$departments" :selected="old('department_id', optional($user)->department_id)" required />

            <x-form.select label="Position" name="position_id" :options="$positions" :selected="old('position_id', optional($user)->position_id)" required />

            <x-form.select label="Employee Role" name="role" :options="$roles" :selected="$selectedRole" required />

        </div>
    </section>


    {{-- PROFILE PICTURE --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-5 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <i class="fa-solid fa-camera"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-900">Profile Picture</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Upload an optional employee profile photo.
                    </p>
                </div>
            </div>
        </div>

        <div class="p-5 sm:p-6">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                {{-- IMAGE PREVIEW --}}
                <div class="relative mx-auto shrink-0 sm:mx-0">
                    <div
                        class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-2xl border-2 border-slate-200 bg-slate-100 shadow-sm">
                        <img id="avatar-preview" src="{{ $currentAvatar ?? '' }}" alt="Employee profile picture preview"
                            class="{{ $currentAvatar ? 'block' : 'hidden' }} h-full w-full object-cover">

                        <div id="avatar-placeholder"
                            class="{{ $currentAvatar ? 'hidden' : 'flex' }} h-full w-full items-center justify-center text-slate-400">
                            <i class="fa-solid fa-user text-4xl"></i>
                        </div>
                    </div>

                    <span
                        class="absolute -bottom-2 -right-2 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-sky-600 text-white shadow-sm">
                        <i class="fa-solid fa-camera text-xs"></i>
                    </span>
                </div>

                {{-- UPLOAD CONTROL --}}
                <div class="min-w-0 flex-1">
                    <label for="avatar" class="mb-2 block text-sm font-semibold text-slate-700">
                        Choose Profile Picture
                    </label>

                    <input id="avatar" type="file" name="avatar"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="block w-full rounded-xl border border-slate-300 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-sky-50 file:px-4 file:py-3 file:font-semibold file:text-sky-700 hover:file:bg-sky-100">

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Accepted formats: JPG, JPEG, PNG, or WEBP. Maximum file size: 2 MB.
                    </p>

                    <p id="avatar-filename" class="mt-2 break-all text-xs font-medium text-sky-700">
                        {{ $currentAvatar ? 'Current profile picture' : 'No image selected.' }}
                    </p>

                    <p id="avatar-error" class="mt-1.5 hidden text-sm text-red-600"></p>

                    <button type="button" id="remove-avatar"
                        class="mt-3 hidden items-center gap-2 rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                        <i class="fa-solid fa-trash-can"></i>
                        Remove Selected Image
                    </button>
                </div>
            </div>
        </div>
    </section>

</div>


{{-- PHONE VALIDATION AND AVATAR PREVIEW --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ==========================================
        // PHONE NUMBER VALIDATION
        // ==========================================

        const phoneInput = document.getElementById('phone');
        const phoneCounter = document.getElementById('phone-counter');
        const phoneStatusIcon = document.getElementById('phone-status-icon');
        const phoneClientError = document.getElementById('phone-client-error');

        function validatePhone() {
            if (!phoneInput) return true;

            // Remove all non-numeric characters.
            phoneInput.value = phoneInput.value.replace(/\D/g, '').slice(0, 11);

            const value = phoneInput.value;
            const isEmpty = value.length === 0;
            const isValid = /^09[0-9]{9}$/.test(value);

            if (phoneCounter) {
                phoneCounter.textContent = value.length + '/11';
                phoneCounter.classList.toggle('text-emerald-600', isValid);
                phoneCounter.classList.toggle('text-red-500', !isEmpty && !isValid);
                phoneCounter.classList.toggle('text-slate-400', isEmpty);
            }

            if (phoneStatusIcon) {
                phoneStatusIcon.classList.toggle('hidden', !isValid);
            }

            if (phoneClientError) {
                let message = '';

                if (!isEmpty && !value.startsWith('09')) {
                    message = 'Phone number must start with 09.';
                } else if (!isEmpty && value.length !== 11) {
                    message = 'Phone number must contain exactly 11 digits.';
                }

                phoneClientError.textContent = message;
                phoneClientError.classList.toggle('hidden', !message);
            }

            phoneInput.classList.toggle('border-red-400', !isEmpty && !isValid);
            phoneInput.classList.toggle('border-slate-300', isEmpty);
            phoneInput.classList.toggle('border-emerald-400', isValid);

            return isEmpty || isValid;
        }

        if (phoneInput) {
            phoneInput.addEventListener('input', validatePhone);
            phoneInput.addEventListener('blur', validatePhone);

            validatePhone();
        }


        // ==========================================
        // PROFILE PICTURE LIVE PREVIEW
        // ==========================================

        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatar-preview');
        const avatarPlaceholder = document.getElementById('avatar-placeholder');
        const avatarFilename = document.getElementById('avatar-filename');
        const avatarError = document.getElementById('avatar-error');
        const removeAvatarButton = document.getElementById('remove-avatar');

        let previewUrl = null;

        function showAvatarError(message) {
            if (!avatarError) return;

            avatarError.textContent = message;
            avatarError.classList.remove('hidden');
        }

        function clearAvatarError() {
            if (!avatarError) return;

            avatarError.textContent = '';
            avatarError.classList.add('hidden');
        }

        function releasePreviewUrl() {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }
        }

        if (avatarInput) {
            avatarInput.addEventListener('change', function() {
                clearAvatarError();

                const file = this.files[0];

                if (!file) return;

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (!allowedTypes.includes(file.type)) {
                    this.value = '';
                    showAvatarError('Please select a JPG, PNG, or WEBP image.');
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    this.value = '';
                    showAvatarError('Image size must not exceed 2 MB.');
                    return;
                }

                releasePreviewUrl();

                previewUrl = URL.createObjectURL(file);

                avatarPreview.src = previewUrl;
                avatarPreview.classList.remove('hidden');
                avatarPlaceholder.classList.add('hidden');

                avatarFilename.textContent = file.name;

                removeAvatarButton.classList.remove('hidden');
                removeAvatarButton.classList.add('inline-flex');
            });

            removeAvatarButton.addEventListener('click', function() {
                avatarInput.value = '';

                releasePreviewUrl();

                // Restore the existing image when editing.
                @if ($currentAvatar)
                    avatarPreview.src = @json($currentAvatar);
                    avatarPreview.classList.remove('hidden');
                    avatarPlaceholder.classList.add('hidden');
                    avatarFilename.textContent = 'Current profile picture';
                @else
                    avatarPreview.removeAttribute('src');
                    avatarPreview.classList.add('hidden');
                    avatarPlaceholder.classList.remove('hidden');
                    avatarFilename.textContent = 'No image selected.';
                @endif

                removeAvatarButton.classList.add('hidden');
                removeAvatarButton.classList.remove('inline-flex');

                clearAvatarError();
            });
        }

        // ==========================================
        // PREVENT INVALID PHONE SUBMISSION
        // ==========================================

        const form = phoneInput ? phoneInput.closest('form') : null;

        if (form) {
            form.addEventListener('submit', function(event) {
                if (!validatePhone()) {
                    event.preventDefault();

                    phoneInput.focus();
                    phoneInput.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });
        }

    });
</script>
