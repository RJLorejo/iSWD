@php
    $user = $user ?? null;

    $selectedRole = old('role', $user && $user->roles->isNotEmpty() ? $user->roles->first()->name : '');
@endphp

<x-form.input label="Employee ID" name="employee_id" :value="old('employee_id', optional($user)->employee_id)" required />

<x-form.input label="First Name" name="first_name" :value="old('first_name', optional($user)->first_name)" required />

<x-form.input label="Middle Name" name="middle_name" :value="old('middle_name', optional($user)->middle_name)" />

<x-form.input label="Last Name" name="last_name" :value="old('last_name', optional($user)->last_name)" required />

<x-form.input label="Suffix" name="suffix" :value="old('suffix', optional($user)->suffix)" />

<x-form.input label="Email" type="email" name="email" :value="old('email', optional($user)->email)" required />

<x-form.select label="Department" name="department_id" :options="$departments" :selected="old('department_id', optional($user)->department_id)" required />

<x-form.select label="Position" name="position_id" :options="$positions" :selected="old('position_id', optional($user)->position_id)" required />

<x-form.select label="Role" name="role" :options="$roles" :selected="$selectedRole" required />

<x-form.input label="Phone" name="phone" :value="old('phone', optional($user)->phone)" />

<div>
    <label class="block mb-2 font-semibold">
        Profile Picture
    </label>

    @if (optional($user)->avatar)
        <img src="{{ asset('storage/' . $user->avatar) }}" class="w-24 h-24 rounded-full object-cover mb-3">
    @endif

    <input type="file" name="avatar" class="w-full">
</div>
