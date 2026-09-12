@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="space-y-8">

        <div class="rounded-3xl bg-gradient-to-r from-blue-700 via-sky-700 to-cyan-600 text-white p-10">

            <h1 class="text-4xl font-bold">

                Welcome back,
                {{ auth()->user()->name }}

            </h1>

            <p class="mt-3 opacity-90">

                Intelligent Maintenance Knowledge Management System

            </p>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <x-admin.stat-card title="Consumers" value="{{ $totalConsumers ?? 0 }}" subtitle="Registered consumers" icon="fa-solid fa-users" />

            <x-admin.stat-card title="Employee" value="{{ $totalTechnicians ?? 0 }}" subtitle="Maintenance personnel"
                icon="fa-solid fa-user" />

            <x-admin.stat-card title="Equipment" value="0" subtitle="Registered equipment"
                icon="fa-solid fa-screwdriver-wrench" />

            <x-admin.stat-card title="Open Complaints" value="{{ $totalComplaints ?? 0 }}" subtitle="Pending complaints"
                icon="fa-solid fa-file-circle-exclamation" />

        </div>

        <div class="grid lg:grid-cols-2 gap-6">

            <div class="card p-6">

                <h2 class="font-semibold text-lg mb-4">

                    Recent Complaints

                </h2>

                <div class="text-slate-400">

                    No data available.

                </div>

            </div>

            <div class="card p-6">

                <h2 class="font-semibold text-lg mb-4">

                    Recent AI Recommendations

                </h2>

                <div class="text-slate-400">

                    No recommendations yet.

                </div>

            </div>

        </div>

    </div>

@endsection
