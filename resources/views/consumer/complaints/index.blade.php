@extends('consumer.layouts.app')

@section('title', 'My Complaints')

@section('content')

    <div class="space-y-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>

                <h1 class="text-3xl font-bold text-slate-900">
                    My Complaints
                </h1>

                <p class="mt-1 text-slate-500">
                    View and track your water service complaints.
                </p>

            </div>

            <a href="{{ route('consumer.complaints.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-sky-700 text-white font-semibold hover:bg-sky-800">
                <i class="fas fa-plus"></i>
                Submit Complaint
            </a>

        </div>


        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">

            @forelse($complaints as $complaint)
                <a href="{{ route('consumer.complaints.show', $complaint) }}"
                    class="block p-6 border-b last:border-b-0 hover:bg-slate-50 transition">

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                        <div class="flex items-start gap-4">

                            <div
                                class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-droplet"></i>
                            </div>

                            <div>

                                <p class="font-bold text-slate-900">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <h2 class="mt-1 font-semibold text-slate-800">
                                    {{ $complaint->subject }}
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ optional($complaint->category)->category_name ?? 'Water Service Concern' }}
                                </p>

                                <p class="mt-2 text-xs text-slate-400">
                                    Submitted {{ $complaint->created_at->format('M d, Y h:i A') }}
                                </p>

                            </div>

                        </div>


                        <div class="flex items-center gap-4">

                            @php
                                $statusClasses = [
                                    'Pending' => 'bg-amber-100 text-amber-700',
                                    'Verified' => 'bg-blue-100 text-blue-700',
                                    'Assigned' => 'bg-indigo-100 text-indigo-700',
                                    'In Progress' => 'bg-sky-100 text-sky-700',
                                    'Completed' => 'bg-emerald-100 text-emerald-700',
                                    'Closed' => 'bg-slate-100 text-slate-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                ];
                            @endphp

                            <span
                                class="inline-flex px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusClasses[$complaint->status] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $complaint->status }}
                            </span>

                            <i class="fas fa-chevron-right text-slate-400"></i>

                        </div>

                    </div>

                </a>

            @empty

                <div class="p-16 text-center">

                    <div class="mx-auto w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                        <i class="fas fa-file-circle-question text-2xl"></i>
                    </div>

                    <h2 class="mt-5 text-xl font-bold text-slate-900">
                        No complaints found
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        You have not submitted any water service complaints yet.
                    </p>

                    <a href="{{ route('consumer.complaints.create') }}"
                        class="inline-flex items-center gap-2 mt-6 px-5 py-3 rounded-xl bg-sky-700 text-white font-semibold hover:bg-sky-800">
                        <i class="fas fa-plus"></i>
                        Submit Your First Complaint
                    </a>

                </div>
            @endforelse

        </div>


        <div>
            {{ $complaints->links() }}
        </div>

    </div>

@endsection
