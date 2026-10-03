<header
    class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur">

    <div
        class="mx-auto flex h-16 w-full max-w-[1600px] items-center justify-between gap-3 px-4 sm:px-6 lg:h-[72px] lg:px-8">

        <div class="flex min-w-0 items-center gap-3 sm:gap-4">

            <button
                type="button"
                @click="sidebarMini = !sidebarMini"
                class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 lg:flex"
                :title="sidebarMini ? 'Expand sidebar' : 'Collapse sidebar'">

                <i
                    class="fas"
                    :class="sidebarMini ? 'fa-angles-right' : 'fa-bars'">
                </i>

            </button>

            <button
                type="button"
                @click="sidebarOpen = true"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 lg:hidden"
                aria-label="Open navigation">

                <i class="fas fa-bars"></i>

            </button>

            <div class="min-w-0">

                <p
                    class="hidden text-[11px] font-bold uppercase tracking-[0.14em] text-sky-600 sm:block">
                    Customer Service
                </p>

                <h1
                    class="truncate text-base font-bold tracking-tight text-slate-900 sm:text-lg lg:text-xl">
                    @yield('title', 'Customer Service')
                </h1>

            </div>

        </div>

        <div class="flex shrink-0 items-center gap-2 sm:gap-3">

            @php
                $navbarNewFeedbackCount = \App\Models\ComplaintFeedback::query()
                    ->where('handling_status', 'New')
                    ->count();
            @endphp

            <a
                href="{{ route('customer-service.feedback.index', ['handling_status' => 'New']) }}"
                class="relative flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-sky-50 hover:text-sky-700"
                title="New consumer feedback">

                <i class="far fa-bell text-lg"></i>

                @if ($navbarNewFeedbackCount > 0)

                    <span
                        class="absolute right-1.5 top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold leading-none text-white ring-2 ring-white">

                        {{ $navbarNewFeedbackCount > 9 ? '9+' : $navbarNewFeedbackCount }}

                    </span>

                @endif

            </a>

            <div
                x-data="{ open: false }"
                class="relative">

                <button
                    type="button"
                    @click="open = !open"
                    class="flex items-center gap-2 rounded-xl p-1.5 transition hover:bg-slate-100 sm:gap-3 sm:px-2">

                    <img
                        src="{{ auth()->user()->avatar_url }}"
                        alt="{{ auth()->user()->full_name ?? auth()->user()->name }}"
                        class="h-9 w-9 rounded-xl border border-slate-200 object-cover sm:h-10 sm:w-10">

                    <div class="hidden max-w-[180px] text-left md:block">

                        <p class="truncate text-sm font-bold text-slate-800">
                            {{ auth()->user()->full_name ?? auth()->user()->name }}
                        </p>

                        <p class="truncate text-xs text-slate-500">
                            Customer Service
                        </p>

                    </div>

                    <i
                        class="fas fa-chevron-down hidden text-[10px] text-slate-400 transition-transform duration-200 sm:block"
                        :class="open ? 'rotate-180' : ''">
                    </i>

                </button>

                <div
                    x-cloak
                    x-show="open"
                    @click.outside="open = false"
                    x-transition.origin.top.right
                    class="absolute right-0 z-50 mt-3 w-[calc(100vw-2rem)] max-w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

                    <div
                        class="bg-gradient-to-br from-sky-700 via-blue-700 to-cyan-600 p-5 text-white">

                        <div class="flex items-center gap-3">

                            <img
                                src="{{ auth()->user()->avatar_url }}"
                                alt="{{ auth()->user()->full_name ?? auth()->user()->name }}"
                                class="h-11 w-11 rounded-xl border border-white/20 object-cover">

                            <div class="min-w-0">

                                <p class="truncate text-sm font-bold">
                                    {{ auth()->user()->full_name ?? auth()->user()->name }}
                                </p>

                                <p class="mt-0.5 truncate text-xs text-sky-100">
                                    {{ auth()->user()->email }}
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="p-2">

                        <a
                            href="{{ route('profile.show') }}"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">

                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600">

                                <i class="fas fa-user text-xs"></i>

                            </span>

                            My Profile

                        </a>

                    </div>

                    <div class="border-t border-slate-100 p-2">

                        <form
                            action="{{ route('logout') }}"
                            method="POST">

                            @csrf

                            <button
                                type="submit"
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
