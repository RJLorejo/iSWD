<div class="bg-white rounded-2xl shadow border">

    <form action="{{ route('profile.password') }}" method="POST">

        @csrf

        @method('PUT')

        <div class="p-6">

            <h2 class="font-bold text-xl mb-6">

                Change Password

            </h2>

            <x-form.input type="password" label="Current Password" name="current_password" required />

            <x-form.input type="password" label="New Password" name="password" required />

            <x-form.input type="password" label="Confirm Password" name="password_confirmation" required />

            <button class="mt-5 w-full bg-sky-700 text-white rounded-xl py-3">

                Update Password

            </button>

        </div>

    </form>

</div>
