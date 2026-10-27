@extends('layouts.report')

@section('title', 'Service Announcements Report - iSWD')

@section('report-title', 'Service Announcements Report')

@section('report-subtitle', 'Customer Service — Announcement Listing')

@section('report-period')
    @if (request()->filled('search') || request()->filled('type') || request()->filled('status'))
        Filtered announcement listing
    @else
        All service announcements
    @endif
@endsection

@section('report-content')

@php
    $announcementCount = $announcements->count();
    $publishedCount = $announcements->where('status', 'Published')->count();
    $draftCount = $announcements->where('status', 'Draft')->count();
    $archivedCount = $announcements->where('status', 'Archived')->count();
@endphp

<style>
    .announcement-report-actions {
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
        margin: 0 0 1rem;
    }

    .announcement-report-button {
        display: inline-flex;
        align-items: center;
        padding: .55rem .9rem;
        border: 1px solid #cbd5e1;
        border-radius: .4rem;
        background: #fff;
        color: #0f172a;
        font-size: .875rem;
        text-decoration: none;
        cursor: pointer;
    }

    .announcement-report-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .65rem;
        margin: 1rem 0 1.25rem;
    }

    .announcement-report-stat {
        border: 1px solid #dbe3ec;
        border-radius: .4rem;
        padding: .7rem .8rem;
        background: #f8fafc;
    }

    .announcement-report-stat-label {
        color: #475569;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .announcement-report-stat-value {
        margin-top: .2rem;
        color: #0f172a;
        font-size: 1.35rem;
        font-weight: 700;
    }

    .announcement-report-filters {
        margin: 0 0 1rem;
        padding: .7rem .85rem;
        border-left: 3px solid #0284c7;
        background: #f1f5f9;
        color: #334155;
        font-size: .82rem;
    }

    .announcement-report-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 9pt;
    }

    .announcement-report-table th,
    .announcement-report-table td {
        padding: .5rem .45rem;
        border: 1px solid #cbd5e1;
        text-align: left;
        vertical-align: top;
        overflow-wrap: anywhere;
    }

    .announcement-report-table thead th {
        background: #eaf3f8;
        color: #0f172a;
        font-weight: 700;
    }

    .announcement-report-title {
        font-weight: 700;
        color: #0f172a;
    }

    .announcement-report-type {
        margin-top: .2rem;
        color: #475569;
        font-size: 8pt;
    }

    .announcement-report-content {
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .announcement-report-empty {
        padding: 1.25rem;
        text-align: center !important;
        color: #64748b;
    }

    @media (max-width: 700px) {
        .announcement-report-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .announcement-report-table {
            font-size: 8pt;
        }

        .announcement-report-table th,
        .announcement-report-table td {
            padding: .35rem .3rem;
        }
    }

    @media print {
        .announcement-report-actions {
            display: none !important;
        }

        .announcement-report-summary {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .announcement-report-table {
            font-size: 8.5pt;
        }

        .announcement-report-table thead {
            display: table-header-group;
        }

        .announcement-report-table tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .announcement-report-stat,
        .announcement-report-filters,
        .announcement-report-table thead th {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

@if (request()->filled('search') || request()->filled('type') || request()->filled('status'))
    <div class="announcement-report-filters">
        <strong>Applied filters:</strong>
        @if (request()->filled('search'))
            Search: “{{ request('search') }}”
        @endif
        @if (request()->filled('type'))
            @if (request()->filled('search')) · @endif
            Type: {{ request('type') }}
        @endif
        @if (request()->filled('status'))
            @if (request()->filled('search') || request()->filled('type')) · @endif
            Status: {{ request('status') }}
        @endif
    </div>
@endif



<table class="announcement-report-table">
    <thead>
        <tr>
            <th style="width: 19%;">Announcement / Type</th>
            <th style="width: 14%;">Affected Area</th>
            <th style="width: 9%;">Status</th>
            <th style="width: 13%;">Published Date</th>
            <th style="width: 45%;">Content</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($announcements as $announcement)
            <tr>
                <td>
                    <div class="announcement-report-title">{{ $announcement->title }}</div>
                    <div class="announcement-report-type">{{ $announcement->type ?: '—' }}</div>
                </td>
                <td>{{ $announcement->affected_barangay ?: 'General Service Area' }}</td>
                <td>{{ $announcement->status ?: '—' }}</td>
                <td>
                    {{ $announcement->published_at ? $announcement->published_at->format('M d, Y g:i A') : '—' }}
                </td>
                <td class="announcement-report-content">{{ $announcement->content ?: '—' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="announcement-report-empty">
                    No announcements match the selected filters.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
