@extends('customer-service.layouts.app')

@section('title', 'Consumer Details')

@section('content')

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Consumer Details
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    View consumer information, address, and service connections.
                </p>
            </div>

            <div class="flex items-center gap-3">

                <a href="{{ route('customer-service.consumers.index') }}"
                    class="px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700
                      hover:bg-gray-50 transition">

                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Back
                </a>

                <a href="{{ route('customer-service.consumers.edit', $consumer) }}"
                    class="px-4 py-2.5 rounded-lg bg-blue-600 text-white
                      hover:bg-blue-700 transition">

                    <i class="fa-solid fa-pen mr-2"></i>
                    Edit
                </a>

            </div>

        </div>


        {{-- CONSUMER INFORMATION --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">

            <div class="px-6 py-5 border-b border-gray-200">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Consumer Information
                        </h2>

                        <p class="text-sm text-gray-500">
                            {{ $consumer->consumer_no }}
                        </p>
                    </div>

                    @if ($consumer->is_active)
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium
                                 bg-green-100 text-green-700">
                            Active
                        </span>
                    @else
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium
                                 bg-gray-100 text-gray-600">
                            Inactive
                        </span>
                    @endif

                </div>

            </div>


            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Consumer No.
                        </p>

                        <p class="mt-1 font-medium text-gray-900">
                            {{ $consumer->consumer_no }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Full Name
                        </p>

                        <p class="mt-1 font-medium text-gray-900">
                            {{ $consumer->full_name }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Sex
                        </p>

                        <p class="mt-1 text-gray-900">
                            {{ $consumer->sex }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Birth Date
                        </p>

                        <p class="mt-1 text-gray-900">
                            {{ $consumer->birth_date?->format('F d, Y') ?? '—' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Contact Number
                        </p>

                        <p class="mt-1 text-gray-900">
                            {{ $consumer->phone }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Email
                        </p>

                        <p class="mt-1 text-gray-900">
                            {{ $consumer->email ?: '—' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Portal Account
                        </p>

                        <p class="mt-1 text-gray-900">

                            @if ($consumer->user_id)
                                <span class="text-green-600 font-medium">
                                    <i class="fa-solid fa-circle-check mr-1"></i>
                                    Registered
                                </span>
                            @else
                                <span class="text-gray-500">
                                    No Portal Account
                                </span>
                            @endif

                        </p>
                    </div>


                    <div>
                        <p class="text-xs text-gray-500 uppercase">
                            Registered
                        </p>

                        <p class="mt-1 text-gray-900">
                            {{ $consumer->created_at?->format('F d, Y') }}
                        </p>
                    </div>

                </div>

            </div>

        </div>


        {{-- RESIDENTIAL ADDRESS --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">

            <div class="px-6 py-5 border-b border-gray-200">

                <h2 class="text-lg font-semibold text-gray-900">
                    Residential Address
                </h2>

            </div>

            <div class="p-6">

                @if ($consumer->address)
                    <p class="text-gray-900 font-medium">

                        {{ $consumer->address->full_address }}

                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-6">

                        <div>
                            <p class="text-xs text-gray-500 uppercase">
                                Barangay
                            </p>

                            <p class="mt-1 text-gray-900">
                                {{ $consumer->address->barangay }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase">
                                Municipality
                            </p>

                            <p class="mt-1 text-gray-900">
                                {{ $consumer->address->municipality }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase">
                                Province
                            </p>

                            <p class="mt-1 text-gray-900">
                                {{ $consumer->address->province }}
                            </p>
                        </div>

                    </div>
                @else
                    <p class="text-gray-500">
                        No residential address recorded.
                    </p>
                @endif

            </div>

        </div>


        {{-- SERVICE CONNECTIONS --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">

            <div class="px-6 py-5 border-b border-gray-200">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Service Connections
                        </h2>

                        <p class="text-sm text-gray-500">
                            Water service connection records associated with this consumer.
                        </p>
                    </div>

                    <span class="text-sm text-gray-500">
                        {{ $consumer->serviceConnections->count() }}
                        connection(s)
                    </span>

                </div>

            </div>


            <div class="overflow-x-auto">

                @if ($consumer->serviceConnections->count())

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium
                                       text-gray-500 uppercase">
                                    Account No.
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium
                                       text-gray-500 uppercase">
                                    Connection No.
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium
                                       text-gray-500 uppercase">
                                    Meter No.
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium
                                       text-gray-500 uppercase">
                                    Type
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium
                                       text-gray-500 uppercase">
                                    Status
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @foreach ($consumer->serviceConnections as $connection)
                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $connection->account_number }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $connection->service_connection_number }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $connection->meter_number }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $connection->connection_type }}
                                    </td>

                                    <td class="px-6 py-4">

                                        @php
                                            $statusClasses = match ($connection->status) {
                                                'Active' => 'bg-green-100 text-green-700',
                                                'Inactive' => 'bg-gray-100 text-gray-600',
                                                'Disconnected' => 'bg-red-100 text-red-700',
                                                'Temporary' => 'bg-yellow-100 text-yellow-700',
                                                'Pending' => 'bg-blue-100 text-blue-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp

                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses }}">
                                            {{ $connection->status }}
                                        </span>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                @else
                    <div class="p-10 text-center">

                        <i class="fa-solid fa-droplet text-3xl text-gray-300"></i>

                        <p class="mt-3 text-gray-500">
                            No service connections recorded.
                        </p>

                    </div>

                @endif

            </div>

        </div>

    </div>

@endsection
