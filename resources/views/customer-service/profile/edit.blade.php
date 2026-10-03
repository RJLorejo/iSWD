@extends($layout)

@section('title', 'Edit Profile')

@section('content')

    <div class="space-y-5">

        @if ($errors->any())

            <div class="rounded-xl border border-red-200 bg-red-50 p-4">

                <div class="flex items-start gap-3">

                    <div
                        class="w-9 h-9 rounded-lg
                               bg-red-100 flex items-center
                               justify-center shrink-0">

                        <i class="fa-solid fa-circle-exclamation text-red-600"></i>

                    </div>

                    <div>

                        <h3 class="font-semibold text-red-800">
                            Please correct the following errors:
                        </h3>

                        <ul class="mt-1.5 text-sm text-red-700 list-disc list-inside space-y-0.5">

                            @foreach ($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif

        <x-form.page-header
            title="Edit Profile"
            subtitle="Update your personal information, profile picture, and account password." />

        <x-form.card>

            <form
                method="POST"
                action="{{ route('profile.update') }}"
                enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="space-y-5">

                    <div
                        x-data="{
                            preview: '{{ $user->avatar_url }}',

                            previewImage(event) {
                                const file = event.target.files[0];

                                if (!file || !file.type.startsWith('image/')) {
                                    return;
                                }

                                const reader = new FileReader();

                                reader.onload = (event) => {
                                    this.preview = event.target.result;
                                };

                                reader.readAsDataURL(file);
                            }
                        }">

                        <div class="mb-3">

                            <h3 class="text-base font-semibold text-gray-900">
                                Profile Picture
                            </h3>

                            <p class="text-sm text-gray-500 mt-0.5">
                                Update the photo displayed on your employee account.
                            </p>

                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                            <img
                                :src="preview"
                                src="{{ $user->avatar_url }}"
                                alt="{{ $user->full_name }}"
                                class="w-16 h-16 rounded-xl object-cover
                                       border border-gray-200 bg-gray-50 shrink-0">

                            <div>

                                <label
                                    for="avatar"
                                    class="inline-flex items-center justify-center gap-2
                                           px-4 py-2 rounded-lg
                                           border border-gray-300
                                           bg-white text-sm font-medium
                                           text-gray-700
                                           hover:bg-gray-50
                                           cursor-pointer transition">

                                    <i class="fa-solid fa-camera text-gray-500"></i>

                                    Choose Photo

                                </label>

                                <input
                                    type="file"
                                    name="avatar"
                                    id="avatar"
                                    accept="image/jpeg,image/png,image/webp"
                                    @change="previewImage($event)"
                                    class="hidden">

                                <p class="text-xs text-gray-500 mt-1">
                                    JPG, PNG or WEBP. Maximum 2 MB.
                                </p>

                                @error('avatar')

                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        </div>

                    </div>

                    <div class="border-t border-gray-200 pt-5">

                        <div class="mb-3">

                            <h3 class="text-base font-semibold text-gray-900">
                                Personal Information
                            </h3>

                            <p class="text-sm text-gray-500 mt-0.5">
                                Update your name and personal information.
                            </p>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

                            <div>

                                <label
                                    for="first_name"
                                    class="block text-sm font-medium text-gray-700 mb-1">

                                    First Name
                                    <span class="text-red-500">*</span>

                                </label>

                                <input
                                    type="text"
                                    name="first_name"
                                    id="first_name"
                                    value="{{ old('first_name', $user->first_name) }}"
                                    autocomplete="given-name"
                                    placeholder="First name"
                                    class="w-full rounded-lg border-gray-300
                                           focus:border-blue-500 focus:ring-blue-500"
                                    required>

                                @error('first_name')

                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            <div>

                                <label
                                    for="middle_name"
                                    class="block text-sm font-medium text-gray-700 mb-1">
                                    Middle Name
                                </label>

                                <input
                                    type="text"
                                    name="middle_name"
                                    id="middle_name"
                                    value="{{ old('middle_name', $user->middle_name) }}"
                                    autocomplete="additional-name"
                                    placeholder="Middle name"
                                    class="w-full rounded-lg border-gray-300
                                           focus:border-blue-500 focus:ring-blue-500">

                                @error('middle_name')

                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            <div>

                                <label
                                    for="last_name"
                                    class="block text-sm font-medium text-gray-700 mb-1">

                                    Last Name
                                    <span class="text-red-500">*</span>

                                </label>

                                <input
                                    type="text"
                                    name="last_name"
                                    id="last_name"
                                    value="{{ old('last_name', $user->last_name) }}"
                                    autocomplete="family-name"
                                    placeholder="Last name"
                                    class="w-full rounded-lg border-gray-300
                                           focus:border-blue-500 focus:ring-blue-500"
                                    required>

                                @error('last_name')

                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            <div>

                                <label
                                    for="suffix"
                                    class="block text-sm font-medium text-gray-700 mb-1">
                                    Suffix
                                </label>

                                <input
                                    type="text"
                                    name="suffix"
                                    id="suffix"
                                    value="{{ old('suffix', $user->suffix) }}"
                                    placeholder="Jr., Sr., III"
                                    class="w-full rounded-lg border-gray-300
                                           focus:border-blue-500 focus:ring-blue-500">

                                @error('suffix')

                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        </div>

                    </div>

                    <div class="border-t border-gray-200 pt-5">

                        <div class="mb-3">

                            <h3 class="text-base font-semibold text-gray-900">
                                Contact Information
                            </h3>

                            <p class="text-sm text-gray-500 mt-0.5">
                                Manage the contact details associated with your account.
                            </p>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>

                                <label
                                    for="email"
                                    class="block text-sm font-medium text-gray-700 mb-1">

                                    Email Address
                                    <span class="text-red-500">*</span>

                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="{{ old('email', $user->email) }}"
                                    autocomplete="email"
                                    placeholder="employee@email.com"
                                    class="w-full rounded-lg border-gray-300
                                           focus:border-blue-500 focus:ring-blue-500"
                                    required>

                                @error('email')

                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            <div>

                                <label
                                    for="phone"
                                    class="block text-sm font-medium text-gray-700 mb-1">
                                    Contact Number
                                </label>

                                <input
                                    type="tel"
                                    name="phone"
                                    id="phone"
                                    value="{{ old('phone', $user->phone) }}"
                                    autocomplete="tel"
                                    inputmode="tel"
                                    placeholder="09XXXXXXXXX"
                                    class="w-full rounded-lg border-gray-300
                                           focus:border-blue-500 focus:ring-blue-500">

                                @error('phone')

                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        </div>

                    </div>

                    <div
                        class="border-t border-gray-200 pt-5"
                        x-data="{
                            showCurrent: false,
                            showPassword: false,
                            showConfirmation: false
                        }">

                        <div class="mb-3">

                            <h3 class="text-base font-semibold text-gray-900">
                                Change Password
                            </h3>

                            <p class="text-sm text-gray-500 mt-0.5">
                                Leave all password fields blank if you do not want to change your password.
                            </p>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            <div>

                                <label
                                    for="current_password"
                                    class="block text-sm font-medium text-gray-700 mb-1">
                                    Current Password
                                </label>

                                <div class="relative">

                                    <input
                                        :type="showCurrent ? 'text' : 'password'"
                                        name="current_password"
                                        id="current_password"
                                        autocomplete="current-password"
                                        placeholder="Current password"
                                        class="w-full rounded-lg border-gray-300
                                               pr-11 focus:border-blue-500
                                               focus:ring-blue-500">

                                    <button
                                        type="button"
                                        @click="showCurrent = !showCurrent"
                                        class="absolute right-3 top-1/2
                                               -translate-y-1/2
                                               text-gray-400 hover:text-gray-600"
                                        tabindex="-1">

                                        <i
                                            class="fa-solid"
                                            :class="showCurrent ? 'fa-eye-slash' : 'fa-eye'">
                                        </i>

                                    </button>

                                </div>

                                @error('current_password')

                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            <div>

                                <label
                                    for="password"
                                    class="block text-sm font-medium text-gray-700 mb-1">
                                    New Password
                                </label>

                                <div class="relative">

                                    <input
                                        :type="showPassword ? 'text' : 'password'"
                                        name="password"
                                        id="password"
                                        autocomplete="new-password"
                                        placeholder="New password"
                                        class="w-full rounded-lg border-gray-300
                                               pr-11 focus:border-blue-500
                                               focus:ring-blue-500">

                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2
                                               -translate-y-1/2
                                               text-gray-400 hover:text-gray-600"
                                        tabindex="-1">

                                        <i
                                            class="fa-solid"
                                            :class="showPassword ? 'fa-eye-slash' : 'fa-eye'">
                                        </i>

                                    </button>

                                </div>

                                @error('password')

                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            <div>

                                <label
                                    for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirm New Password
                                </label>

                                <div class="relative">

                                    <input
                                        :type="showConfirmation ? 'text' : 'password'"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        autocomplete="new-password"
                                        placeholder="Confirm new password"
                                        class="w-full rounded-lg border-gray-300
                                               pr-11 focus:border-blue-500
                                               focus:ring-blue-500">

                                    <button
                                        type="button"
                                        @click="showConfirmation = !showConfirmation"
                                        class="absolute right-3 top-1/2
                                               -translate-y-1/2
                                               text-gray-400 hover:text-gray-600"
                                        tabindex="-1">

                                        <i
                                            class="fa-solid"
                                            :class="showConfirmation ? 'fa-eye-slash' : 'fa-eye'">
                                        </i>

                                    </button>

                                </div>

                            </div>

                        </div>

                        <p class="text-xs text-gray-500 mt-1.5">
                            Use at least 8 characters with uppercase, lowercase, and a number.
                        </p>

                    </div>

                    <div
                        class="border-t border-gray-200 pt-5
                               flex flex-col-reverse sm:flex-row
                               sm:items-center sm:justify-end gap-3">

                        <a
                            href="{{ route('profile.show') }}"
                            class="inline-flex items-center justify-center
                                   px-5 py-2.5 rounded-lg
                                   border border-gray-300
                                   text-gray-700
                                   hover:bg-gray-50 transition">

                            Cancel

                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2
                                   px-5 py-2.5 rounded-lg
                                   bg-blue-600 text-white font-medium
                                   hover:bg-blue-700 transition">

                            <i class="fa-solid fa-floppy-disk"></i>

                            Save Changes

                        </button>

                    </div>

                </div>

            </form>

        </x-form.card>

    </div>

@endsection
