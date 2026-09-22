@php
    $consumer = $consumer ?? null;
    $isEdit = $consumer !== null;
@endphp

<div class="space-y-8">

    {{-- ========================================================= --}}
    {{-- CONSUMER INFORMATION --}}
    {{-- ========================================================= --}}

    <div>

        <div class="mb-5">

            <h3 class="text-lg font-semibold text-gray-900">
                Consumer Information
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Enter the consumer's account and personal information.
            </p>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- Account Number --}}
            <div>

                <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">
                    Account Number
                    <span class="text-red-500">*</span>
                </label>

                <input type="text" name="account_number" id="account_number"
                    value="{{ old('account_number', $consumer?->account_number ?? '') }}"
                    placeholder="Enter SWD account number"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('account_number')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- First Name --}}
            <div>

                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                    First Name
                    <span class="text-red-500">*</span>
                </label>

                <input type="text" name="first_name" id="first_name"
                    value="{{ old('first_name', $consumer?->first_name ?? '') }}"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('first_name')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Middle Name --}}
            <div>

                <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Middle Name
                </label>

                <input type="text" name="middle_name" id="middle_name"
                    value="{{ old('middle_name', $consumer?->middle_name ?? '') }}"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                @error('middle_name')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Last Name --}}
            <div>

                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Last Name
                    <span class="text-red-500">*</span>
                </label>

                <input type="text" name="last_name" id="last_name"
                    value="{{ old('last_name', $consumer?->last_name ?? '') }}"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('last_name')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Suffix --}}
            <div>

                <label for="suffix" class="block text-sm font-medium text-gray-700 mb-1">
                    Suffix
                </label>

                <input type="text" name="suffix" id="suffix"
                    value="{{ old('suffix', $consumer?->suffix ?? '') }}" placeholder="Jr., Sr., III"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                @error('suffix')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Sex --}}
            <div>

                <label for="sex" class="block text-sm font-medium text-gray-700 mb-1">
                    Sex
                    <span class="text-red-500">*</span>
                </label>

                <select name="sex" id="sex"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                    <option value="">
                        Select Sex
                    </option>

                    <option value="Male" @selected(old('sex', $consumer?->sex ?? '') === 'Male')>
                        Male
                    </option>

                    <option value="Female" @selected(old('sex', $consumer?->sex ?? '') === 'Female')>
                        Female
                    </option>

                </select>

                @error('sex')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Phone --}}
            <div>

                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Contact Number
                    <span class="text-red-500">*</span>
                </label>

                <input type="text" name="phone" id="phone" value="{{ old('phone', $consumer?->phone ?? '') }}"
                    placeholder="09XXXXXXXXX"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('phone')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Email --}}
            <div>

                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address
                    <span class="text-red-500">*</span>
                </label>

                <input type="email" name="email" id="email" value="{{ old('email', $consumer?->email ?? '') }}"
                    placeholder="consumer@email.com"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                <p class="text-xs text-gray-500 mt-1">
                    This email will be used to log in to the consumer portal.
                </p>

                @error('email')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ACCOUNT STATUS - EDIT ONLY --}}
    {{-- ========================================================= --}}

    @if ($isEdit)
        <div class="border-t border-gray-200 pt-8">

            <div class="mb-5">

                <h3 class="text-lg font-semibold text-gray-900">
                    Online Account Status
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Activate or deactivate this consumer's access to the iSWD online portal.
                </p>

            </div>


            @php
                $currentStatus = (string) old('is_active', $consumer->is_active ? '1' : '0');
            @endphp


            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">

                {{-- Active --}}
                <label
                    class="flex items-center gap-4 p-5 rounded-xl
                           border cursor-pointer transition
                           {{ $currentStatus === '1' ? 'border-green-400 bg-green-50' : 'border-gray-200 bg-white hover:bg-gray-50' }}">

                    <input type="radio" name="is_active" value="1" @checked($currentStatus === '1')
                        class="text-green-600 focus:ring-green-500" required>

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-green-100 text-green-600
                               flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div>

                        <p class="font-semibold text-gray-900">
                            Active
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            Consumer can log in and submit online complaints.
                        </p>

                    </div>

                </label>


                {{-- Inactive --}}
                <label
                    class="flex items-center gap-4 p-5 rounded-xl
                           border cursor-pointer transition
                           {{ $currentStatus === '0' ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white hover:bg-gray-50' }}">

                    <input type="radio" name="is_active" value="0" @checked($currentStatus === '0')
                        class="text-red-600 focus:ring-red-500" required>

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-red-100 text-red-600
                               flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>

                    <div>

                        <p class="font-semibold text-gray-900">
                            Inactive
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            Consumer cannot access the online portal.
                        </p>

                    </div>

                </label>

            </div>


            @error('is_active')
                <p class="text-sm text-red-600 mt-2">
                    {{ $message }}
                </p>
            @enderror

        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- RESIDENTIAL ADDRESS --}}
    {{-- ========================================================= --}}

    <div class="border-t border-gray-200 pt-8">

        <div class="mb-5">

            <h3 class="text-lg font-semibold text-gray-900">
                Residential Address
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Enter the consumer's current residential address.
            </p>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- House Number --}}
            <div>

                <label for="house_no" class="block text-sm font-medium text-gray-700 mb-1">
                    House No.
                </label>

                <input type="text" name="house_no" id="house_no"
                    value="{{ old('house_no', $consumer?->address?->house_no ?? '') }}"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                @error('house_no')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Street --}}
            <div>

                <label for="street" class="block text-sm font-medium text-gray-700 mb-1">
                    Street
                </label>

                <input type="text" name="street" id="street"
                    value="{{ old('street', $consumer?->address?->street ?? '') }}"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                @error('street')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Purok --}}
            <div>

                <label for="purok" class="block text-sm font-medium text-gray-700 mb-1">
                    Purok
                </label>

                <input type="text" name="purok" id="purok"
                    value="{{ old('purok', $consumer?->address?->purok ?? '') }}"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                @error('purok')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Barangay --}}
            <div>

                <label for="barangay" class="block text-sm font-medium text-gray-700 mb-1">
                    Barangay
                    <span class="text-red-500">*</span>
                </label>

                <input type="text" name="barangay" id="barangay"
                    value="{{ old('barangay', $consumer?->address?->barangay ?? '') }}"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('barangay')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Municipality --}}
            <div>

                <label for="municipality" class="block text-sm font-medium text-gray-700 mb-1">
                    Municipality
                    <span class="text-red-500">*</span>
                </label>

                <input type="text" name="municipality" id="municipality"
                    value="{{ old('municipality', $consumer?->address?->municipality ?? 'Sagay') }}"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('municipality')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Province --}}
            <div>

                <label for="province" class="block text-sm font-medium text-gray-700 mb-1">
                    Province
                    <span class="text-red-500">*</span>
                </label>

                <input type="text" name="province" id="province"
                    value="{{ old('province', $consumer?->address?->province ?? 'Negros Occidental') }}"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('province')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- ZIP Code --}}
            <div>

                <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-1">
                    ZIP Code
                </label>

                <input type="text" name="zip_code" id="zip_code"
                    value="{{ old('zip_code', $consumer?->address?->zip_code ?? '') }}" placeholder="6122"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                @error('zip_code')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PORTAL ACCOUNT --}}
    {{-- ========================================================= --}}

    <div class="border-t border-gray-200 pt-8">

        <div class="mb-5">

            <h3 class="text-lg font-semibold text-gray-900">
                Consumer Portal Account
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Manage the consumer's iSWD online portal access.
            </p>

        </div>


        {{-- CREATE --}}
        @if (!$isEdit)
            <div class="rounded-xl border border-blue-200
                       bg-blue-50 p-5">

                <div class="flex items-start gap-4">

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-blue-100 text-blue-600
                               flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-lock"></i>
                    </div>


                    <div>

                        <p class="font-semibold text-blue-900">
                            Online Account Will Be Created Automatically
                        </p>

                        <p class="text-sm text-blue-700 mt-1">
                            Registering this consumer will automatically create an active iSWD portal account.
                        </p>

                        <p class="text-sm text-blue-700 mt-2">
                            A secure temporary password will be generated and shown once after registration.
                        </p>

                        <p class="text-sm text-blue-700 mt-2">
                            The consumer will use the email address above and the temporary password to log in.
                        </p>

                    </div>

                </div>

            </div>


            {{-- EDIT WITH USER --}}
        @elseif ($consumer->user)
            <div class="space-y-5">

                {{-- Existing Account --}}
                <div
                    class="rounded-xl border
                           {{ $consumer->user->is_active ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}
                           p-5">

                    <div class="flex items-start gap-4">

                        <div
                            class="w-11 h-11 rounded-xl
                                   {{ $consumer->user->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}
                                   flex items-center justify-center shrink-0">

                            <i
                                class="fa-solid
                                       {{ $consumer->user->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>

                        </div>


                        <div>

                            <p
                                class="font-semibold
                                       {{ $consumer->user->is_active ? 'text-green-900' : 'text-red-900' }}">
                                {{ $consumer->user->is_active ? 'Portal Account Active' : 'Portal Account Inactive' }}
                            </p>

                            <p
                                class="text-sm mt-1
                                       {{ $consumer->user->is_active ? 'text-green-700' : 'text-red-700' }}">
                                Login email:
                                <span class="font-medium">
                                    {{ $consumer->user->email }}
                                </span>
                            </p>

                        </div>

                    </div>

                </div>


                {{-- Password Reset --}}
                <div class="rounded-xl border border-gray-200 bg-white p-5">

                    <div class="flex items-start gap-4 mb-5">

                        <div
                            class="w-11 h-11 rounded-xl
                                   bg-amber-100 text-amber-600
                                   flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-key"></i>
                        </div>


                        <div>

                            <h4 class="font-semibold text-gray-900">
                                Reset Portal Password
                            </h4>

                            <p class="text-sm text-gray-500 mt-1">
                                Leave these fields blank if the current password should remain unchanged.
                            </p>

                        </div>

                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- New Password --}}
                        <div>

                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                                New Password
                            </label>

                            <div class="relative">

                                <input type="password" name="new_password" id="new_password"
                                    autocomplete="new-password" placeholder="Enter new password"
                                    class="w-full rounded-lg border-gray-300
                                           pr-11 focus:border-blue-500
                                           focus:ring-blue-500">

                                <button type="button" onclick="toggleConsumerPassword('new_password', this)"
                                    class="absolute right-3 top-1/2
                                           -translate-y-1/2
                                           text-gray-400 hover:text-gray-600"
                                    tabindex="-1">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                Minimum 8 characters with uppercase, lowercase and number.
                            </p>

                            @error('new_password')
                                <p class="text-sm text-red-600 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Confirm Password --}}
                        <div>

                            <label for="new_password_confirmation"
                                class="block text-sm font-medium text-gray-700 mb-1">
                                Confirm New Password
                            </label>

                            <div class="relative">

                                <input type="password" name="new_password_confirmation"
                                    id="new_password_confirmation" autocomplete="new-password"
                                    placeholder="Confirm new password"
                                    class="w-full rounded-lg border-gray-300
                                           pr-11 focus:border-blue-500
                                           focus:ring-blue-500">

                                <button type="button"
                                    onclick="toggleConsumerPassword(
                                        'new_password_confirmation',
                                        this
                                    )"
                                    class="absolute right-3 top-1/2
                                           -translate-y-1/2
                                           text-gray-400 hover:text-gray-600"
                                    tabindex="-1">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- LEGACY CONSUMER WITHOUT USER --}}
        @else
            <div class="rounded-xl border border-amber-200
                       bg-amber-50 p-5">

                <div class="flex items-start gap-4">

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-amber-100 text-amber-600
                               flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>

                    <div>

                        <p class="font-semibold text-amber-900">
                            Portal Account Missing
                        </p>

                        <p class="text-sm text-amber-700 mt-1">
                            This is an existing consumer record without a linked User account.
                        </p>

                    </div>

                </div>

            </div>
        @endif

    </div>


    {{-- ========================================================= --}}
    {{-- ACTIONS --}}
    {{-- ========================================================= --}}

    <div
        class="border-t border-gray-200 pt-6
               flex flex-col-reverse sm:flex-row
               sm:items-center sm:justify-end gap-3">

        <a href="{{ route('customer-service.consumers.index') }}"
            class="inline-flex items-center justify-center
                   px-5 py-2.5 rounded-lg
                   border border-gray-300
                   text-gray-700 hover:bg-gray-50">
            Cancel
        </a>


        <button type="submit"
            class="inline-flex items-center justify-center gap-2
                   px-5 py-2.5 rounded-lg
                   bg-blue-600 text-white font-medium
                   hover:bg-blue-700 transition">

            <i class="fa-solid fa-floppy-disk"></i>

            {{ $isEdit ? 'Update Consumer' : 'Register Consumer' }}

        </button>

    </div>

</div>


@if ($isEdit && $consumer->user)
    <script>
        function toggleConsumerPassword(inputId, button) {

            const input =
                document.getElementById(inputId);

            const icon =
                button.querySelector('i');

            if (!input) {
                return;
            }

            if (input.type === 'password') {

                input.type = 'text';

                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');

            } else {

                input.type = 'password';

                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');

            }

        }
    </script>
@endif
