@extends('customer-service.layouts.app')

@section('title', 'Edit Profile')

@section('content')

    <div class="grid lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2">

            <div class="bg-white rounded-2xl shadow border">

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    @method('PUT')

                    <div class="p-8 grid md:grid-cols-2 gap-6">

                        @include('profile.partials.profile-form')

                    </div>

                    <div class="border-t p-6 flex justify-end gap-3">

                        <a href="{{ route('profile.show') }}" class="px-5 py-3 rounded-xl border">

                            Cancel

                        </a>

                        <button class="px-6 py-3 rounded-xl bg-sky-700 text-white">

                            Save Changes

                        </button>

                    </div>

                </form>

            </div>

        </div>

        <div>

            @include('profile.partials.password-form')

        </div>

    </div>

@endsection
