@extends('consumer.auth.layout')

@section('title', 'Verify Your Email')

@section('content')

    <div class="min-h-screen bg-slate-50">

        <div class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-sky-100 bg-white">
                        <img src="{{ asset('images/logo/logo.png') }}" alt="iSWD Logo" class="h-9 w-9 object-contain">
                    </div>
                    <div>
                        <p class="text-xl font-semibold leading-none text-sky-700">iSWD</p>
                        <p class="mt-1 text-[10px] font-medium uppercase tracking-[0.12em] text-slate-500">
                            Sagay Water District
                        </p>
                    </div>
                </a>
            </div>
        </div>

        <div class="mx-auto max-w-md px-4 py-10 sm:py-14">

            {{-- Progress --}}
            <div class="mb-6 flex items-center justify-center gap-2 text-[11px] font-semibold">
                <span class="rounded-full bg-sky-100 px-3 py-1 text-sky-700"><i class="fa-solid fa-check mr-1"></i>
                    Information</span>
                <span class="h-px w-5 bg-slate-300"></span>
                <span class="rounded-full bg-sky-700 px-3 py-1 text-white">Email OTP</span>
                <span class="h-px w-5 bg-slate-300"></span>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-400">SWD Review</span>
            </div>

            <div id="otp-verification-card"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/40 sm:p-8"
                x-data="{
                    digits: ['', '', '', '', '', ''],
                    left: {{ (int) $resendIn }},
                    loading: false,
                    code() { return this.digits.join(''); },
                    type(i, e) {
                        const v = e.target.value.replace(/\D/g, '').slice(-1);
                        this.digits[i] = v;
                        e.target.value = v;
                        if (v && i < 5) this.$refs['d' + (i + 1)].focus();
                    },
                    back(i, e) {
                        if (e.key === 'Backspace' && !this.digits[i] && i > 0) this.$refs['d' + (i - 1)].focus();
                    },
                    paste(e) {
                        const t = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
                        if (!t) return;
                        e.preventDefault();
                        t.split('').forEach((c, i) => {
                            this.digits[i] = c;
                            this.$refs['d' + i].value = c;
                        });
                        this.$refs['d' + Math.min(t.length, 5)].focus();
                    },
                    init() {
                        this.$refs.d0.focus();
                        setInterval(() => { if (this.left > 0) this.left--; }, 1000);
                    }
                }">

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <i class="fa-solid fa-envelope-circle-check text-lg"></i>
                </div>

                <h1 class="mt-5 text-xl font-semibold text-slate-900">Verify your email</h1>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    We sent a 6-digit code to
                    <span class="font-semibold text-slate-800">{{ $maskedEmail }}</span>.
                    It expires in 5 minutes.
                </p>

                @if (session('success'))
                    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800">
                        <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $errors->first() }}
                    </div>
                @endif

                {{-- Verify --}}
                <form method="POST" action="{{ route('consumer.register.verify-email.submit') }}" class="mt-6"
                    @submit="loading = true">
                    @csrf

                    <input type="hidden" name="code" :value="code()">

                    <div class="flex justify-between gap-2" @paste="paste($event)">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" inputmode="numeric"
                                autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}" maxlength="1"
                                x-ref="d{{ $i }}" @input="type({{ $i }}, $event)"
                                @keydown="back({{ $i }}, $event)" aria-label="Digit {{ $i + 1 }}"
                                class="h-12 w-full max-w-[3.25rem] rounded-xl border border-slate-300 text-center text-lg font-semibold text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                        @endfor
                    </div>

                    <button type="submit" :disabled="code().length < 6 || loading"
                        class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-show="!loading" class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-check"></i> Verify &amp; Submit Registration
                        </span>
                        <span x-cloak x-show="loading" class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner animate-spin"></i> Verifying...
                        </span>
                    </button>
                </form>

                {{-- Resend --}}
                <form method="POST" action="{{ route('consumer.register.verify-email.resend') }}"
                    class="mt-5 text-center text-xs text-slate-500">
                    @csrf
                    Didn't get the code?
                    <button type="submit" :disabled="left > 0"
                        class="font-semibold text-sky-700 hover:text-sky-800 disabled:cursor-not-allowed disabled:text-slate-400">
                        <span x-show="left > 0" x-text="'Resend in ' + left + 's'"></span>
                        <span x-cloak x-show="left <= 0">Resend code</span>
                    </button>
                </form>

                <p class="mt-4 text-center text-[11px] leading-5 text-slate-400">
                    Check your spam folder if it doesn't arrive within a minute.
                </p>

            </div>

            {{-- Registration Options --}}
            <div class="mt-5 space-y-3" x-data="{
                showChangeEmail: false,
                changingEmail: false
            }">

                {{-- Back To Registration --}}
                <div class="text-center">

                    <a href="{{ route('consumer.register') }}"
                        class="
                inline-flex items-center gap-2
                text-xs font-semibold
                text-slate-600
                transition
                hover:text-sky-700
            ">

                        <i class="fa-solid fa-arrow-left"></i>

                        Back to Registration

                    </a>

                    <p class="mt-1 text-[11px] text-slate-400">
                        Your information will be restored. You only need to re-enter your password.
                    </p>

                </div>


                {{-- Change Email --}}
                <div class="text-center">

                    <button type="button"
                        @click="
        showChangeEmail = !showChangeEmail;

        if (showChangeEmail) {
            $nextTick(() => {
                const input = document.getElementById('new-registration-email');

                if (input) {
                    input.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    setTimeout(() => {
                        input.focus();
                    }, 450);
                }
            });
        }
    "
                        class="
                text-xs font-semibold
                text-sky-700
                transition
                hover:text-sky-800
            ">

                        <i class="fa-solid fa-envelope mr-1"></i>

                        Change Email Address

                    </button>

                </div>


                {{-- Change Email Form --}}
                <div x-cloak x-show="showChangeEmail" x-transition
                    class="
            rounded-2xl
            border border-slate-200
            bg-white
            p-4
            shadow-sm
        ">

                    <div class="mb-4">

                        <h2
                            class="
                    text-sm
                    font-semibold
                    text-slate-900
                ">
                            Change Email Address
                        </h2>

                        <p
                            class="
                    mt-1
                    text-xs
                    leading-5
                    text-slate-500
                ">
                            Enter the correct email address. A new verification code will be sent there and your previous
                            code will no longer work.
                        </p>

                    </div>


                    <form method="POST" action="{{ route('consumer.register.verify-email.change-email') }}"
                        @submit="changingEmail = true">

                        @csrf


                        <label for="new-registration-email"
                            class="
                    mb-1.5
                    block
                    text-xs
                    font-semibold
                    text-slate-700
                ">
                            New Email Address
                        </label>


                        <input type="email" id="new-registration-email" name="email" value="{{ old('email') }}"
                            required autocomplete="email" placeholder="example@email.com"
                            class="
                    w-full
                    rounded-xl
                    border border-slate-300
                    px-4 py-3
                    text-sm
                    text-slate-900
                    outline-none
                    transition
                    placeholder:text-slate-400
                    focus:border-sky-500
                    focus:ring-2
                    focus:ring-sky-100
                ">


                        @error('email')
                            <p
                                class="
                        mt-2
                        text-xs
                        text-red-600
                    ">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i>

                                {{ $message }}
                            </p>
                        @enderror


                        <div
                            class="
                    mt-4
                    flex
                    gap-2
                ">

                            <button type="button" @click="showChangeEmail = false" :disabled="changingEmail"
                                class="
                        flex-1
                        rounded-xl
                        border border-slate-300
                        bg-white
                        px-4 py-2.5
                        text-xs
                        font-semibold
                        text-slate-700
                        transition
                        hover:bg-slate-50
                        disabled:cursor-not-allowed
                        disabled:opacity-60
                    ">
                                Cancel
                            </button>


                            <button type="submit" :disabled="changingEmail"
                                class="
                        inline-flex
                        flex-1
                        items-center
                        justify-center
                        gap-2
                        rounded-xl
                        bg-sky-700
                        px-4 py-2.5
                        text-xs
                        font-semibold
                        text-white
                        transition
                        hover:bg-sky-800
                        disabled:cursor-not-allowed
                        disabled:opacity-60
                    ">

                                <span x-show="!changingEmail">
                                    Send New Code
                                </span>

                                <span x-cloak x-show="changingEmail" class="flex items-center gap-2">

                                    <i
                                        class="
                                fa-solid
                                fa-spinner
                                animate-spin
                            "></i>

                                    Sending...

                                </span>

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

    @if (session('focus_otp'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                setTimeout(function() {

                    const otpCard =
                        document.getElementById(
                            'otp-verification-card'
                        );

                    if (!otpCard) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Smoothly Return To OTP
                    |--------------------------------------------------------------------------
                    */

                    otpCard.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });


                    /*
                    |--------------------------------------------------------------------------
                    | Focus First OTP Input
                    |--------------------------------------------------------------------------
                    |
                    | Give Alpine enough time to initialize the OTP component.
                    |
                    */

                    setTimeout(function() {

                        const firstOtpInput =
                            otpCard.querySelector(
                                'input[inputmode="numeric"]'
                            );

                        if (firstOtpInput) {
                            firstOtpInput.focus();
                        }

                    }, 500);

                }, 200);

            });
        </script>
    @endif

@endsection
