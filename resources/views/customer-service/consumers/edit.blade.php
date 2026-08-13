@extends('customer-service.layouts.app')

@section('title', 'Edit Consumer')

@section('content')

    <div class="space-y-6">

        <x-form.page-header title="Edit Consumer" subtitle="Update consumer, address, and water service information." />

        @if ($errors->any())

            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-5">

                <div class="flex items-start gap-3">

                    <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center shrink-0">

                        <i class="fa-solid fa-circle-exclamation text-red-600"></i>

                    </div>

                    <div>

                        <h3 class="font-semibold text-red-800">
                            Please correct the following errors:
                        </h3>

                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif
        <form action="{{ route('customer-service.consumers.update', $consumer) }}" method="POST" class="space-y-6">

            @csrf
            @method('PUT')

            {{-- ===================================================== --}}
            {{-- CONSUMER INFORMATION --}}
            {{-- ===================================================== --}}

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <h3 class="font-semibold text-gray-900">
                        Consumer Information
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Personal information of the registered consumer.
                    </p>

                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                    <x-form.input label="Consumer No." name="consumer_no" :value="$consumer->consumer_no" disabled />

                    <x-form.input label="First Name" name="first_name" :value="old('first_name', $consumer->first_name)" required />

                    <x-form.input label="Middle Name" name="middle_name" :value="old('middle_name', $consumer->middle_name)" />

                    <x-form.input label="Last Name" name="last_name" :value="old('last_name', $consumer->last_name)" required />

                    <x-form.input label="Suffix" name="suffix" :value="old('suffix', $consumer->suffix)" />

                    {{-- Sex --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sex <span class="text-red-500">*</span>
                        </label>

                        <select name="sex"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>

                            <option value="">Select Sex</option>

                            <option value="Male" @selected(old('sex', $consumer->sex) === 'Male')>
                                Male
                            </option>

                            <option value="Female" @selected(old('sex', $consumer->sex) === 'Female')>
                                Female
                            </option>

                        </select>

                        @error('sex')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <x-form.input label="Birth Date" name="birth_date" type="date" :value="old('birth_date', optional($consumer->birth_date)->format('Y-m-d'))" />

                    <x-form.input label="Contact Number" name="phone" :value="old('phone', $consumer->phone)" required />

                    <x-form.input label="Email" name="email" type="email" :value="old('email', $consumer->email)" />

                </div>

            </x-form.card>


            {{-- ===================================================== --}}
            {{-- RESIDENTIAL ADDRESS --}}
            {{-- ===================================================== --}}

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <h3 class="font-semibold text-gray-900">
                        Residential Address
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Current residential address of the consumer.
                    </p>

                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                    <x-form.input label="House No." name="house_no" :value="old('house_no', $consumer->address?->house_no)" />

                    <x-form.input label="Street" name="street" :value="old('street', $consumer->address?->street)" />

                    <x-form.input label="Purok" name="purok" :value="old('purok', $consumer->address?->purok)" />

                    <x-form.input label="Barangay" name="barangay" :value="old('barangay', $consumer->address?->barangay)" required />

                    <x-form.input label="Municipality" name="municipality" :value="old('municipality', $consumer->address?->municipality ?? 'Sagay')" />

                    <x-form.input label="Province" name="province" :value="old('province', $consumer->address?->province ?? 'Negros Occidental')" />

                    <x-form.input label="ZIP Code" name="zip_code" :value="old('zip_code', $consumer->address?->zip_code)" />

                </div>

            </x-form.card>


            {{-- ===================================================== --}}
            {{-- SERVICE CONNECTION --}}
            {{-- ===================================================== --}}

            @if ($connection)

                <x-form.card>

                    <div class="px-6 py-5 border-b border-gray-100">

                        <h3 class="font-semibold text-gray-900">
                            Service Connection
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Water service account and meter information.
                        </p>

                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                        <x-form.input label="Account Number" name="account_number" :value="old('account_number', $connection->account_number)" required />

                        <x-form.input label="Service Connection No." name="service_connection_number" :value="$connection->service_connection_number"
                            disabled />

                        <x-form.input label="Meter Number" name="meter_number" :value="old('meter_number', $connection->meter_number)" required />

                        {{-- Connection Type --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Connection Type
                                <span class="text-red-500">*</span>
                            </label>

                            <select name="connection_type"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                required>

                                @foreach (['Residential', 'Commercial', 'Government', 'Institutional'] as $type)
                                    <option value="{{ $type }}" @selected(old('connection_type', $connection->connection_type) === $type)>
                                        {{ $type }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        <x-form.input label="Meter Size" name="meter_size" :value="old('meter_size', $connection->meter_size)" />


                        {{-- Status --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                                <span class="text-red-500">*</span>
                            </label>

                            <select name="connection_status"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                required>

                                @foreach (['Active', 'Inactive', 'Disconnected', 'Temporary', 'Pending'] as $status)
                                    <option value="{{ $status }}" @selected(old('connection_status', $connection->status) === $status)>
                                        {{ $status }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        <x-form.input label="Installation Date" name="installation_date" type="date"
                            :value="old(
                                'installation_date',
                                optional($connection->installation_date)->format('Y-m-d'),
                            )" />

                    </div>


                    {{-- Remarks --}}

                    <div class="px-6 pb-6">

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Remarks
                        </label>

                        <textarea name="remarks" rows="3"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('remarks', $connection->remarks) }}</textarea>

                    </div>

                </x-form.card>


                {{-- ================================================= --}}
                {{-- SERVICE ADDRESS --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-6 py-5 border-b border-gray-100">

                        <h3 class="font-semibold text-gray-900">
                            Service Address
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Location where the water service is connected.
                        </p>

                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                        <x-form.input label="House No." name="service_house_no" :value="old('service_house_no', $connection->address?->house_no)" />

                        <x-form.input label="Street" name="service_street" :value="old('service_street', $connection->address?->street)" />

                        <x-form.input label="Purok" name="service_purok" :value="old('service_purok', $connection->address?->purok)" />

                        <x-form.input label="Barangay" name="service_barangay" :value="old('service_barangay', $connection->address?->barangay)" required />

                        <x-form.input label="City" name="service_city" :value="old('service_city', $connection->address?->city ?? 'Sagay City')" required />

                        <x-form.input label="Province" name="service_province" :value="old('service_province', $connection->address?->province ?? 'Negros Occidental')" required />

                        <x-form.input label="Zip Code" name="service_zip_code" :value="old('service_zip_code', $connection->address?->zip_code)" />

                        <x-form.input label="Landmark" name="landmark" :value="old('landmark', $connection->address?->landmark)" />

                    </div>

                </x-form.card>

            @endif

            {{-- ===================================================== --}}
            {{-- PORTAL ACCOUNT --}}
            {{-- ===================================================== --}}

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <h3 class="font-semibold text-gray-900">
                        Consumer Portal Account
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Manage the consumer's access to the iSWD consumer portal.
                    </p>

                </div>

                <div class="p-6">

                    @if ($consumer->user)

                        {{-- Existing Account --}}

                        <div class="flex items-start gap-4">

                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">

                                <i class="fa-solid fa-circle-check text-green-600"></i>

                            </div>

                            <div>

                                <p class="font-medium text-gray-900">
                                    Consumer Portal Account Active
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    This consumer already has an iSWD portal account.
                                </p>

                                @if ($consumer->user->email)
                                    <p class="text-sm text-gray-600 mt-2">

                                        <i class="fa-solid fa-envelope mr-1"></i>

                                        {{ $consumer->user->email }}

                                    </p>
                                @else
                                    <p class="text-sm text-amber-600 mt-2">

                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>

                                        No email address is currently associated with this account.

                                    </p>
                                @endif

                            </div>

                        </div>
                    @else
                        {{-- No Existing Account --}}

                        <div class="flex items-start gap-4">

                            <input type="checkbox" name="create_account" value="1" id="create_account"
                                @checked(old('create_account'))
                                class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                            <div>

                                <label for="create_account" class="font-medium text-gray-900 cursor-pointer">
                                    Create Consumer Portal Account
                                </label>

                                <p class="text-sm text-gray-500 mt-1">
                                    Enable this if the consumer will use the iSWD online portal.
                                </p>

                                <p class="text-sm text-gray-500 mt-2">

                                    Consumers without email access can remain registered
                                    as offline records.

                                </p>

                                <p class="text-xs text-gray-400 mt-2">

                                    Temporary password:
                                    <span class="font-medium text-gray-600">
                                        Temp@12345
                                    </span>

                                </p>

                            </div>

                        </div>

                    @endif

                </div>

            </x-form.card>


            {{-- ===================================================== --}}
            {{-- ACTIONS --}}
            {{-- ===================================================== --}}

            <div class="flex items-center justify-end gap-3">

                <a href="{{ route('customer-service.consumers.show', $consumer) }}"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">

                    <i class="fa-solid fa-floppy-disk mr-2"></i>

                    Save Changes

                </button>

            </div>

        </form>

    </div>

@endsection
