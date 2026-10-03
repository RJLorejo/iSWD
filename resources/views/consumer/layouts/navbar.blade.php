<header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur">

    <div
        class="mx-auto flex h-16 w-full max-w-[1600px] items-center justify-between gap-4 px-4 sm:px-6 lg:h-[72px] lg:px-8">

        <div class="flex min-w-0 items-center gap-3">

            <button type="button" @click="sidebarMini = !sidebarMini"
                class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 lg:flex"
                aria-label="Toggle sidebar">

                <i class="fas fa-bars"></i>

            </button>

            <button type="button" @click="sidebarOpen = true"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 lg:hidden"
                aria-label="Open navigation">

                <i class="fas fa-bars"></i>

            </button>

            <div class="min-w-0">

                <p class="truncate text-base font-bold text-slate-900 sm:text-lg">
                    @hasSection('title')
                        @yield('title')
                    @else
                        Consumer Portal
                    @endif
                </p>

                <div class="mt-0.5 hidden items-center gap-1.5 text-xs text-slate-400 sm:flex">

                    <i class="fas fa-building text-[10px]"></i>

                    <span>
                        Sagay Water District
                    </span>

                </div>

            </div>

        </div>

        <div class="flex items-center gap-2 sm:gap-3">

            <a href="{{ route('consumer.announcements.index') }}"
                class="relative flex h-10 w-10 items-center justify-center
           rounded-xl text-slate-500 transition
           hover:bg-slate-100 hover:text-slate-800"
                aria-label="Service announcements"
                title="{{ ($newAnnouncementCount ?? 0) > 0
                    ? $newAnnouncementCount . ' unread announcement' . ($newAnnouncementCount > 1 ? 's' : '')
                    : 'Service Announcements' }}">

                <i class="{{ ($newAnnouncementCount ?? 0) > 0 ? 'fas' : 'far' }} fa-bell">
                </i>

                @if (($newAnnouncementCount ?? 0) > 0)
                    <span
                        class="absolute -right-0.5 -top-0.5
                   flex min-w-[18px] h-[18px]
                   items-center justify-center
                   rounded-full bg-red-500
                   px-1 text-[10px] font-bold
                   leading-none text-white
                   ring-2 ring-white">

                        {{ $newAnnouncementCount > 99 ? '99+' : $newAnnouncementCount }}

                    </span>
                @endif

            </a>

            <div x-data="{ open: false }" class="relative">

                <button type="button" @click="open = !open" :aria-expanded="open"
                    class="flex items-center gap-2 rounded-xl p-1.5 transition hover:bg-slate-100 sm:gap-3 sm:pr-3">

                    <img class="h-9 w-9 shrink-0 rounded-xl object-cover ring-1 ring-slate-200 sm:h-10 sm:w-10"
                        src="{{ auth()->user()->avatar_url }}"
                        alt="{{ auth()->user()->full_name ?? auth()->user()->name }}">

                    <div class="hidden max-w-[160px] text-left md:block">

                        <p class="truncate text-sm font-semibold text-slate-800">
                            {{ auth()->user()->full_name ?? auth()->user()->name }}
                        </p>

                        <p class="truncate text-xs text-slate-400">
                            Consumer
                        </p>

                    </div>

                    <i class="fas fa-chevron-down hidden text-[10px] text-slate-400 transition-transform duration-200 sm:block"
                        :class="open ? 'rotate-180' : ''">
                    </i>

                </button>

                <div x-cloak x-show="open" @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute right-0 mt-2 w-[calc(100vw-2rem)] max-w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/60">

                    <div class="border-b border-slate-100 p-4">

                        <div class="flex items-center gap-3">

                            <img class="h-11 w-11 shrink-0 rounded-xl object-cover ring-1 ring-slate-200"
                                src="{{ auth()->user()->avatar_url }}"
                                alt="{{ auth()->user()->full_name ?? auth()->user()->name }}">

                            <div class="min-w-0">

                                <p class="truncate text-sm font-bold text-slate-900">
                                    {{ auth()->user()->full_name ?? auth()->user()->name }}
                                </p>

                                <p class="mt-0.5 truncate text-xs text-slate-500">
                                    {{ auth()->user()->email }}
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="p-2">

                        @if (Route::has('profile.show'))
                            <a href="{{ route('profile.show') }}"
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">

                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-500">

                                    <i class="fas fa-user text-xs"></i>

                                </span>

                                My Profile

                            </a>
                        @endif

                        <a href="{{ route('consumer.complaints.index') }}"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">

                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-500">

                                <i class="fas fa-file-lines text-xs"></i>

                            </span>

                            My Complaints

                        </a>

                    </div>

                    <div class="border-t border-slate-100 p-2">

                        <form action="{{ route('logout') }}" method="POST">

                            @csrf

                            <button type="submit"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-red-600 transition hover:bg-red-50">

                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-500">

                                    <i class="fas fa-right-from-bracket text-xs"></i>

                                </span>

                                Sign Out

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</header>
