@extends('admin.layouts.app')

@section('title', 'Activity Details')

@section('content')
<div class="space-y-6">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Activity Details</h1>
            <p class="text-sm text-slate-500">Read-only audit record #{{ $activity->id }}</p>
        </div>

        <a href="{{ route('admin.audit-logs.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Back to Activity Log
        </a>
    </div>

    <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-800">
        <i class="fa-solid fa-shield-halved mr-1"></i>
        This audit record is read-only. Changes to the underlying business
        record do not change this historical entry.
    </div>

    {{-- Activity Information --}}
    <div class="rounded-xl bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-slate-800">Activity Information</h2>
        </div>

        <div class="grid grid-cols-1 gap-6 p-5 md:grid-cols-2">
            <div>
                <div class="mb-1 text-xs text-slate-500">Date and Time</div>
                <div class="font-semibold text-slate-800">
                    {{ $activity->created_at?->format('F d, Y h:i:s A') ?? '—' }}
                </div>
            </div>

            <div>
                <div class="mb-1 text-xs text-slate-500">Activity ID</div>
                <div class="font-semibold text-slate-800">#{{ $activity->id }}</div>
            </div>

            <div>
                <div class="mb-1 text-xs text-slate-500">User</div>
                <div class="font-semibold text-slate-800">
                    {{ $activity->user_name ?: 'System / Guest' }}
                </div>
                @if ($activity->user_id)
                    <div class="text-xs text-slate-500">User ID: {{ $activity->user_id }}</div>
                @endif
            </div>

            <div>
                <div class="mb-1 text-xs text-slate-500">Role at Time of Activity</div>
                <div class="text-slate-700">{{ $activity->user_role ?: 'Unknown' }}</div>
            </div>

            <div>
                <div class="mb-1 text-xs text-slate-500">Module</div>
                <div class="text-slate-700">{{ $activity->module }}</div>
            </div>

            <div>
                <div class="mb-1 text-xs text-slate-500">Action</div>
                <div class="text-slate-700">
                    {{ \Illuminate\Support\Str::headline($activity->action) }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="mb-1 text-xs text-slate-500">Description</div>
                <div class="font-semibold text-slate-800">{{ $activity->description }}</div>
            </div>

            <div>
                <div class="mb-1 text-xs text-slate-500">Affected Record Type</div>
                <div class="text-slate-700">{{ $activity->subject_type ?: 'Not specified' }}</div>
            </div>

            <div>
                <div class="mb-1 text-xs text-slate-500">Affected Record ID</div>
                <div class="text-slate-700">{{ $activity->subject_id ?: 'Not specified' }}</div>
            </div>
        </div>
    </div>

    {{-- Request Information --}}
    <div class="rounded-xl bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-slate-800">Request Information</h2>
        </div>

        <div class="grid grid-cols-1 gap-6 p-5 md:grid-cols-2">
            <div>
                <div class="mb-1 text-xs text-slate-500">IP Address</div>
                <div class="text-slate-700">{{ $activity->ip_address ?: 'Not recorded' }}</div>
            </div>

            <div>
                <div class="mb-1 text-xs text-slate-500">HTTP Method</div>
                <div class="text-slate-700">{{ $activity->http_method ?: 'Not recorded' }}</div>
            </div>

            <div class="md:col-span-2">
                <div class="mb-1 text-xs text-slate-500">URL</div>
                <div class="break-all text-slate-700">{{ $activity->url ?: 'Not recorded' }}</div>
            </div>

            <div class="md:col-span-2">
                <div class="mb-1 text-xs text-slate-500">User Agent</div>
                <div class="break-words text-slate-700">{{ $activity->user_agent ?: 'Not recorded' }}</div>
            </div>
        </div>
    </div>

    {{-- Old / New Values --}}
    @foreach ([
        'old_values' => 'Previous Values',
        'new_values' => 'New Values',
    ] as $field => $heading)
        <div class="rounded-xl bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-800">{{ $heading }}</h2>
            </div>

            <div class="p-5">
                @php($values = $activity->{$field})

                @if (is_array($values) && count($values))
                    <div class="overflow-x-auto">
                        <table class="w-full border border-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="w-[30%] border-b border-slate-200 px-3 py-2">Field</th>
                                    <th class="border-b border-slate-200 px-3 py-2">Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($values as $key => $value)
                                    <tr>
                                        <th class="bg-slate-50 px-3 py-2 font-medium text-slate-700">
                                            {{ \Illuminate\Support\Str::headline((string) $key) }}
                                        </th>
                                        <td class="break-words px-3 py-2 text-slate-700">
                                            @if (is_array($value))
                                                <pre class="whitespace-pre-wrap text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @elseif (is_bool($value))
                                                {{ $value ? 'Yes' : 'No' }}
                                            @elseif ($value === null)
                                                <span class="text-slate-400">Null</span>
                                            @else
                                                {{ (string) $value }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-slate-500">No values were recorded for this activity.</p>
                @endif
            </div>
        </div>
    @endforeach

</div>
@endsection