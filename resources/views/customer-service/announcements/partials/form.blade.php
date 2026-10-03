@php
    $announcement = $serviceAnnouncement ?? null;
    $isEdit = $announcement !== null;

    $selectedType = old(
        'type',
        $announcement?->type ?? 'General Announcement'
    );

    $currentTitle = old(
        'title',
        $announcement?->title ?? ''
    );

    $currentArea = old(
        'affected_barangay',
        $announcement?->affected_barangay ?? ''
    );

    $currentContent = old(
        'content',
        $announcement?->content ?? ''
    );
@endphp

<div class="space-y-5">

    <div>

        <div class="mb-3 mx-3">

            <h3 class="text-base font-semibold text-gray-900">
                Announcement Information
            </h3>

            <p class="mt-0.5 text-sm text-gray-500">
                Select the announcement type and affected area. The system can suggest a title that you can still edit.
            </p>

        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 mx-3">

            <div>

                <label
                    for="type"
                    class="mb-1 block text-sm font-medium text-gray-700">

                    Announcement Type
                    <span class="text-red-500">*</span>

                </label>

                <select
                    name="type"
                    id="type"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                    <option value="">
                        Select announcement type
                    </option>

                    @foreach ([
                        'General Announcement',
                        'Service Advisory',
                        'Water Interruption',
                        'Scheduled Maintenance',
                        'Emergency Advisory',
                    ] as $type)

                        <option
                            value="{{ $type }}"
                            @selected($selectedType === $type)>

                            {{ $type }}

                        </option>

                    @endforeach

                </select>

                @error('type')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div>

                <label
                    for="affected_barangay"
                    class="mb-1 block text-sm font-medium text-gray-700">

                    Affected Barangay / Area

                </label>

                <input
                    type="text"
                    name="affected_barangay"
                    id="affected_barangay"
                    value="{{ $currentArea }}"
                    placeholder="Leave blank for general service area"
                    class="w-full rounded-lg border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                <p
                    id="areaHelp"
                    class="mt-1 text-xs text-gray-500">

                    Leave blank if the announcement applies to all consumers.

                </p>

                @error('affected_barangay')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div class="lg:col-span-2">

                <div class="flex items-center justify-between gap-3">

                    <label
                        for="title"
                        class="mb-1 block text-sm font-medium text-gray-700">

                        Announcement Title
                        <span class="text-red-500">*</span>

                    </label>

                    <button
                        type="button"
                        id="generateTitleButton"
                        class="mb-1 inline-flex items-center gap-1.5
                               text-xs font-medium text-blue-600
                               transition hover:text-blue-800">

                        <i class="fa-solid fa-wand-magic-sparkles"></i>

                        Suggest Title

                    </button>

                </div>

                <div class="relative">

                    <input
                        type="text"
                        name="title"
                        id="title"
                        value="{{ $currentTitle }}"
                        placeholder="Select a type and enter an affected area"
                        maxlength="255"
                        class="w-full rounded-lg border-gray-300
                               pr-10 focus:border-blue-500
                               focus:ring-blue-500"
                        required>

                    <div
                        id="titleSuggestionIcon"
                        class="pointer-events-none absolute
                               right-3 top-1/2 hidden
                               -translate-y-1/2 text-blue-500">

                        <i class="fa-solid fa-wand-magic-sparkles"></i>

                    </div>

                </div>

                <div class="mt-1 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

                    <p
                        id="titleHelp"
                        class="text-xs text-gray-500">

                        The title should briefly describe the specific announcement.

                    </p>

                    <p class="shrink-0 text-xs text-gray-400">
                        <span id="titleCharacterCount">0</span>/255
                    </p>

                </div>

                @error('title')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

    </div>

    <div class="border-t border-gray-200 pt-5 mx-3">

        <div class="mb-3">

            <h3 class="text-base font-semibold text-gray-900">
                Announcement Content
            </h3>

            <p class="mt-0.5 text-sm text-gray-500">
                Write a clear message explaining the advisory or service update.
            </p>

        </div>

        <div>

            <label
                for="content"
                class="mb-1 block text-sm font-medium text-gray-700">

                Message
                <span class="text-red-500">*</span>

            </label>

            <textarea
                name="content"
                id="content"
                rows="8"
                placeholder="Enter the complete announcement..."
                class="w-full resize-y rounded-lg border-gray-300
                       focus:border-blue-500 focus:ring-blue-500"
                required>{{ $currentContent }}</textarea>

            <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-3">

                <div class="flex items-start gap-2.5">

                    <div
                        class="flex h-7 w-7 shrink-0
                               items-center justify-center
                               rounded-lg bg-white text-blue-600
                               shadow-sm">

                        <i class="fa-solid fa-lightbulb text-xs"></i>

                    </div>

                    <div>

                        <p class="text-xs font-semibold text-slate-700">
                            Writing Guide
                        </p>

                        <p
                            id="contentGuide"
                            class="mt-0.5 text-xs leading-5 text-slate-500">

                            Provide the important information consumers need to know.

                        </p>

                    </div>

                </div>

            </div>

            @error('content')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>

    </div>

    <div
        id="scheduleSection"
        class="border-t border-gray-200 pt-5 mx-3">

        <div class="mb-3">

            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h3 class="text-base font-semibold text-gray-900">
                        Service Schedule
                    </h3>

                    <p
                        id="scheduleDescription"
                        class="mt-0.5 text-sm text-gray-500">

                        Specify the affected service period when applicable.

                    </p>

                </div>

                <span
                    id="scheduleRecommendation"
                    class="hidden w-fit rounded-full
                           bg-blue-50 px-2.5 py-1
                           text-xs font-medium text-blue-700">

                    Recommended

                </span>

            </div>

        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

            <div>

                <label
                    for="start_at"
                    class="mb-1 block text-sm font-medium text-gray-700">

                    Start Date & Time

                </label>

                <div class="relative">

                    <input
                        type="datetime-local"
                        name="start_at"
                        id="start_at"
                        value="{{ old(
                            'start_at',
                            $announcement?->start_at?->format('Y-m-d\TH:i') ?? ''
                        ) }}"
                        class="w-full rounded-lg border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">

                </div>

                <p
                    id="startHelp"
                    class="mt-1 text-xs text-gray-500">

                    When the service activity or advisory begins.

                </p>

                @error('start_at')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div>

                <label
                    for="end_at"
                    class="mb-1 block text-sm font-medium text-gray-700">

                    Expected End Date & Time

                </label>

                <div class="relative">

                    <input
                        type="datetime-local"
                        name="end_at"
                        id="end_at"
                        value="{{ old(
                            'end_at',
                            $announcement?->end_at?->format('Y-m-d\TH:i') ?? ''
                        ) }}"
                        class="w-full rounded-lg border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">

                </div>

                <p
                    id="endHelp"
                    class="mt-1 text-xs text-gray-500">

                    Expected completion or restoration time.

                </p>

                @error('end_at')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

        <div class="mt-3 rounded-lg border border-blue-100 bg-blue-50 p-3">

            <div class="flex items-start gap-2.5">

                <i class="fa-solid fa-circle-info mt-0.5 text-blue-600"></i>

                <p
                    id="scheduleHelp"
                    class="text-xs leading-5 text-blue-700">

                    The schedule is optional. Leave both fields blank for announcements that do not have a specific service period.

                </p>

            </div>

        </div>

    </div>

    <div
        class="flex flex-col-reverse gap-3 border-t
               border-gray-200 pt-5 sm:flex-row
               sm:items-center sm:justify-end mx-3">

        <a
            href="{{ $isEdit
                ? route('customer-service.announcements.show', $announcement)
                : route('customer-service.announcements.index') }}"
            class="inline-flex items-center justify-center
                   rounded-lg border border-gray-300
                   px-5 py-2.5 text-gray-700
                   transition hover:bg-gray-50">

            Cancel

        </a>

        <button
            type="submit"
            class="inline-flex items-center justify-center gap-2
                   rounded-lg bg-blue-600 px-5 py-2.5
                   font-medium text-white transition
                   hover:bg-blue-700">

            <i class="fa-solid fa-floppy-disk"></i>

            {{ $isEdit ? 'Save Changes' : 'Save as Draft' }}

        </button>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeInput = document.getElementById('type');
        const areaInput = document.getElementById('affected_barangay');
        const titleInput = document.getElementById('title');
        const contentInput = document.getElementById('content');
        const titleHelp = document.getElementById('titleHelp');
        const titleIcon = document.getElementById('titleSuggestionIcon');
        const titleCount = document.getElementById('titleCharacterCount');
        const generateTitleButton = document.getElementById('generateTitleButton');
        const contentGuide = document.getElementById('contentGuide');
        const scheduleDescription = document.getElementById('scheduleDescription');
        const scheduleRecommendation = document.getElementById('scheduleRecommendation');
        const scheduleHelp = document.getElementById('scheduleHelp');
        const startHelp = document.getElementById('startHelp');
        const endHelp = document.getElementById('endHelp');

        const isEdit = @json($isEdit);
        const initialTitle = @json($currentTitle);

        let titleWasManuallyEdited = isEdit && initialTitle.trim() !== '';
        let lastGeneratedTitle = '';

        const configurations = {
            'General Announcement': {
                title: 'General Announcement',
                placeholder: 'Enter the important information or update for Sagay Water District consumers...',
                guide: 'Explain the general information, update, reminder, or notice that consumers need to know.',
                scheduleRecommended: false,
                scheduleDescription: 'Add a schedule only if this announcement applies during a specific period.',
                scheduleHelp: 'The schedule is optional for general announcements.',
                startHelp: 'When the announcement or activity becomes applicable.',
                endHelp: 'When the announcement or activity is expected to end.'
            },

            'Service Advisory': {
                title: 'Service Advisory',
                placeholder: 'Describe the current service condition, affected area, expected impact, and any instructions for consumers...',
                guide: 'Describe the service condition, affected consumers, expected impact, and any action consumers should take.',
                scheduleRecommended: true,
                scheduleDescription: 'Specify when the service advisory takes effect and when normal service is expected.',
                scheduleHelp: 'A service schedule is recommended so consumers know when the advisory applies.',
                startHelp: 'When the service advisory begins.',
                endHelp: 'Expected end of the service condition.'
            },

            'Water Interruption': {
                title: 'Water Interruption',
                placeholder: 'Explain the reason for the water interruption, affected area, interruption period, and expected restoration...',
                guide: 'Include the reason for the interruption, affected area, start time, expected restoration, and important consumer instructions.',
                scheduleRecommended: true,
                scheduleDescription: 'Specify the interruption period and expected water service restoration.',
                scheduleHelp: 'Start and expected end times are strongly recommended for water interruption announcements.',
                startHelp: 'When water service is expected to be interrupted.',
                endHelp: 'Expected water service restoration time.'
            },

            'Scheduled Maintenance': {
                title: 'Scheduled Maintenance',
                placeholder: 'Describe the maintenance activity, affected location, service impact, schedule, and expected completion...',
                guide: 'Explain the planned maintenance work, location, possible service impact, and expected completion.',
                scheduleRecommended: true,
                scheduleDescription: 'Specify the scheduled maintenance period.',
                scheduleHelp: 'Start and expected end times are recommended for scheduled maintenance.',
                startHelp: 'Scheduled start of maintenance work.',
                endHelp: 'Expected completion of maintenance work.'
            },

            'Emergency Advisory': {
                title: 'Emergency Advisory',
                placeholder: 'Describe the urgent service condition, affected area, immediate impact, and instructions consumers should follow...',
                guide: 'Clearly state the urgent condition, affected area, immediate service impact, and any safety or service instructions.',
                scheduleRecommended: true,
                scheduleDescription: 'Provide the known emergency period when timing information is available.',
                scheduleHelp: 'Provide the best available schedule. The expected end may be updated later if restoration time is uncertain.',
                startHelp: 'When the emergency condition started or is expected to begin.',
                endHelp: 'Expected resolution time, if currently known.'
            }
        };

        function getConfiguration() {
            return configurations[typeInput.value] ?? {
                title: '',
                placeholder: 'Enter the complete announcement...',
                guide: 'Provide the important information consumers need to know.',
                scheduleRecommended: false,
                scheduleDescription: 'Specify the affected service period when applicable.',
                scheduleHelp: 'The schedule is optional. Leave both fields blank when there is no specific service period.',
                startHelp: 'When the service activity or advisory begins.',
                endHelp: 'Expected completion or restoration time.'
            };
        }

        function buildSuggestedTitle() {
            const config = getConfiguration();
            const area = areaInput.value.trim();

            if (!config.title) {
                return '';
            }

            if (area !== '') {
                return `${config.title} - ${area}`;
            }

            return config.title;
        }

        function updateTitleCounter() {
            titleCount.textContent = titleInput.value.length;
        }

        function showGeneratedState() {
            titleIcon.classList.remove('hidden');

            titleHelp.textContent =
                'Suggested from the announcement type and affected area. You can still edit this title.';
        }

        function showManualState() {
            titleIcon.classList.add('hidden');

            titleHelp.textContent =
                'Custom title. Use Suggest Title if you want to regenerate it from the type and affected area.';
        }

        function suggestTitle(force = false) {
            const suggestedTitle = buildSuggestedTitle();

            if (!suggestedTitle) {
                return;
            }

            const currentTitle = titleInput.value.trim();

            const canAutomaticallyUpdate =
                force ||
                currentTitle === '' ||
                currentTitle === lastGeneratedTitle ||
                !titleWasManuallyEdited;

            if (!canAutomaticallyUpdate) {
                return;
            }

            titleInput.value = suggestedTitle;
            lastGeneratedTitle = suggestedTitle;
            titleWasManuallyEdited = false;

            showGeneratedState();
            updateTitleCounter();
        }

        function updateTypeGuidance() {
            const config = getConfiguration();

            contentInput.placeholder = config.placeholder;
            contentGuide.textContent = config.guide;
            scheduleDescription.textContent = config.scheduleDescription;
            scheduleHelp.textContent = config.scheduleHelp;
            startHelp.textContent = config.startHelp;
            endHelp.textContent = config.endHelp;

            if (config.scheduleRecommended) {
                scheduleRecommendation.classList.remove('hidden');
            } else {
                scheduleRecommendation.classList.add('hidden');
            }
        }

        typeInput.addEventListener('change', function () {
            updateTypeGuidance();
            suggestTitle(false);
        });

        areaInput.addEventListener('input', function () {
            suggestTitle(false);
        });

        titleInput.addEventListener('input', function () {
            const currentTitle = titleInput.value.trim();

            if (
                currentTitle !== '' &&
                currentTitle !== lastGeneratedTitle
            ) {
                titleWasManuallyEdited = true;
                showManualState();
            }

            if (currentTitle === '') {
                titleWasManuallyEdited = false;
            }

            updateTitleCounter();
        });

        generateTitleButton.addEventListener('click', function () {
            titleWasManuallyEdited = false;
            suggestTitle(true);
            titleInput.focus();
        });

        updateTypeGuidance();
        updateTitleCounter();

        if (!isEdit && titleInput.value.trim() === '') {
            suggestTitle(false);
        }

        if (isEdit && titleInput.value.trim() !== '') {
            showManualState();
        }
    });
</script>
