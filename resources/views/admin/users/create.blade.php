@extends('admin.layouts.app')

@section('title', 'Register Employee')

@section('content')

    <x-form.page-header title="" subtitle="Create a new employee account" />

    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-2xl shadow border">

            <div class="border-b p-6">

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-full bg-sky-700 text-white flex items-center justify-center">

                            1

                        </div>

                        <div>

                            <h2 class="font-bold">

                                Basic Information

                            </h2>

                            <small class="text-gray-500">

                                Employee personal information

                            </small>

                        </div>

                    </div>

                </div>

            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="p-8 grid grid-cols-2 gap-6">

                    @include('admin.users.partials.form')

                </div>


                <div class="border-t p-6 flex justify-end">

                    <a href="{{ route('admin.users.index') }}"
                        class="        px-5
        py-2.5
        rounded-xl
        font-medium
        transition
        duration-300
        shadow-sm
        hover:shadow-lg">

                        Cancel

                    </a>

                    <x-form.button>

                        Continue →

                    </x-form.button>


                </div>

            </form>

        </div>

    </div>

@endsection
