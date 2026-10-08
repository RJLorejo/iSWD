
@extends('consumer.auth.layout')

@section('title', 'Verify Password Reset')

@section('content')

@php
    $remaining = max(0, min(5, (int) ($attemptsRemaining ?? 5)));
    $locked = (bool) ($tooManyAttempts ?? ($remaining === 0));
@endphp

<div
    class="min-h-screen bg-slate-50 lg:grid lg:grid-cols-[1.08fr_.92fr]"
    x-data="passwordResetOtp({
        resendSeconds: {{ (int) ($resendAvailableIn ?? 0) }},
        expirySeconds: {{ (int) ($otpExpiresIn ?? 0) }},
        attemptsRemaining: {{ $remaining }},
        locked: @json($locked)
    })"
>

    {{-- LEFT SIDE --}}
    <div class="flex min-h-screen items-center justify-center px-4 py-5 sm:px-6 lg:px-10 xl:px-16">

        <div class="w-full max-w-md">

            {{-- BRAND --}}
            <div class="mb-5 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center gap-3">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-sky-100 bg-white shadow-sm">
                        <img
                            src="{{ asset('images/logo/logo.png') }}"
                            alt="iSWD Logo"
                            class="h-9 w-9 object-contain"
                        >
                    </div>

                    <div>
                        <p class="text-xl font-semibold leading-none text-sky-700">
                            iSWD
                        </p>
                        <p class="mt-1 text-[10px] font-medium uppercase tracking-[0.12em] text-slate-500">
                            Sagay Water District
                        </p>
                    </div>

                </a>
            </div>

            {{-- MAIN CARD --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-7">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">
                    Verify Your Email
                </h1>

                <p class="mt-1.5 text-sm leading-6 text-slate-500">
                    Enter the 6-digit verification code sent to
                </p>

                <p class="mt-1 break-all text-sm font-semibold text-slate-700">
                    {{ $email }}
                </p>

                {{-- SUCCESS MESSAGE --}}
                @if (session('status'))
                    <div class="mt-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5">
                        <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
                        <p class="text-sm leading-5 text-emerald-700">
                            {{ session('status') }}
                        </p>
                    </div>
                @endif

                {{-- ERROR MESSAGE --}}
                @if ($errors->any())
                    <div class="mt-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-3.5">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-600"></i>
                        <p class="text-sm leading-5 text-red-700">
                            {{ $errors->first() }}
                        </p>
                    </div>
                @endif

                {{-- TOO MANY ATTEMPTS --}}
                <div
                    x-cloak
                    x-show="locked"
                    class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600">
                            <i class="fa-solid fa-lock"></i>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-red-800">
                                Too Many Attempts
                            </p>

                            <p class="mt-1 text-xs leading-5 text-red-700">
                                You have used all 5 verification attempts.
                                This code is locked. Please request a new code.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- OTP FORM --}}
                <form
                    method="POST"
                    action="{{ route('consumer.password.verify.submit') }}"
                    class="mt-6"
                    @submit="submitOtp($event)"
                >
                    @csrf

                    <input type="hidden" name="otp" :value="otp">

                    {{-- SIX OTP INPUTS --}}
                    <div
                        id="password-reset-otp-inputs"
                        class="grid grid-cols-6 gap-2 sm:gap-2.5"
                        @paste.prevent="pasteOtp($event)"
                    >
                        @for ($i = 0; $i < 6; $i++)
                            <input
                                id="otp-{{ $i }}"
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                maxlength="1"
                                autocomplete="off"
                                data-otp-index="{{ $i }}"
                                :disabled="locked || expirySeconds <= 0"
                                class="otp-input h-12 w-full rounded-xl border border-slate-300 bg-white text-center text-lg font-bold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
                                @input="handleInput({{ $i }}, $event)"
                                @keydown="handleKeydown({{ $i }}, $event)"
                            >
                        @endfor
                    </div>

                    {{-- REMAINING ATTEMPTS --}}
                    <div class="mt-4" x-show="!locked && expirySeconds > 0">

                        <div class="flex items-center justify-between">
                            <p class="text-xs font-medium text-slate-500">
                                Verification attempts
                            </p>

                            <p
                                class="text-xs font-semibold"
                                :class="attemptsRemaining <= 2 ? 'text-red-600' : 'text-sky-700'"
                            >
                                <span x-text="attemptsRemaining"></span>
                                of 5 remaining
                            </p>
                        </div>

                        <div class="mt-2 grid grid-cols-5 gap-1.5">
                            <template x-for="index in 5" :key="index">
                                <div
                                    class="h-1.5 rounded-full transition-colors"
                                    :class="index <= attemptsRemaining
                                        ? (attemptsRemaining <= 2 ? 'bg-amber-500' : 'bg-sky-600')
                                        : 'bg-slate-200'"
                                ></div>
                            </template>
                        </div>

                        <p
                            x-cloak
                            x-show="attemptsRemaining <= 2"
                            class="mt-2 text-xs text-amber-700"
                        >
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            Please check your code carefully.
                        </p>

                    </div>

                    {{-- EXPIRATION / LOCKED STATUS --}}
                    <div class="mt-4 flex items-center justify-center">

                        {{-- ACTIVE TIMER --}}
                        <div
                            x-show="!locked && expirySeconds > 0"
                            class="inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500"
                        >
                            <i class="fa-regular fa-clock text-sky-600"></i>

                            <span>Code expires in</span>

                            <span
                                class="font-semibold tabular-nums"
                                :class="expirySeconds <= 60 ? 'text-red-600' : 'text-sky-700'"
                                x-text="formattedExpiry"
                            ></span>
                        </div>

                        {{-- EXPIRED --}}
                        <div
                            x-cloak
                            x-show="!locked && expirySeconds <= 0"
                            class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-600"
                        >
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            Verification code expired
                        </div>

                        {{-- LOCKED --}}
                        <div
                            x-cloak
                            x-show="locked"
                            class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-700"
                        >
                            <i class="fa-solid fa-lock"></i>
                            Code locked after 5 attempts
                        </div>

                    </div>

                    {{-- VERIFY BUTTON --}}
                    <button
                        type="submit"
                        :disabled="otp.length !== 6 || loading || locked || expirySeconds <= 0"
                        class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span x-show="!loading" class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>

                            <span x-text="locked
                                ? 'Verification Locked'
                                : (expirySeconds <= 0 ? 'Code Expired' : 'Verify Code')">
                            </span>
                        </span>

                        <span
                            x-cloak
                            x-show="loading"
                            class="flex items-center gap-2"
                        >
                            <i class="fa-solid fa-spinner animate-spin"></i>
                            Verifying...
                        </span>
                    </button>

                </form>

                {{-- RESEND OTP --}}
                <div class="mt-5 border-t border-slate-100 pt-5 text-center">

                    <p class="text-xs text-slate-500">
                        Didn't receive the code?
                    </p>

                    {{-- COOLDOWN --}}
                    <div
                        x-show="resendSeconds > 0"
                        class="mt-2 text-sm text-slate-500"
                    >
                        Resend available in

                        <span
                            class="font-semibold text-sky-700 tabular-nums"
                            x-text="resendSeconds"
                        ></span>

                        <span
                            x-text="resendSeconds === 1 ? 'second' : 'seconds'"
                        ></span>
                    </div>

                    {{-- RESEND BUTTON --}}
                    <form
                        x-cloak
                        x-show="resendSeconds <= 0"
                        method="POST"
                        action="{{ route('consumer.password.resend') }}"
                        class="mt-2"
                        @submit="resending = true"
                    >
                        @csrf

                        <button
                            type="submit"
                            :disabled="resending"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-sky-700 transition hover:text-sky-800 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span x-show="!resending" class="inline-flex items-center gap-2">
                                <i class="fa-solid fa-rotate-right text-xs"></i>

                                <span x-text="locked || expirySeconds <= 0
                                    ? 'Send New Code'
                                    : 'Resend Verification Code'">
                                </span>
                            </span>

                            <span
                                x-cloak
                                x-show="resending"
                                class="inline-flex items-center gap-2"
                            >
                                <i class="fa-solid fa-spinner animate-spin"></i>
                                Sending...
                            </span>
                        </button>
                    </form>

                </div>

                {{-- CHANGE EMAIL --}}
                <div class="mt-5 text-center">
                    <a
                        href="{{ route('consumer.password.request') }}"
                        class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 transition hover:text-sky-700"
                    >
                        <i class="fa-solid fa-arrow-left"></i>
                        Use a different email
                    </a>
                </div>

            </div>

            {{-- FOOTER --}}
            <p class="mt-4 text-center text-[11px] text-slate-400">
                &copy; {{ date('Y') }}
                Sagay Water District &middot;
                iSWD Consumer Portal
            </p>

        </div>
    </div>

    {{-- RIGHT SIDE --}}
    <div class="relative hidden overflow-hidden bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700 text-white lg:flex lg:items-center">

        <div class="relative mx-auto w-full max-w-lg px-10 xl:px-14">

            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-xs font-medium text-sky-50">
                <i class="fa-solid fa-envelope-circle-check"></i>
                Email Verification
            </span>

            <h2 class="mt-5 text-3xl font-semibold leading-tight tracking-tight xl:text-4xl">
                Confirm it's really you
            </h2>

            <p class="mt-4 text-sm leading-7 text-sky-50/90 xl:text-base">
                Password changes require email verification
                before a new password can be created.
            </p>

            <div class="mt-8 rounded-xl border border-white/15 bg-white/10 p-5">
                <div class="flex items-start gap-4">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10">
                        <i class="fa-solid fa-lock"></i>
                    </div>

                    <div>
                        <p class="text-sm font-semibold">
                            Never share your code
                        </p>

                        <p class="mt-1 text-xs leading-5 text-sky-100/80">
                            Sagay Water District personnel should
                            never ask you to disclose your verification code.
                        </p>
                    </div>

                </div>
            </div>

            <div class="mt-4 rounded-xl border border-white/15 bg-white/10 p-5">
                <div class="flex items-start gap-4">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>

                    <div>
                        <p class="text-sm font-semibold">
                            Secure password recovery
                        </p>

                        <p class="mt-1 text-xs leading-5 text-sky-100/80">
                            Your verification code expires after 5 minutes
                            and allows a maximum of 5 incorrect attempts.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

<script>
function passwordResetOtp(config) {
    return {
        digits: ['', '', '', '', '', ''],
        otp: '',
        loading: false,
        resending: false,

        resendSeconds: Math.max(0, Number(config.resendSeconds) || 0),
        expirySeconds: Math.max(0, Number(config.expirySeconds) || 0),

        attemptsRemaining: Math.max(
            0,
            Math.min(5, Number(config.attemptsRemaining ?? 5))
        ),

        locked: Boolean(config.locked),

        resendTimer: null,
        expiryTimer: null,

        init() {
            this.clearOtp();

            this.$nextTick(() => {
                if (!this.locked && this.expirySeconds > 0) {
                    this.focusInput(0);
                }
            });

            this.startResendTimer();
            this.startExpiryTimer();
        },

        getInputs() {
            return Array.from(
                this.$root.querySelectorAll(
                    '#password-reset-otp-inputs .otp-input'
                )
            );
        },

        focusInput(index) {
            if (this.locked || this.expirySeconds <= 0) return;

            const inputs = this.getInputs();
            const input = inputs[index];

            if (input && !input.disabled) {
                input.focus();
                input.select();
            }
        },

        updateOtp() {
            this.otp = this.digits.join('');
        },

        clearOtp() {
            this.digits = ['', '', '', '', '', ''];
            this.otp = '';

            this.$nextTick(() => {
                this.getInputs().forEach(input => {
                    input.value = '';
                });
            });
        },

        handleInput(index, event) {
            if (this.locked || this.expirySeconds <= 0) {
                event.target.value = '';
                return;
            }

            const value = event.target.value.replace(/\D/g, '');

            if (value.length > 1) {
                this.fillFromString(value, index);
                return;
            }

            this.digits[index] = value.slice(0, 1);
            event.target.value = this.digits[index];

            this.updateOtp();

            if (this.digits[index] && index < 5) {
                this.focusInput(index + 1);
            }
        },

        handleKeydown(index, event) {
            if (this.locked || this.expirySeconds <= 0) {
                event.preventDefault();
                return;
            }

            const inputs = this.getInputs();

            if (event.key === 'Backspace') {
                event.preventDefault();

                if (this.digits[index]) {
                    this.digits[index] = '';
                    inputs[index].value = '';
                } else if (index > 0) {
                    this.digits[index - 1] = '';
                    inputs[index - 1].value = '';
                    this.focusInput(index - 1);
                }

                this.updateOtp();
                return;
            }

            if (event.key === 'ArrowLeft' && index > 0) {
                event.preventDefault();
                this.focusInput(index - 1);
            }

            if (event.key === 'ArrowRight' && index < 5) {
                event.preventDefault();
                this.focusInput(index + 1);
            }

            if (
                event.key.length === 1 &&
                !/^\d$/.test(event.key) &&
                !event.ctrlKey &&
                !event.metaKey &&
                !event.altKey
            ) {
                event.preventDefault();
            }
        },

        fillFromString(value, startIndex = 0) {
            if (this.locked || this.expirySeconds <= 0) return;

            const numbers = value.replace(/\D/g, '').slice(0, 6);
            const inputs = this.getInputs();

            let lastIndex = startIndex;

            for (let i = 0; i < numbers.length; i++) {
                const index = startIndex + i;

                if (index >= 6) break;

                this.digits[index] = numbers[i];
                inputs[index].value = numbers[i];

                lastIndex = index;
            }

            this.updateOtp();

            if (lastIndex < 5) {
                this.focusInput(lastIndex + 1);
            } else {
                this.focusInput(5);
            }
        },

        pasteOtp(event) {
            if (this.locked || this.expirySeconds <= 0) return;

            const pasted = (
                event.clipboardData?.getData('text') || ''
            ).replace(/\D/g, '');

            if (!pasted) return;

            const targetIndex = Number(
                event.target.dataset.otpIndex ?? 0
            );

            this.fillFromString(pasted, targetIndex);
        },

        submitOtp(event) {
            this.updateOtp();

            if (
                this.loading ||
                this.locked ||
                this.expirySeconds <= 0 ||
                !/^\d{6}$/.test(this.otp)
            ) {
                event.preventDefault();
                return;
            }

            this.loading = true;
        },

        startResendTimer() {
            if (this.resendSeconds <= 0) return;

            this.resendTimer = setInterval(() => {
                this.resendSeconds = Math.max(
                    0,
                    this.resendSeconds - 1
                );

                if (this.resendSeconds === 0) {
                    clearInterval(this.resendTimer);
                }
            }, 1000);
        },

        startExpiryTimer() {
            if (this.locked || this.expirySeconds <= 0) return;

            this.expiryTimer = setInterval(() => {
                if (this.locked) {
                    clearInterval(this.expiryTimer);
                    return;
                }

                this.expirySeconds = Math.max(
                    0,
                    this.expirySeconds - 1
                );

                if (this.expirySeconds === 0) {
                    clearInterval(this.expiryTimer);
                    this.clearOtp();
                }
            }, 1000);
        },

        get formattedExpiry() {
            const seconds = Math.max(
                0,
                Math.floor(this.expirySeconds)
            );

            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;

            return String(minutes).padStart(2, '0')
                + ':'
                + String(remainingSeconds).padStart(2, '0');
        }
    };
}
</script>

@endsection
