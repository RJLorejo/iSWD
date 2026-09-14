<section class="bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-700 text-white">

    <div class="max-w-7xl mx-auto px-8 py-24">

        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <div>

                <p class="uppercase tracking-widest text-sky-200">

                    Sagay Water District

                </p>

                <h1 class="text-6xl font-bold mt-4">

                    iSWD
                </h1>

                <p class="mt-8 text-xl leading-9">

                    An Intelligent Water Service and Complaint Support System
                    that helps consumers report concerns, receive AI-assisted guidance,
                    and enables Sagay Water District personnel to efficiently manage
                    and respond to service complaints.

                </p>

                <div class="mt-10 flex flex-wrap gap-4">

                    {{-- Consumer Portal --}}
                    <a href="{{ route('consumer.login') }}"
                        class="inline-flex items-center gap-2
               bg-white
               text-decoration-none
               text-sky-700
               px-8 py-4
               rounded-xl
               font-semibold
               shadow-lg
               hover:bg-sky-50
               transition">

                        <i class="fas fa-user"></i>

                        Consumer Portal

                    </a>

                    <a href="#features"
                        class="inline-flex items-center gap-2
               border
               text-white
               text-decoration-none
               border-white/50
               px-8 py-4
               rounded-xl
               hover:bg-white/10
               transition">

                        Learn More

                    </a>

                </div>

            </div>

            <div>

                <img src="{{ asset('images/logo/landing/hero.png') }}" class="rounded-3xl shadow-2xl w-full">

            </div>

        </div>

    </div>

</section>
