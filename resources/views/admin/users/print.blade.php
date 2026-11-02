@extends('layouts.report')

@section('title', 'Employee Report')

@section('back-url')
    {{ route('admin.users.index', request()->query()) }}
@endsection

@section('report-title', 'Employee Report')

@section('report-subtitle')
    iSWD AI-Assisted Consumer Complaint Management and Service Support System
@endsection

@section('report-content')

    {{-- APPLIED FILTERS --}}
    <section class="report-section">

        <h3 class="report-section-title">
            Report Filters
        </h3>

        <div class="report-grid">

            <div class="report-field">
                <div class="report-field-label">Search</div>
                <div class="report-field-value">
                    {{ request('search') ?: 'All' }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Department</div>
                <div class="report-field-value">
                    @if (request('department'))
                        {{ $users->first()?->department?->department_name ?? 'Selected Department' }}
                    @else
                        All Departments
                    @endif
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Role</div>
                <div class="report-field-value">
                    {{ request('role') ?: 'All Roles' }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Status</div>
                <div class="report-field-value">
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

    </section>


    {{-- EMPLOYEE SUMMARY --}}
    <section class="report-section">

        <h3 class="report-section-title">
            Employee Summary
        </h3>

        <div class="report-grid">

            <div class="report-field">
                <div class="report-field-label">Total Employees</div>
                <div class="report-field-value">
                    {{ number_format($totalEmployees) }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Active Employees</div>
                <div class="report-field-value">
                    {{ number_format($activeEmployees) }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Inactive Employees</div>
                <div class="report-field-value">
                    {{ number_format($inactiveEmployees) }}
                </div>
            </div>

        </div>

    </section>


    {{-- EMPLOYEE RECORDS --}}
    <section class="report-section">

        <div class="report-section-heading-row">

            <h3 class="report-section-title">
                Employee Records
            </h3>

            <div class="report-record-count">
                Records:
                <strong>{{ number_format($users->count()) }}</strong>
            </div>

        </div>

        <div class="report-table-wrapper">

            <table class="report-table">

                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 11%;">Employee ID</th>
                        <th style="width: 19%;">Employee Name</th>
                        <th style="width: 14%;">Department</th>
                        <th style="width: 14%;">Position</th>
                        <th style="width: 12%;">Role</th>
                        <th style="width: 9%;">Status</th>
                        <th style="width: 16%;">Last Login</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($users as $user)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $user->employee_id ?: '—' }}
                            </td>

                            <td>
                                {{ $user->full_name }}
                            </td>

                            <td>
                                {{ $user->department?->department_name ?? '—' }}
                            </td>

                            <td>
                                {{ $user->position?->position_name ?? '—' }}
                            </td>

                            <td>
                                {{ $user->roles->pluck('name')->join(', ') ?: '—' }}
                            </td>

                            <td>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </td>

                            <td>
                                @if ($user->last_login_at)
                                    {{ $user->last_login_at->format('M d, Y h:i A') }}
                                @else
                                    Never logged in
                                @endif
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center">
                                No employee records match the selected filters.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

@endsection
