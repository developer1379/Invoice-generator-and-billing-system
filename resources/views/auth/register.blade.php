@extends('invoices.layout')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-10">
    <div class="w-full max-w-sm bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-mui-2 hover:shadow-mui-8 transition-all flex flex-col gap-6">
        
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-zinc-50 tracking-tight">Create Account</h2>
            <p class="text-xs text-slate-505 dark:text-zinc-400 mt-1">Register now to start saving and custom-styling invoices.</p>
        </div>

        <!-- Google Sign-In Option -->
        <a href="{{ route('auth.google') }}" class="w-full inline-flex items-center justify-center gap-2.5 bg-white dark:bg-zinc-950/40 hover:bg-slate-50 dark:hover:bg-zinc-800/60 text-slate-700 dark:text-zinc-200 border border-slate-300 dark:border-zinc-800 font-bold py-2.5 rounded-lg text-xs tracking-wide shadow-sm hover:shadow active:scale-95 transition-all cursor-pointer">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
            </svg>
            <span>Continue with Google</span>
        </a>

        <!-- Divider -->
        <div class="relative flex items-center justify-center my-0.5 select-none">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200 dark:border-zinc-800/80"></div>
            </div>
            <span class="relative bg-white dark:bg-zinc-900 px-3 text-[9px] font-bold text-slate-400 dark:text-zinc-550 uppercase tracking-widest">Or register with email</span>
        </div>

        <!-- Form -->
        <form action="{{ route('register') }}" method="POST" class="flex flex-col gap-5">
            @csrf

            <!-- Name -->
            <div class="relative mt-2">
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-800 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                <label for="name" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Full Name</label>
                @error('name')
                    <p class="text-[10px] text-rose-600 dark:text-rose-400 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="relative mt-2">
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-800 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                <label for="email" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Email Address</label>
                @error('email')
                    <p class="text-[10px] text-rose-600 dark:text-rose-400 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="relative mt-2">
                <input type="password" name="password" id="password" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                <label for="password" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Password</label>
                @error('password')
                    <p class="text-[10px] text-rose-600 dark:text-rose-400 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="relative mt-2">
                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                <label for="password_confirmation" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Confirm Password</label>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-505 active:bg-indigo-750 text-white font-semibold py-2.5 rounded-lg text-xs tracking-wide shadow-mui-1 hover:shadow-mui-2 active:scale-95 cursor-pointer transition-all mt-1">
                Register Account
            </button>
        </form>

        <!-- Redirect -->
        <div class="text-center text-xs border-t border-slate-100 dark:border-zinc-800/60 pt-4">
            <span class="text-slate-500 dark:text-zinc-400">Already registered?</span>
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500 ml-1 transition-colors">Sign in here</a>
        </div>
    </div>
</div>
@endsection
