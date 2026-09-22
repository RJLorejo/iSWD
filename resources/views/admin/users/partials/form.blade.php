@php

    $user = $user ?? null;

    $selectedRole = old('role', $user && $user->roles->isNotEmpty() ? $user->roles->first()->name : '');

@endphp


<div class="space-y-8">


    {{-- ========================================================= --}}
    {{-- EMPLOYEE INFORMATION --}}
    {{-- ========================================================= --}}

    <div>

        <div class="mb-5">

            <h3 class="text-lg font-bold text-slate-900">
                Employee Information
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Enter the employee's personal and identification information.
            </p>

        </div>


        <div class="grid md:grid-cols-2 gap-5">

            <x-form.input label="Employee ID" name="employee_id" :value="old('employee_id', optional($user)->employee_id)" required />


            <x-form.input label="First Name" name="first_name" :value="old('first_name', optional($user)->first_name)" required />


            <x-form.input label="Middle Name" name="middle_name" :value="old('middle_name', optional($user)->middle_name)" />


            <x-form.input label="Last Name" name="last_name" :value="old('last_name', optional($user)->last_name)" required />


            <x-form.input label="Suffix" name="suffix" :value="old('suffix', optional($user)->suffix)" />

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CONTACT INFORMATION --}}
    {{-- ========================================================= --}}

    <div class="border-t border-slate-200 pt-7">

        <div class="mb-5">

            <h3 class="text-lg font-bold text-slate-900">
                Contact Information
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Employee contact information used for their system account.
            </p>

        </div>


        <div class="grid md:grid-cols-2 gap-5">

            <x-form.input label="Email Address" type="email" name="email" :value="old('email', optional($user)->email)" required />


            <x-form.input label="Phone Number" name="phone" :value="old('phone', optional($user)->phone)" />

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ORGANIZATION & ACCESS --}}
    {{-- ========================================================= --}}

    <div class="border-t border-slate-200 pt-7">

        <div class="mb-5">

            <h3 class="text-lg font-bold text-slate-900">
                Organization & Access
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Assign the employee's department, position, and system role.
            </p>

        </div>


        <div class="grid md:grid-cols-3 gap-5">

            <x-form.select label="Department" name="department_id" :options="$departments" :selected="old('department_id', optional($user)->department_id)" required />


            <x-form.select label="Position" name="position_id" :options="$positions" :selected="old('position_id', optional($user)->position_id)" required />


            <x-form.select label="Employee Role" name="role" :options="$roles" :selected="$selectedRole" required />

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PROFILE PICTURE --}}
    {{-- ========================================================= --}}

    <div class="border-t border-slate-200 pt-7">

        <div class="mb-5">

            <h3 class="text-lg font-bold text-slate-900">
                Profile Picture
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Optional employee profile photo. Maximum file size is 2 MB.
            </p>

        </div>


        <div class="flex flex-col sm:flex-row gap-5 sm:items-center">


            {{-- Existing Avatar --}}

            @if (optional($user)->avatar)
                <div class="shrink-0">

                    <img src="{{ asset('storage/' . $user->avatar) }}"
                        alt="Employee Profile"
                        class="w-24 h-24
                               rounded-2xl
                               object-cover
                               border border-slate-200
                               shadow-sm">

                </div>
            @else
                <div
                    class="w-24 h-24
                           rounded-2xl
                           bg-slate-100
                           text-slate-400
                           flex items-center
                           justify-center
                           border border-slate-200
                           shrink-0">

                    <i class="fa-solid fa-user text-3xl"></i>

                </div>
            @endif


            <div class="flex-1">

                <label for="avatar"
                    class="block mb-2
                           text-sm font-semibold
                           text-slate-700">
                    Upload Profile Picture
                </label>

                <input id="avatar" type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp"
                    class="block w-full
                           text-sm text-slate-600
                           file:mr-4
                           file:py-2.5
                           file:px-4
                           file:rounded-xl
                           file:border-0
                           file:text-sm
                           file:font-semibold
                           file:bg-sky-50
                           file:text-sky-700
                           hover:file:bg-sky-100">

                <p class="text-xs text-slate-400 mt-2">
                    JPG, JPEG, PNG or WEBP. Maximum 2 MB.
                </p>


                @error('avatar')
                    <p class="text-sm text-red-600 mt-2">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

    </div>

</div>
