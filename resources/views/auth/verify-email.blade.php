@extends('invoices.layout')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-10">
    <div class="w-full max-w-md bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-mui-2 hover:shadow-mui-8 transition-all flex flex-col gap-6 select-none">
        
        <!-- Icon & Header -->
        <div class="text-center">
            <div class="h-12 w-12 rounded-full bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center mx-auto mb-4 text-indigo-500 animate-bounce">
                <i class="fa-solid fa-envelope-open-text text-xl"></i>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-zinc-50 tracking-tight">Verify Your Email</h2>
            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-2 leading-relaxed">
                Before accessing your invoicing dashboard, please verify your email address by clicking the activation link we just emailed to you.
            </p>
        </div>

        @if (session('success'))
            <!-- Success Alert -->
            <div class="p-3.5 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 text-emerald-800 dark:text-emerald-300 rounded-lg text-xs leading-relaxed font-semibold flex items-start gap-2.5">
                <i class="fa-solid fa-circle-check text-base shrink-0 mt-0.5 animate-pulse"></i>
                <span>A new verification link has been sent to the email address you provided during registration.</span>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex flex-col gap-3.5 mt-2">
            <!-- Resend Form -->
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-750 text-white font-semibold py-2.5 rounded-lg text-xs tracking-wide shadow-mui-1 hover:shadow-mui-2 active:scale-95 cursor-pointer transition-all">
                    Resend Verification Email
                </button>
            </form>

            <div class="flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/60 pt-4 text-xs font-semibold">
                <span class="text-slate-400 dark:text-zinc-500">Wrong email?</span>
                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-indigo-600 hover:text-indigo-500 transition-colors cursor-pointer">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
