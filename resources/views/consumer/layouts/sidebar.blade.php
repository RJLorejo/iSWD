<aside
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed left-0 top-0 z-50 h-screen bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700 text-white transition-all duration-300">

    <div class="h-20 flex items-center justify-center border-b border-sky-600">

        <div x-show="!sidebarMini">

            <h1 class="text-2xl font-bold">

                iSWD

            </h1>

            <p class="text-xs text-sky-200 text-center">

                Consumer Panel

            </p>

        </div>

        <i x-show="sidebarMini" class="fa-solid fa-droplet text-3xl"></i>

    </div>

    <div class="p-5 border-b border-sky-600">

        <div class="flex items-center gap-3">

            <img class="w-12 h-12 rounded-full"
                src="https://ui-avatars.com/api/?background=ffffff&color=2563eb&name={{ urlencode(auth()->user()->name) }}">

            <div x-show="!sidebarMini">

                <div class="font-semibold">

                    {{ auth()->user()->name }}

                </div>

                <div class="text-xs text-sky-200">

                    Consumer

                </div>

            </div>

        </div>

    </div>

    <nav class="mt-5 px-3 space-y-2">

        <a href="{{ route('consumer.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20">

            <i class="fa-solid fa-chart-line w-6"></i>

            <span x-show="!sidebarMini">Dashboard</span>

        </a>

        <a href="{{ route('consumer.complaints.create') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20">

            <i class="fa-solid fa-file-circle-plus w-6"></i>

            <span x-show="!sidebarMini">Submit Complaint</span>

        </a>

        <a href="{{ route('consumer.complaints.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20">

            <i class="fa-solid fa-file-lines w-6"></i>

            <span x-show="!sidebarMini">My Complaints</span>

        </a>


        <a href="{{ route('consumer.ai.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20">

            <i class="fa-solid fa-robot w-6"></i>

            <span x-show="!sidebarMini">Ai Assistant</span>

        </a>

        <a href="{{ route('consumer.announcements.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20">

            <i class="fa-solid fa-bullhorn w-6"></i>

            <span x-show="!sidebarMini">Announcements</span>

        </a>

    </nav>

</aside>

<div x-show="sidebarOpen" @click="sidebarOpen=false" class="fixed inset-0 bg-black/50 lg:hidden">
</div>
