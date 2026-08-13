<div class="space-y-8">

    {{-- ========================================================= --}}
    {{-- CONSUMER INFORMATION --}}
    {{-- ========================================================= --}}

    <div>
        <div class="mb-5">
            <h3 class="text-lg font-semibold text-gray-900">
                Consumer Information
            </h3>

            <p class="text-sm text-gray-500">
                Enter the consumer's personal information.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- First Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    First Name <span class="text-red-500">*</span>
                </label>

                <input type="text" name="first_name" value="{{ old('first_name', $consumer->first_name ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>

                @error('first_name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Middle Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Middle Name
                </label>

                <input type="text" name="middle_name" value="{{ old('middle_name', $consumer->middle_name ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                @error('middle_name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Last Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Last Name <span class="text-red-500">*</span>
                </label>

                <input type="text" name="last_name" value="{{ old('last_name', $consumer->last_name ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>

                @error('last_name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Suffix --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Suffix
                </label>

                <input type="text" name="suffix" placeholder="Jr., Sr., III"
                    value="{{ old('suffix', $consumer->suffix ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            {{-- Sex --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Sex <span class="text-red-500">*</span>
                </label>

                <select name="sex"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">Select Sex</option>

                    <option value="Male" @selected(old('sex', $consumer->sex ?? '') === 'Male')>
                        Male
                    </option>

                    <option value="Female" @selected(old('sex', $consumer->sex ?? '') === 'Female')>
                        Female
                    </option>
                </select>

                @error('sex')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Birth Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Birth Date
                </label>

                <input type="date" name="birth_date"
                    value="{{ old('birth_date', isset($consumer) && $consumer->birth_date ? $consumer->birth_date->format('Y-m-d') : '') }}"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Contact Number <span class="text-red-500">*</span>
                </label>

                <input type="text" name="phone" value="{{ old('phone', $consumer->phone ?? '') }}"
                    placeholder="09XXXXXXXXX"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>

                @error('phone')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>

                <input type="email" name="email" value="{{ old('email', $consumer->email ?? '') }}"
                    placeholder="Optional"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                <p class="text-xs text-gray-500 mt-1">
                    Optional for consumers without email access.
                </p>
            </div>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- RESIDENTIAL ADDRESS --}}
    {{-- ========================================================= --}}

    <div class="border-t pt-8">

        <div class="mb-5">
            <h3 class="text-lg font-semibold text-gray-900">
                Residential Address
            </h3>

            <p class="text-sm text-gray-500">
                Consumer's current residential address.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- House --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    House No.
                </label>

                <input type="text" name="house_no" value="{{ old('house_no', $consumer->address->house_no ?? '') }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

            {{-- Street --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Street
                </label>

                <input type="text" name="street" value="{{ old('street', $consumer->address->street ?? '') }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

            {{-- Purok --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Purok
                </label>

                <input type="text" name="purok" value="{{ old('purok', $consumer->address->purok ?? '') }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

            {{-- Barangay --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Barangay <span class="text-red-500">*</span>
                </label>

                <input type="text" name="barangay" value="{{ old('barangay', $consumer->address->barangay ?? '') }}"
                    class="w-full rounded-lg border-gray-300" required>

                @error('barangay')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Municipality --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Municipality <span class="text-red-500">*</span>
                </label>

                <input type="text" name="municipality"
                    value="{{ old('municipality', $consumer->address->municipality ?? 'Sagay') }}"
                    class="w-full rounded-lg border-gray-300" required>

                @error('municipality')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Province --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Province <span class="text-red-500">*</span>
                </label>

                <input type="text" name="province"
                    value="{{ old('province', $consumer->address->province ?? 'Negros Occidental') }}"
                    class="w-full rounded-lg border-gray-300" required>

                @error('province')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ZIP --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    ZIP Code
                </label>

                <input type="text" name="zip_code"
                    value="{{ old('zip_code', $consumer->address->zip_code ?? '') }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SERVICE CONNECTION --}}
    {{-- ========================================================= --}}

    <div class="border-t pt-8">

        <div class="mb-5">
            <h3 class="text-lg font-semibold text-gray-900">
                Water Service Connection
            </h3>

            <p class="text-sm text-gray-500">
                Enter the consumer's water service and meter information.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- Account Number --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Account Number <span class="text-red-500">*</span>
                </label>

                <input type="text" name="account_number"
                    value="{{ old('account_number', $connection->account_number ?? '') }}"
                    class="w-full rounded-lg border-gray-300" required>

                @error('account_number')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Service Connection Number --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Service Connection No.
                </label>

                <input type="text" name="service_connection_number"
                    value="{{ old('service_connection_number', $connection->service_connection_number ?? '') }}"
                    placeholder="Auto-generated if blank" class="w-full rounded-lg border-gray-300">

                <p class="text-xs text-gray-500 mt-1">
                    Leave blank to generate automatically.
                </p>
            </div>

            {{-- Meter Number --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Meter Number <span class="text-red-500">*</span>
                </label>

                <input type="text" name="meter_number"
                    value="{{ old('meter_number', $connection->meter_number ?? '') }}"
                    class="w-full rounded-lg border-gray-300" required>

                @error('meter_number')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Connection Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Connection Type <span class="text-red-500">*</span>
                </label>

                <select name="connection_type" class="w-full rounded-lg border-gray-300" required>
                    <option value="">Select Type</option>

                    @foreach (['Residential', 'Commercial', 'Government', 'Institutional'] as $type)
                        <option value="{{ $type }}" @selected(old('connection_type', $connection->connection_type ?? 'Residential') === $type)>
                            {{ $type }}
                        </option>
                    @endforeach

                </select>

                @error('connection_type')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Meter Size --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Meter Size
                </label>

                <input type="text" name="meter_size"
                    value="{{ old('meter_size', $connection->meter_size ?? '') }}" placeholder="e.g. 1/2 inch"
                    class="w-full rounded-lg border-gray-300">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Status <span class="text-red-500">*</span>
                </label>

                <select name="status" class="w-full rounded-lg border-gray-300" required>

                    @foreach (['Pending', 'Active', 'Inactive', 'Disconnected', 'Temporary'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $connection->status ?? 'Pending') === $status)>
                            {{ $status }}
                        </option>
                    @endforeach

                </select>

                @error('status')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Installation Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Installation Date
                </label>

                <input type="date" name="installation_date"
                    value="{{ old(
                        'installation_date',
                        isset($connection) && $connection->installation_date ? $connection->installation_date->format('Y-m-d') : '',
                    ) }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SERVICE ADDRESS --}}
    {{-- ========================================================= --}}

    <div class="border-t pt-8">

        <div class="mb-5">
            <h3 class="text-lg font-semibold text-gray-900">
                Service Address
            </h3>

            <p class="text-sm text-gray-500">
                Location where the water service is installed.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- House --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    House No.
                </label>

                <input type="text" name="service_house_no"
                    value="{{ old('service_house_no', $connection->address->house_no ?? '') }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

            {{-- Street --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Street
                </label>

                <input type="text" name="service_street"
                    value="{{ old('service_street', $connection->address->street ?? '') }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

            {{-- Purok --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Purok
                </label>

                <input type="text" name="service_purok"
                    value="{{ old('service_purok', $connection->address->purok ?? '') }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

            {{-- Barangay --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Barangay <span class="text-red-500">*</span>
                </label>

                <input type="text" name="service_barangay"
                    value="{{ old('service_barangay', $connection->address->barangay ?? '') }}"
                    class="w-full rounded-lg border-gray-300" required>

                @error('service_barangay')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- City --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    City
                </label>

                <input type="text" name="service_city"
                    value="{{ old('service_city', $connection->address->city ?? 'Sagay City') }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

            {{-- Province --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Province
                </label>

                <input type="text" name="service_province"
                    value="{{ old('service_province', $connection->address->province ?? 'Negros Occidental') }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

            {{-- Postal Code --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Zip Code
                </label>

                <input type="text" name="service_zip_code"
                    value="{{ old('service_zip_code', $connection->address->zip_code ?? '') }}"
                    class="w-full rounded-lg border-gray-300">
            </div>

            {{-- Landmark --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Landmark
                </label>

                <input type="text" name="landmark"
                    value="{{ old('landmark', $connection->address->landmark ?? '') }}" placeholder="Optional"
                    class="w-full rounded-lg border-gray-300">
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- LOCATION / REMARKS --}}
    {{-- ========================================================= --}}

    <div class="border-t pt-8">

        <div class="mb-5">
            <h3 class="text-lg font-semibold text-gray-900">
                Additional Information
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- Remarks --}}
            <div class="md:col-span-2 xl:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Remarks
                </label>

                <textarea name="remarks" placeholder="Optional" rows="3" class="w-full rounded-lg border-gray-300">{{ old('remarks', $connection->remarks ?? '') }}</textarea>
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- PORTAL ACCOUNT --}}
    {{-- ========================================================= --}}

    <div class="border-t pt-8">

        <div class="flex items-start gap-4">

            <input type="checkbox" name="create_account" value="1" id="create_account"
                @checked(old('create_account')) class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

            <div>

                <label for="create_account" class="font-medium text-gray-900 cursor-pointer">
                    Create Consumer Portal Account
                </label>

                <p class="text-sm text-gray-500 mt-1">
                    Enable this if the consumer will use the online portal.
                    Consumers without email access can remain offline records.
                </p>

                <p class="text-xs text-gray-400 mt-2">
                    Temporary password:
                    <span class="font-medium">Temp@12345</span>
                </p>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ACTIONS --}}
    {{-- ========================================================= --}}

    <div class="border-t pt-6 flex items-center justify-end gap-3">

        <a href="{{ route('customer-service.consumers.index') }}"
            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
            Cancel
        </a>

        <button type="submit"
            class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition">
            {{ isset($consumer) ? 'Update Consumer' : 'Register Consumer' }}
        </button>

    </div>

</div>
