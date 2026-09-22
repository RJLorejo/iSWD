<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Employee Report | iSWD</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        @media print {

            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .print-container {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>

</head>


<body class="bg-slate-100 text-slate-900">

    <div class="max-w-7xl mx-auto p-6">


        {{-- Print Controls --}}

        <div
            class="no-print
                   flex items-center
                   justify-between
                   gap-4 mb-6">

            <a href="{{ route('admin.users.index', request()->query()) }}"
                class="inline-flex
                       items-center gap-2
                       px-5 py-3
                       rounded-xl
                       border border-slate-300
                       bg-white
                       text-slate-700
                       font-semibold">

                ← Back

            </a>


            <button type="button" onclick="window.print()"
                class="inline-flex
                       items-center gap-2
                       px-6 py-3
                       rounded-xl
                       bg-sky-700
                       text-white
                       font-semibold">

                Print Report

            </button>

        </div>


        <div
            class="print-container
                   bg-white
                   rounded-2xl
                   border border-slate-200
                   shadow-sm
                   p-8">


            {{-- Report Header --}}

            <div
                class="flex items-center
                       justify-between
                       border-b-2
                       border-slate-800
                       pb-5">

                <div class="flex items-center gap-4">

                    <img src="{{ asset('images/logo/logo.png') }}" alt="Sagay Water District"
                        class="w-16 h-16 object-contain">

                    <div>

                        <h1
                            class="text-xl
                                   font-bold
                                   uppercase">
                            Sagay Water District
                        </h1>

                        <p class="text-sm text-slate-600">
                            iSWD AI-Assisted Consumer Complaint
                            Management and Service Support System
                        </p>

                    </div>

                </div>


                <div class="text-right">

                    <h2 class="text-lg
                               font-bold">
                        Employee Report
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Generated:
                        {{ now()->format('F d, Y h:i A') }}
                    </p>

                </div>

            </div>


            {{-- Applied Filters --}}

            <div class="mt-5">

                <p
                    class="text-xs font-bold
                           uppercase
                           tracking-wider
                           text-slate-500">
                    Report Filters
                </p>


                <div
                    class="mt-2 flex
                           flex-wrap gap-x-6
                           gap-y-2
                           text-sm">

                    <div>

                        <span class="font-semibold">
                            Search:
                        </span>

                        {{ request('search') ?: 'All' }}

                    </div>


                    <div>

                        <span class="font-semibold">
                            Department:
                        </span>

                        @if (request('department'))
                            {{ $users->first()?->department?->department_name ?? 'Selected Department' }}
                        @else
                            All Departments
                        @endif

                    </div>


                    <div>

                        <span class="font-semibold">
                            Role:
                        </span>

                        {{ request('role') ?: 'All Roles' }}

                    </div>


                    <div>

                        <span class="font-semibold">
                            Status:
                        </span>

                        @if (request('status') === '1')
                            Active
                        @elseif (request('status') === '0')
                            Inactive
                        @else
                            All Statuses
                        @endif

                    </div>

                </div>

            </div>


            {{-- Summary --}}

            <div class="grid grid-cols-3
                       gap-4 mt-6">

                <div
                    class="border
                           border-slate-300
                           rounded-lg p-4">

                    <p class="text-xs text-slate-500">
                        Employees
                    </p>

                    <p class="text-xl font-bold mt-1">
                        {{ number_format($totalEmployees) }}
                    </p>

                </div>


                <div
                    class="border
                           border-slate-300
                           rounded-lg p-4">

                    <p class="text-xs text-slate-500">
                        Active
                    </p>

                    <p class="text-xl font-bold mt-1">
                        {{ number_format($activeEmployees) }}
                    </p>

                </div>


                <div
                    class="border
                           border-slate-300
                           rounded-lg p-4">

                    <p class="text-xs text-slate-500">
                        Inactive
                    </p>

                    <p class="text-xl font-bold mt-1">
                        {{ number_format($inactiveEmployees) }}
                    </p>

                </div>

            </div>


            {{-- Table --}}

            <div class="mt-6">

                <table class="w-full
                           border-collapse
                           text-xs">

                    <thead>

                        <tr class="bg-slate-100">

                            <th
                                class="border
                                       border-slate-300
                                       px-3 py-2
                                       text-left">
                                #
                            </th>

                            <th
                                class="border
                                       border-slate-300
                                       px-3 py-2
                                       text-left">
                                Employee ID
                            </th>

                            <th
                                class="border
                                       border-slate-300
                                       px-3 py-2
                                       text-left">
                                Employee Name
                            </th>

                            <th
                                class="border
                                       border-slate-300
                                       px-3 py-2
                                       text-left">
                                Department
                            </th>

                            <th
                                class="border
                                       border-slate-300
                                       px-3 py-2
                                       text-left">
                                Position
                            </th>

                            <th
                                class="border
                                       border-slate-300
                                       px-3 py-2
                                       text-left">
                                Role
                            </th>

                            <th
                                class="border
                                       border-slate-300
                                       px-3 py-2
                                       text-left">
                                Status
                            </th>

                            <th
                                class="border
                                       border-slate-300
                                       px-3 py-2
                                       text-left">
                                Last Login
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($users as $user)
                            <tr>

                                <td
                                    class="border
                                           border-slate-300
                                           px-3 py-2">
                                    {{ $loop->iteration }}
                                </td>


                                <td
                                    class="border
                                           border-slate-300
                                           px-3 py-2">
                                    {{ $user->employee_id ?: '—' }}
                                </td>


                                <td
                                    class="border
                                           border-slate-300
                                           px-3 py-2
                                           font-medium">
                                    {{ $user->full_name }}
                                </td>


                                <td
                                    class="border
                                           border-slate-300
                                           px-3 py-2">
                                    {{ $user->department?->department_name ?? '—' }}
                                </td>


                                <td
                                    class="border
                                           border-slate-300
                                           px-3 py-2">
                                    {{ $user->position?->position_name ?? '—' }}
                                </td>


                                <td
                                    class="border
                                           border-slate-300
                                           px-3 py-2">
                                    {{ $user->roles->pluck('name')->join(', ') ?: '—' }}
                                </td>


                                <td
                                    class="border
                                           border-slate-300
                                           px-3 py-2">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </td>


                                <td
                                    class="border
                                           border-slate-300
                                           px-3 py-2">

                                    @if ($user->last_login_at)
                                        {{ $user->last_login_at->format('M d, Y h:i A') }}
                                    @else
                                        Never logged in
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="border
                                           border-slate-300
                                           px-4 py-8
                                           text-center
                                           text-slate-500">
                                    No employee records match
                                    the selected filters.
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Footer --}}

            <div
                class="mt-8 pt-4
                       border-t
                       border-slate-300
                       flex justify-between
                       text-xs text-slate-500">

                <span>
                    iSWD Employee Report
                </span>

                <span>
                    Generated by:
                    {{ auth()->user()->full_name }}
                </span>

            </div>

        </div>

    </div>

</body>

</html>
