<x-form.input label="First Name" name="first_name" :value="old('first_name', $user->first_name)" required />

<x-form.input label="Middle Name" name="middle_name" :value="old('middle_name', $user->middle_name)" />

<x-form.input label="Last Name" name="last_name" :value="old('last_name', $user->last_name)" required />

<x-form.input label="Suffix" name="suffix" :value="old('suffix', $user->suffix)" />

<x-form.input label="Email" type="email" name="email" :value="old('email', $user->email)" required />

<x-form.input label="Phone" name="phone" :value="old('phone', $user->phone)" />

<div class="md:col-span-2">

    <label class="font-semibold block mb-2">

        Profile Picture

    </label>

    @if ($user->avatar)
        <img src="{{ asset('storage/' . $user->avatar) }}" class="w-24 h-24 rounded-full object-cover mb-3">
    @endif

    <input type="file" name="avatar">

</div>
