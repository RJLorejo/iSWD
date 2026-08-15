@extends($layout)

@section('title', 'Edit Profile')

@section('content')

    <div class="max-w-7xl mx-auto space-y-8">

        {{-- PAGE HEADER --}}
        <div>

            <h1 class="text-2xl font-bold text-gray-900">
                Edit Profile
            </h1>

            <p class="text-gray-500 mt-1">
                Update your personal information and account security.
            </p>

        </div>


        {{-- VALIDATION ERRORS --}}
        @if ($errors->any())

            <div class="rounded-xl border border-red-200 bg-red-50 p-5">

                <div class="flex gap-3">

                    <i class="fas fa-circle-exclamation text-red-600 mt-1"></i>

                    <div>

                        <p class="font-semibold text-red-700">
                            Please correct the following errors:
                        </p>

                        <ul class="mt-2 list-disc list-inside text-sm text-red-600">

                            @foreach ($errors->all() as $error)
                                <li>
                                    {{ $error }}
                                </li>
                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif


        <div class="grid lg:grid-cols-3 gap-8">

            {{-- PROFILE INFORMATION --}}
            <div class="lg:col-span-2">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">

                    <div class="border-b border-gray-200 p-6">

                        <h2 class="text-xl font-bold text-gray-900">
                            Personal Information
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Keep your account information up to date.
                        </p>

                    </div>


                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        @method('PUT')


                        <div class="p-8 grid md:grid-cols-2 gap-6">

                            @include('profile.partials.profile-form')

                        </div>


                        {{-- ACTIONS --}}
                        <div
                            class="border-t border-gray-200 p-6
                               flex flex-col sm:flex-row
                               justify-end gap-3">

                            <a href="{{ route('profile.show') }}"
                                class="inline-flex items-center justify-center gap-2
                                   px-5 py-3 rounded-xl
                                   border border-gray-300
                                   text-gray-700
                                   hover:bg-gray-50
                                   transition">

                                <i class="fas fa-arrow-left"></i>

                                Cancel

                            </a>


                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2
                                   px-6 py-3 rounded-xl
                                   bg-sky-700 text-white
                                   font-medium
                                   hover:bg-sky-800
                                   transition">

                                <i class="fas fa-save"></i>

                                Save Changes

                            </button>

                        </div>

                    </form>

                </div>

            </div>


            {{-- PASSWORD --}}
            <div>

                @include('profile.partials.password-form')

            </div>

        </div>

    </div>

@endsection
