@extends('maintenance-manager.layouts.app')

@section('title', 'Plumber Service Areas')

@section('content')

    <div x-data="{
        areaModal: false,
        assignmentModal: false,

        editingArea: false,
        areaId: null,
        areaName: '',
        areaDescription: '',

        plumberId: null,
        plumberName: '',
        plumberArea: '',

        openCreateArea() {
            this.editingArea = false;
            this.areaId = null;
            this.areaName = '';
            this.areaDescription = '';
            this.areaModal = true;
        },

        openEditArea(
            id,
            name,
            description
        ) {
            this.editingArea = true;
            this.areaId = id;
            this.areaName = name ?? '';
            this.areaDescription = description ?? '';
            this.areaModal = true;
        },

        openAssignment(
            id,
            name,
            areaId
        ) {
            this.plumberId = id;
            this.plumberName = name;
            this.plumberArea = areaId ?? '';
            this.assignmentModal = true;
        }
    }" class="mx-auto max-w-7xl space-y-6">

        {{-- Header --}}
        <div
            class="flex flex-col gap-4
                   sm:flex-row
                   sm:items-center
                   sm:justify-between">

            <div>

                <p class="text-sm font-medium
                           text-sky-700">
                    Maintenance Management
                </p>

                <h1
                    class="mt-1 text-2xl
                           font-bold text-slate-900
                           sm:text-3xl">
                    Plumber Service Areas
                </h1>

                <p class="mt-1 max-w-2xl
                           text-sm text-slate-500">
                    Manage permanent service areas and
                    plumber area assignments.
                </p>

            </div>

            <button type="button" @click="openCreateArea()"
                class="inline-flex items-center
                       justify-center gap-2
                       rounded-xl bg-sky-700
                       px-4 py-2.5
                       text-sm font-semibold
                       text-white shadow-sm
                       transition
                       hover:bg-sky-800">

                <i class="fas fa-plus"></i>

                Add Service Area

            </button>

        </div>


        {{-- Success --}}
        @if (session('success'))
            <div
                class="rounded-2xl border
                       border-emerald-200
                       bg-emerald-50
                       px-5 py-4
                       text-sm font-medium
                       text-emerald-800">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-check
                               mt-0.5"></i>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>

            </div>
        @endif


        {{-- Errors --}}
        @if ($errors->any())

            <div
                class="rounded-2xl border
                       border-red-200
                       bg-red-50
                       px-5 py-4">

                <div class="flex gap-3">

                    <i class="fas fa-circle-exclamation
                               mt-0.5 text-red-600"></i>

                    <div>

                        <p class="font-semibold
                                   text-red-800">
                            Please correct the following:
                        </p>

                        <ul
                            class="mt-2 list-disc
                                   space-y-1 pl-5
                                   text-sm text-red-700">

                            @foreach ($errors->all() as $error)
                                <li>
                                    {{ $error }}
                                </li>
                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif


        {{-- ===================================================== --}}
        {{-- SERVICE AREAS --}}
        {{-- ===================================================== --}}

        <section
            class="overflow-hidden rounded-2xl
                   border border-slate-200
                   bg-white shadow-sm">

            <div
                class="flex items-center
                       justify-between
                       border-b border-slate-200
                       px-5 py-4">

                <div>

                    <h2 class="font-bold
                               text-slate-900">
                        Service Areas
                    </h2>

                    <p class="mt-0.5
                               text-xs text-slate-500">
                        Permanent maintenance coverage areas.
                    </p>

                </div>

                <span
                    class="rounded-full
                           bg-slate-100
                           px-3 py-1
                           text-xs font-semibold
                           text-slate-600">
                    {{ $serviceAreas->count() }}
                    {{ Str::plural('area', $serviceAreas->count()) }}
                </span>

            </div>


            {{-- Desktop --}}
            <div class="hidden overflow-x-auto md:block">

                <table class="min-w-full
                           divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-bold uppercase
                                       tracking-wide
                                       text-slate-500">
                                Service Area
                            </th>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-bold uppercase
                                       tracking-wide
                                       text-slate-500">
                                Plumbers
                            </th>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-bold uppercase
                                       tracking-wide
                                       text-slate-500">
                                Status
                            </th>

                            <th
                                class="px-5 py-3
                                       text-right text-xs
                                       font-bold uppercase
                                       tracking-wide
                                       text-slate-500">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y
                               divide-slate-100">

                        @forelse ($serviceAreas as $area)
                            <tr class="transition
                                       hover:bg-slate-50">

                                <td class="px-5 py-4">

                                    <div
                                        class="flex
                                               items-start
                                               gap-3">

                                        <div
                                            class="flex h-10 w-10
                                                   shrink-0
                                                   items-center
                                                   justify-center
                                                   rounded-xl
                                                   bg-sky-50
                                                   text-sky-700">
                                            <i
                                                class="fas
                                                       fa-map-location-dot"></i>
                                        </div>

                                        <div>

                                            <p
                                                class="font-semibold
                                                       text-slate-900">
                                                {{ $area->name }}
                                            </p>

                                            @if ($area->description)
                                                <p
                                                    class="mt-1
                                                           max-w-md
                                                           text-xs
                                                           text-slate-500">
                                                    {{ $area->description }}
                                                </p>
                                            @else
                                                <p
                                                    class="mt-1
                                                           text-xs
                                                           text-slate-400">
                                                    No description
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                </td>

                                <td class="px-5 py-4
                                           text-sm text-slate-600">

                                    <span
                                        class="inline-flex
                                               items-center gap-2">

                                        <i
                                            class="fas fa-users
                                                   text-slate-400"></i>

                                        {{ $area->plumbers_count }}

                                    </span>

                                </td>

                                <td class="px-5 py-4">

                                    @if ($area->is_active)
                                        <span
                                            class="inline-flex
                                                   items-center
                                                   rounded-full
                                                   bg-emerald-50
                                                   px-2.5 py-1
                                                   text-xs
                                                   font-semibold
                                                   text-emerald-700">
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex
                                                   items-center
                                                   rounded-full
                                                   bg-slate-100
                                                   px-2.5 py-1
                                                   text-xs
                                                   font-semibold
                                                   text-slate-600">
                                            Inactive
                                        </span>
                                    @endif

                                </td>

                                <td class="px-5 py-4">

                                    <div
                                        class="flex
                                               justify-end
                                               gap-2">

                                        <button type="button"
                                            @click='openEditArea(
                                                @json($area->id),
                                                @json($area->name),
                                                @json($area->description)

                                            )'
                                            class="flex h-9 w-9
                                                   items-center
                                                   justify-center
                                                   rounded-lg
                                                   border
                                                   border-slate-200
                                                   text-slate-600
                                                   transition
                                                   hover:border-sky-200
                                                   hover:bg-sky-50
                                                   hover:text-sky-700"
                                            title="Edit service area">
                                            <i
                                                class="fas
                                                       fa-pen"></i>
                                        </button>

                                        <form method="POST"
                                            action="{{ route('maintenance-manager.service-areas.toggle-status', $area) }}">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                onclick="return confirm(
                                                    '{{ $area->is_active ? 'Deactivate this service area?' : 'Activate this service area?' }}'
                                                )"
                                                class="flex h-9 w-9
                                                       items-center
                                                       justify-center
                                                       rounded-lg
                                                       border
                                                       border-slate-200
                                                       text-slate-600
                                                       transition
                                                       hover:bg-slate-50"
                                                title="{{ $area->is_active ? 'Deactivate' : 'Activate' }}">

                                                <i
                                                    class="fas
                                                        {{ $area->is_active ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off text-slate-400' }}"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4"
                                    class="px-6 py-12
                                           text-center">

                                    <div
                                        class="mx-auto flex
                                               h-12 w-12
                                               items-center
                                               justify-center
                                               rounded-2xl
                                               bg-slate-100
                                               text-slate-400">
                                        <i
                                            class="fas
                                                   fa-map-location-dot"></i>
                                    </div>

                                    <p
                                        class="mt-3
                                               font-semibold
                                               text-slate-700">
                                        No service areas yet
                                    </p>

                                    <p
                                        class="mt-1
                                               text-sm
                                               text-slate-500">
                                        Add the first permanent
                                        plumber service area.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Mobile --}}
            <div class="divide-y
                       divide-slate-100 md:hidden">

                @forelse ($serviceAreas as $area)
                    <div class="p-4">

                        <div class="flex items-start
                                   justify-between gap-3">

                            <div class="min-w-0">

                                <div class="flex
                                           items-center gap-2">

                                    <p class="font-semibold
                                               text-slate-900">
                                        {{ $area->name }}
                                    </p>

                                    @if ($area->is_active)
                                        <span
                                            class="rounded-full
                                                   bg-emerald-50
                                                   px-2 py-0.5
                                                   text-[10px]
                                                   font-bold
                                                   text-emerald-700">
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="rounded-full
                                                   bg-slate-100
                                                   px-2 py-0.5
                                                   text-[10px]
                                                   font-bold
                                                   text-slate-600">
                                            Inactive
                                        </span>
                                    @endif

                                </div>

                                @if ($area->description)
                                    <p
                                        class="mt-1
                                               text-xs
                                               text-slate-500">
                                        {{ $area->description }}
                                    </p>
                                @endif

                                <p
                                    class="mt-2 text-xs
                                           font-medium
                                           text-slate-500">
                                    {{ $area->plumbers_count }}
                                    assigned
                                    {{ Str::plural('plumber', $area->plumbers_count) }}
                                </p>

                            </div>

                            <button type="button"
                                @click='openEditArea(
                                    @json($area->id),
                                    @json($area->name),
                                    @json($area->description)

                                )'
                                class="flex h-9 w-9
                                       shrink-0
                                       items-center
                                       justify-center
                                       rounded-lg
                                       border
                                       border-slate-200
                                       text-slate-600">
                                <i class="fas fa-pen"></i>
                            </button>

                        </div>

                        <form method="POST" action="{{ route('maintenance-manager.service-areas.toggle-status', $area) }}"
                            class="mt-3">

                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                onclick="return confirm(
                                    '{{ $area->is_active ? 'Deactivate this service area?' : 'Activate this service area?' }}'
                                )"
                                class="text-xs font-semibold
                                       {{ $area->is_active ? 'text-slate-500' : 'text-emerald-700' }}">

                                {{ $area->is_active ? 'Deactivate' : 'Activate' }}

                            </button>

                        </form>

                    </div>

                @empty

                    <div class="px-5 py-10
                               text-center">

                        <p class="font-semibold
                                   text-slate-700">
                            No service areas yet
                        </p>

                        <p class="mt-1 text-sm
                                   text-slate-500">
                            Add your first service area.
                        </p>

                    </div>
                @endforelse

            </div>

        </section>


        {{-- ===================================================== --}}
        {{-- PLUMBER ASSIGNMENTS --}}
        {{-- ===================================================== --}}

        <section
            class="overflow-hidden rounded-2xl
                   border border-slate-200
                   bg-white shadow-sm">

            <div class="border-b border-slate-200
                       px-5 py-4">

                <h2 class="font-bold
                           text-slate-900">
                    Plumber Assignments
                </h2>

                <p class="mt-0.5
                           text-xs text-slate-500">
                    Set each plumber's permanent
                    service area.
                </p>

            </div>


            {{-- Desktop --}}
            <div class="hidden overflow-x-auto md:block">

                <table class="min-w-full
                           divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-bold uppercase
                                       tracking-wide
                                       text-slate-500">
                                Plumber
                            </th>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-bold uppercase
                                       tracking-wide
                                       text-slate-500">
                                Position
                            </th>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-bold uppercase
                                       tracking-wide
                                       text-slate-500">
                                Permanent Service Area
                            </th>

                            <th
                                class="px-5 py-3
                                       text-right text-xs
                                       font-bold uppercase
                                       tracking-wide
                                       text-slate-500">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y
                               divide-slate-100">

                        @forelse ($plumbers as $plumber)
                            <tr class="transition
                                       hover:bg-slate-50">

                                <td class="px-5 py-4">

                                    <div
                                        class="flex
                                               items-center
                                               gap-3">

                                        <img src="{{ $plumber->avatar_url }}" alt="{{ $plumber->full_name }}"
                                            class="h-10 w-10
                                                   rounded-xl
                                                   object-cover">

                                        <div>

                                            <p
                                                class="font-semibold
                                                       text-slate-900">
                                                {{ $plumber->full_name }}
                                            </p>

                                            @if ($plumber->employee_id)
                                                <p
                                                    class="text-xs
                                                           text-slate-500">
                                                    {{ $plumber->employee_id }}
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                </td>

                                <td
                                    class="px-5 py-4
                                           text-sm
                                           text-slate-600">
                                    {{ $plumber->position?->name ?? 'Maintenance Technician' }}
                                </td>

                                <td class="px-5 py-4">

                                    @if ($plumber->serviceArea)
                                        <span
                                            class="inline-flex
                                                   items-center gap-2
                                                   rounded-lg
                                                   bg-sky-50
                                                   px-3 py-1.5
                                                   text-sm
                                                   font-semibold
                                                   text-sky-700">

                                            <i
                                                class="fas
                                                       fa-location-dot"></i>

                                            {{ $plumber->serviceArea->name }}

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex
                                                   rounded-lg
                                                   bg-amber-50
                                                   px-3 py-1.5
                                                   text-sm
                                                   font-semibold
                                                   text-amber-700">
                                            Not Assigned
                                        </span>
                                    @endif

                                </td>

                                <td class="px-5 py-4">

                                    <div class="flex justify-end">

                                        <button type="button"
                                            @click='openAssignment(
                                                @json($plumber->id),
                                                @json($plumber->full_name),
                                                @json($plumber->service_area_id)
                                            )'
                                            class="inline-flex
                                                   items-center gap-2
                                                   rounded-lg
                                                   border
                                                   border-slate-200
                                                   bg-white
                                                   px-3 py-2
                                                   text-xs
                                                   font-semibold
                                                   text-slate-700
                                                   transition
                                                   hover:border-sky-200
                                                   hover:bg-sky-50
                                                   hover:text-sky-700">

                                            <i
                                                class="fas
                                                       fa-location-dot"></i>

                                            {{ $plumber->serviceArea ? 'Change Area' : 'Assign Area' }}

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4"
                                    class="px-6 py-12
                                           text-center
                                           text-sm
                                           text-slate-500">
                                    No active maintenance
                                    technicians found.
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Mobile --}}
            <div class="divide-y
                       divide-slate-100 md:hidden">

                @forelse ($plumbers as $plumber)
                    <div class="p-4">

                        <div class="flex
                                   items-center gap-3">

                            <img src="{{ $plumber->avatar_url }}" alt="{{ $plumber->full_name }}"
                                class="h-10 w-10
                                       rounded-xl
                                       object-cover">

                            <div class="min-w-0">

                                <p
                                    class="truncate
                                           font-semibold
                                           text-slate-900">
                                    {{ $plumber->full_name }}
                                </p>

                                <p
                                    class="truncate
                                           text-xs
                                           text-slate-500">
                                    {{ $plumber->position?->name ?? 'Maintenance Technician' }}
                                </p>

                            </div>

                        </div>

                        <div
                            class="mt-4 flex
                                   items-center
                                   justify-between gap-3">

                            <div>

                                <p
                                    class="text-[10px]
                                           font-bold uppercase
                                           tracking-wide
                                           text-slate-400">
                                    Service Area
                                </p>

                                <p
                                    class="mt-1 text-sm
                                           font-semibold
                                           {{ $plumber->serviceArea ? 'text-sky-700' : 'text-amber-700' }}">
                                    {{ $plumber->serviceArea?->name ?? 'Not Assigned' }}
                                </p>

                            </div>

                            <button type="button"
                                @click='openAssignment(
                                    @json($plumber->id),
                                    @json($plumber->full_name),
                                    @json($plumber->service_area_id)
                                )'
                                class="rounded-lg
                                       border
                                       border-slate-200
                                       px-3 py-2
                                       text-xs
                                       font-semibold
                                       text-slate-700">
                                {{ $plumber->serviceArea ? 'Change' : 'Assign' }}
                            </button>

                        </div>

                    </div>

                @empty

                    <div
                        class="px-5 py-10
                               text-center
                               text-sm
                               text-slate-500">
                        No active maintenance
                        technicians found.
                    </div>
                @endforelse

            </div>

        </section>


        {{-- ===================================================== --}}
        {{-- AREA CREATE / EDIT MODAL --}}
        {{-- ===================================================== --}}

        <div x-cloak x-show="areaModal" x-transition.opacity @keydown.escape.window="areaModal = false"
            class="fixed inset-0 z-[100]
                   flex items-center
                   justify-center p-4">

            <div class="absolute inset-0
                       bg-slate-950/50" @click="areaModal = false"></div>

            <div x-transition
                class="relative z-10
                       w-full max-w-lg
                       overflow-hidden
                       rounded-2xl bg-white
                       shadow-2xl">

                <form method="POST"
                    :action="editingArea
                        ?
                        '{{ url('/maintenance-manager/service-areas') }}/' + areaId :
                        '{{ route('maintenance-manager.service-areas.store') }}'"
                    @submit="
        if (editingArea && !confirm('Are you sure you want to save these changes to this service area?')) {
            $event.preventDefault();
        }
    ">

                    @csrf

                    <template x-if="editingArea">

                        <input type="hidden" name="_method" value="PUT">

                    </template>

                    <div
                        class="flex items-center
                               justify-between
                               border-b
                               border-slate-200
                               px-5 py-4">

                        <div>

                            <h3 class="font-bold
                                       text-slate-900"
                                x-text="editingArea
                                    ? 'Edit Service Area'
                                    : 'Add Service Area'">
                            </h3>

                            <p
                                class="mt-0.5
                                       text-xs
                                       text-slate-500">
                                Permanent plumber
                                coverage area.
                            </p>

                        </div>

                        <button type="button" @click="areaModal = false"
                            class="flex h-9 w-9
                                   items-center
                                   justify-center
                                   rounded-lg
                                   text-slate-400
                                   hover:bg-slate-100
                                   hover:text-slate-700">
                            <i class="fas fa-xmark"></i>
                        </button>

                    </div>


                    <div class="space-y-4 p-5">

                        <div>

                            <label
                                class="mb-1.5 block
                                       text-sm font-semibold
                                       text-slate-700">
                                Service Area Name
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="name" x-model="areaName" required maxlength="255"
                                placeholder="Example: Area 1"
                                class="w-full rounded-xl
                                       border-slate-300
                                       text-sm
                                       shadow-sm
                                       focus:border-sky-500
                                       focus:ring-sky-500">

                        </div>


                        <div>

                            <label
                                class="mb-1.5 block
                                       text-sm font-semibold
                                       text-slate-700">
                                Description
                            </label>

                            <textarea name="description" x-model="areaDescription" rows="3" maxlength="1000"
                                placeholder="Optional description or coverage information"
                                class="w-full rounded-xl
                                       border-slate-300
                                       text-sm
                                       shadow-sm
                                       focus:border-sky-500
                                       focus:ring-sky-500"></textarea>

                        </div>

                    </div>


                    <div
                        class="flex justify-end gap-3
                               border-t border-slate-200
                               bg-slate-50
                               px-5 py-4">

                        <button type="button" @click="areaModal = false"
                            class="rounded-xl
                                   border border-slate-300
                                   bg-white
                                   px-4 py-2.5
                                   text-sm font-semibold
                                   text-slate-700
                                   hover:bg-slate-50">
                            Cancel
                        </button>

                        <button type="submit"
                            class="inline-flex
                                   items-center gap-2
                                   rounded-xl
                                   bg-sky-700
                                   px-4 py-2.5
                                   text-sm font-semibold
                                   text-white
                                   hover:bg-sky-800">

                            <i class="fas fa-check"></i>

                            <span
                                x-text="editingArea
                                    ? 'Save Changes'
                                    : 'Create Area'"></span>

                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- PLUMBER ASSIGNMENT MODAL --}}
        {{-- ===================================================== --}}

        <div x-cloak x-show="assignmentModal" x-transition.opacity
            @keydown.escape.window="
                assignmentModal = false
            "
            class="fixed inset-0 z-[100]
                   flex items-center
                   justify-center p-4">

            <div class="absolute inset-0
                       bg-slate-950/50"
                @click="
                    assignmentModal = false
                "></div>

            <div x-transition
                class="relative z-10
                       w-full max-w-md
                       overflow-hidden
                       rounded-2xl bg-white
                       shadow-2xl">

                <form method="POST"
                    :action="'{{ url('/maintenance-manager/service-areas/plumbers') }}/' +
                    plumberId
                        +
                        '/assign'"
                    onsubmit="return confirm(
    'Save this permanent service area assignment?'
)">

                    @csrf
                    @method('PATCH')

                    <div
                        class="border-b
                               border-slate-200
                               px-5 py-4">

                        <div
                            class="flex items-start
                                   justify-between
                                   gap-3">

                            <div>

                                <h3 class="font-bold
                                           text-slate-900">
                                    Assign Service Area
                                </h3>

                                <p class="mt-1
                                           text-sm
                                           text-slate-500"
                                    x-text="plumberName"></p>

                            </div>

                            <button type="button"
                                @click="
                                    assignmentModal = false
                                "
                                class="flex h-9 w-9
                                       items-center
                                       justify-center
                                       rounded-lg
                                       text-slate-400
                                       hover:bg-slate-100
                                       hover:text-slate-700">
                                <i class="fas
                                           fa-xmark"></i>
                            </button>

                        </div>

                    </div>


                    <div class="p-5">

                        <label
                            class="mb-1.5 block
                                   text-sm font-semibold
                                   text-slate-700">
                            Permanent Service Area
                        </label>

                        <select name="service_area_id" x-model="plumberArea"
                            class="w-full rounded-xl
                                   border-slate-300
                                   text-sm shadow-sm
                                   focus:border-sky-500
                                   focus:ring-sky-500">

                            <option value="">
                                Not Assigned
                            </option>

                            @foreach ($serviceAreas->where('is_active', true) as $area)
                                <option value="{{ $area->id }}">
                                    {{ $area->name }}
                                </option>
                            @endforeach

                        </select>

                        <p
                            class="mt-2 text-xs
                                   leading-5
                                   text-slate-500">
                            This is the plumber's normal
                            permanent coverage area.
                            The Maintenance Manager can
                            still assign the plumber to
                            complaints outside this area.
                        </p>

                    </div>


                    <div
                        class="flex justify-end gap-3
                               border-t border-slate-200
                               bg-slate-50
                               px-5 py-4">

                        <button type="button"
                            @click="
                                assignmentModal = false
                            "
                            class="rounded-xl
                                   border border-slate-300
                                   bg-white
                                   px-4 py-2.5
                                   text-sm font-semibold
                                   text-slate-700">
                            Cancel
                        </button>

                        <button type="submit"
                            class="inline-flex
                                   items-center gap-2
                                   rounded-xl
                                   bg-sky-700
                                   px-4 py-2.5
                                   text-sm font-semibold
                                   text-white
                                   hover:bg-sky-800">

                            <i class="fas
                                       fa-location-dot"></i>

                            Save Assignment

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
