<header class="sticky top-0 z-50 bg-white shadow-sm border-b">

    <div class="h-20 px-8 flex items-center justify-between">

        <div class="flex items-center gap-5">

            <button @click="sidebarMini=!sidebarMini"
                class="hidden lg:flex h-10 w-10 rounded-lg hover:bg-gray-100 items-center justify-center">

                <i class="fa-solid fa-bars"></i>

            </button>

            <button @click="sidebarOpen=true" class="lg:hidden h-10 w-10 rounded-lg hover:bg-gray-100">

                <i class="fa-solid fa-bars"></i>

            </button>

            <div>

                <h1 class="font-bold text-2xl text-slate-700">

                    @yield('title')

                </h1>

                <p class="text-sm text-gray-500">

                    iSWD - Sagay Water District

                </p>

            </div>

        </div>

        <div class="flex items-center gap-5">

            <form method="GET" action="{{ route('search') }}" class="hidden md:block relative">

                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3 text-gray-400"></i>

                <input type="search" name="q" value="{{ request('q') }}"
                    placeholder="Search employees, departments, positions..."
                    class="pl-11 pr-10 w-80 rounded-xl border bg-gray-50 px-4 py-2 focus:ring-2 focus:ring-sky-500 outline-none transition">

                @if (request('q'))
                    <a href="{{ route('search') }}" class="absolute right-3 top-3 text-gray-400 hover:text-red-500">

                        <i class="fa-solid fa-xmark"></i>

                    </a>
                @endif

            </form>

            <button class="relative h-10 w-10 rounded-xl hover:bg-gray-100">

                <i class="fa-regular fa-bell text-xl"></i>

                <span class="absolute right-2 top-2 w-2 h-2 bg-red-500 rounded-full"></span>

            </button>

            <div x-data="{ open: false }" class="relative">

                <button @click="open=!open" class="flex items-center gap-3 rounded-xl hover:bg-gray-100 px-3 py-2">

                    <img class="w-11 h-11 rounded-full" src="{{ auth()->user()->avatar_url }}" alt="User Avatar">

                    <div class="hidden md:block text-left">

                        <div class="font-semibold">

                            {{ auth()->user()->name }}

                        </div>

                        <div class="text-xs text-gray-500">

                            Customer Service

                        </div>

                    </div>

                    <i class="fa-solid fa-chevron-down text-sm"></i>

                </button>

                <div x-show="open" @click.away="open=false" x-transition
                    class="absolute right-0 mt-3 bg-white rounded-xl shadow-xl border w-72 overflow-hidden">

                    <div class="bg-gradient-to-r from-sky-700 to-cyan-600 text-white p-5">

                        <h4 class="font-semibold">

                            {{ auth()->user()->name }}

                        </h4>

                        <p class="text-sm">

                            {{ auth()->user()->email }}

                        </p>

                    </div>

                    <a href="{{ route('customer-service.profile.show') }}" class="block px-5 py-3 hover:bg-gray-100">

                        <i class="fa-solid fa-user mr-2"></i>

                        My Profile

                    </a>

                    <a href="#" class="block px-5 py-3 hover:bg-gray-100">

                        <i class="fa-solid fa-gear mr-2"></i>

                        Settings

                    </a>

                    <form action="{{ route('logout') }}" method="POST">

                        @csrf

                        <button class="w-full text-left px-5 py-3 hover:bg-red-50 text-red-600">

                            <i class="fa-solid fa-right-from-bracket mr-2"></i>

                            Logout

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</header>
