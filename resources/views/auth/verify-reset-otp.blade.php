
@extends('layouts.auth')

@section('title', 'Verify Employee Email')

@section('content')

<div class="min-h-screen bg-slate-50 lg:grid lg:grid-cols-[1.08fr_.92fr]"
     x-data="staffOtp({
        resend: {{ (int) $resendAvailableIn }},
        expiry: {{ (int) $otpExpiresIn }}
     })"
     x-init="startTimers()">

    <div class="flex min-h-screen items-center justify-center px-4 py-6 sm:px-6 lg:px-10 xl:px-16">
        <div class="w-full max-w-md">

            <a href="{{ route('landing') }}" class="mb-5 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-sky-100 bg-white shadow-sm">
                    <img src="{{ asset('images/logo/logo.png') }}" alt="iSWD" class="h-9 w-9 object-contain">
                </div>
                <div>
                    <p class="text-xl font-semibold text-sky-700">iSWD</p>
                    <p class="text-[10px] uppercase tracking-widest text-slate-500">Sagay Water District</p>
                </div>
            </a>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-7">

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                <h1 class="mt-5 text-2xl font-semibold text-slate-900">Verify Your Email</h1>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Enter the six-digit verification code sent to:
                </p>

                <p class="mt-1 break-all text-sm font-semibold text-sky-700">
                    {{ $email }}
                </p>

                @if (session('status'))
                    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('password.verify.submit') }}"
                      class="mt-6"
                      @submit="loading = true">

                    @csrf

                    <input type="hidden" name="otp" :value="digits.join('')">

                    <label class="mb-3 block text-sm font-medium text-slate-700">
                        Verification Code
                    </label>

                    <div class="flex justify-between gap-2" @paste.prevent="pasteCode($event)">
                        <template x-for="(digit, index) in digits" :key="index">
                            <input type="text"
                                   inputmode="numeric"
                                   maxlength="1"
                                   autocomplete="one-time-code"
                                   :value="digit"
                                   :data-otp-index="index"
                                   @input="updateDigit(index, $event)"
                                 @keydown.backspace.prevent="backspace(index, $event)"
                                   class="h-14 w-full min-w-0 rounded-xl border border-slate-300 bg-white text-center text-xl font-bold text-slate-900 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                        </template>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-3 text-xs">
                        <span class="text-slate-500">
                            <i class="fa-regular fa-clock mr-1"></i>
                            Expires in
                            <strong x-text="formatTime(expiry)" :class="expiry === 0 ? 'text-red-600' : 'text-sky-700'"></strong>
                        </span>

                        <span class="text-slate-500">
                            {{ $attemptsRemaining }} attempts remaining
                        </span>
                    </div>

                    <button type="submit"
                            :disabled="loading || digits.join('').length !== 6 || expiry === 0 || {{ $attemptsRemaining }} === 0"
                            class="mt-6 flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-5 py-3 font-semibold text-white shadow-md disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-show="!loading">
                            <i class="fa-solid fa-check-circle mr-2"></i>Verify Code
                        </span>
                        <span x-cloak x-show="loading">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>Verifying...
                        </span>
                    </button>
                </form>

                <form method="POST" action="{{ route('password.resend') }}" class="mt-5 text-center">
                    @csrf
                    <p class="mb-2 text-xs text-slate-500">Didn't receive the code?</p>
                    <button type="submit" :disabled="resend > 0"
                            class="text-sm font-semibold text-sky-700 disabled:cursor-not-allowed disabled:text-slate-400">
                        <span x-show="resend === 0">Resend Verification Code</span>
                        <span x-show="resend > 0" x-cloak>
                            Resend in <span x-text="resend"></span>s
                        </span>
                    </button>
                </form>

                <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-sky-700">
                        <i class="fa-solid fa-arrow-left mr-2"></i>Use a Different Email
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="hidden min-h-screen items-center justify-center bg-gradient-to-br from-sky-800 via-blue-700 to-cyan-600 px-10 text-white lg:flex">
        <div class="max-w-lg">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/20 bg-white/10">
                <i class="fa-solid fa-envelope-circle-check text-3xl"></i>
            </div>

            <h2 class="mt-8 text-3xl font-semibold">Verify Your Identity</h2>

            <p class="mt-4 text-sm leading-7 text-sky-100">
                Your security matters. Confirm access to your employee email before creating a new password.
            </p>

            <div class="mt-9 space-y-4">
                <div class="rounded-xl border border-white/15 bg-white/10 p-5">
                    <i class="fa-solid fa-clock mr-2"></i>
                    Your code is valid for 5 minutes.
                </div>
                <div class="rounded-xl border border-white/15 bg-white/10 p-5">
                    <i class="fa-solid fa-shield-halved mr-2"></i>
                    Never share your verification code.
                </div>
                <div class="rounded-xl border border-white/15 bg-white/10 p-5">
                    <i class="fa-solid fa-rotate mr-2"></i>
                    Request a new code if yours expires.
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('staffOtp', (config) => ({
        digits: ['', '', '', '', '', ''],
        resend: config.resend,
        expiry: config.expiry,
        loading: false,
        timer: null,

        startTimers() {
            this.$nextTick(() => this.focus(0));

            this.timer = setInterval(() => {
                if (this.resend > 0) this.resend--;
                if (this.expiry > 0) this.expiry--;

                if (this.resend === 0 && this.expiry === 0) {
                    clearInterval(this.timer);
                }
            }, 1000);
        },

        focus(index) {
            this.$nextTick(() => {
                const input = this.$root.querySelector(
                    `[data-otp-index="${index}"]`
                );

                if (input) {
                    input.focus();
                    input.select();
                }
            });
        },

        updateDigit(index, event) {
            const value = event.target.value.replace(/\D/g, '');

            // Handle autofill or multiple characters.
            if (value.length > 1) {
                const values = value.slice(0, 6 - index).split('');

                values.forEach((digit, offset) => {
                    this.digits[index + offset] = digit;
                });

                this.focus(Math.min(index + values.length, 5));
                return;
            }

            this.digits[index] = value;
            event.target.value = value;

            if (value && index < 5) {
                this.focus(index + 1);
            }
        },

        backspace(index, event) {
            if (!this.digits[index] && index > 0) {
                this.digits[index - 1] = '';
                this.focus(index - 1);
            } else {
                this.digits[index] = '';
                event.target.value = '';
            }
        },

        pasteCode(event) {
            const code = event.clipboardData
                .getData('text')
                .replace(/\D/g, '')
                .slice(0, 6);

            this.digits = ['', '', '', '', '', ''];

            code.split('').forEach((digit, index) => {
                this.digits[index] = digit;
            });

            this.focus(Math.min(code.length, 5));
        },

        formatTime(seconds) {
            return `${Math.floor(seconds / 60)}:${String(seconds % 60).padStart(2, '0')}`;
        }
    }));
});
</script>


@endsection
