<nav class="sticky top-0 z-50 bg-white shadow">

    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center h-20 px-6 ">

            <a href="/" class="flex items-center gap-3 text-decoration-none">
                <img src="{{ asset('images/logo/logo.png') }}" alt="KnowledgeRetain AI Logo"
                    class="w-12 h-12 object-contain">

                <div>
                    <h1 class="text-2xl font-bold text-sky-700">
                        KnowledgeRetain AI
                    </h1>
                    <p class="text-xs text-gray-500">
                        Sagay Water District
                    </p>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-8 ">

                <a href="#about" class="text-gray-500 hover:text-sky-600 transition text-decoration-none">

                    About

                </a>

                <a href="#features" class="text-gray-500 hover:text-sky-600  transition text-decoration-none">

                    Features

                </a>

                <a href="#workflow" class="text-gray-500 hover:text-sky-600 transition text-decoration-none">

                    Workflow

                </a>

                <a href="#contact" class="text-gray-500 hover:text-sky-600 text-decoration-none transition ">

                    Contact

                </a>

            </div>

            <a href="{{ route('login') }}"
                class="bg-sky-700 text-white text-decoration-none px-6 py-3 rounded-xl hover:bg-sky-800 transition">

                Login

            </a>

        </div>

    </div>

</nav>
