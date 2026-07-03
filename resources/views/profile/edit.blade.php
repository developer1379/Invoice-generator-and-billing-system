@extends('invoices.layout')

@section('content')
<div x-data="profileSettings" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Title Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-zinc-50 tracking-tight">Account Settings</h1>
        <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">Manage your security credentials and default business billing metadata.</p>
    </div>

    <!-- Tab-Based Responsive Workspace Grid -->
    <div class="flex flex-col lg:flex-row gap-8 items-start">
        
        <!-- Left Sidebar Tabs Navigation -->
        <div class="w-full lg:w-64 shrink-0 flex flex-row lg:flex-col gap-1.5 overflow-x-auto lg:overflow-x-visible border-b lg:border-b-0 lg:border-r border-slate-200/60 dark:border-zinc-800/80 pb-4 lg:pb-0 lg:pr-4 select-none">
            <button type="button" @click="settingsTab = 'profile'" :class="settingsTab === 'profile' ? 'bg-indigo-50 dark:bg-indigo-950/30 text-indigo-650 dark:text-indigo-400 font-extrabold border-b-2 lg:border-b-0 lg:border-l-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50/50 dark:hover:bg-zinc-850/40'" class="w-full text-left px-4 py-3 rounded-xl text-xs flex items-center gap-2.5 transition-all cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-user-shield text-sm"></i> Login & Security
            </button>
            <button type="button" @click="settingsTab = 'business'" :class="settingsTab === 'business' ? 'bg-indigo-50 dark:bg-indigo-950/30 text-indigo-650 dark:text-indigo-400 font-extrabold border-b-2 lg:border-b-0 lg:border-l-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50/50 dark:hover:bg-zinc-850/40'" class="w-full text-left px-4 py-3 rounded-xl text-xs flex items-center gap-2.5 transition-all cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-briefcase text-sm"></i> Business Defaults
            </button>
            <button type="button" @click="settingsTab = 'fields'" :class="settingsTab === 'fields' ? 'bg-indigo-50 dark:bg-indigo-950/30 text-indigo-650 dark:text-indigo-400 font-extrabold border-b-2 lg:border-b-0 lg:border-l-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50/50 dark:hover:bg-zinc-850/40'" class="w-full text-left px-4 py-3 rounded-xl text-xs flex items-center gap-2.5 transition-all cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-list-check text-sm"></i> Custom Product Fields
            </button>
            <button type="button" @click="settingsTab = 'integrations'" :class="settingsTab === 'integrations' ? 'bg-indigo-50 dark:bg-indigo-950/30 text-indigo-650 dark:text-indigo-400 font-extrabold border-b-2 lg:border-b-0 lg:border-l-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50/50 dark:hover:bg-zinc-850/40'" class="w-full text-left px-4 py-3 rounded-xl text-xs flex items-center gap-2.5 transition-all cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-key text-sm"></i> API Integrations
            </button>
        </div>

        <!-- Right Content Area -->
        <div class="flex-1 w-full bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 shadow-sm min-h-[460px]">
            
            <!-- TAB 1: LOGIN PROFILE -->
            <div x-show="settingsTab === 'profile'" x-transition>
                <h2 class="text-sm font-black text-slate-800 dark:text-zinc-50 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-indigo-600"></i>
                    Login Profile & Security
                </h2>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mb-6 font-bold uppercase tracking-wider">Configure your profile identity and login password credentials.</p>

                <form action="{{ route('profile.update') }}" method="POST" class="flex flex-col gap-4 max-w-md">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="relative mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-800 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                        <label for="name" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Full Name</label>
                        @error('name')
                            <p class="text-[10px] text-rose-600 dark:text-rose-400 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="relative mt-2">
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                        <label for="email" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Email Address</label>
                        @error('email')
                            <p class="text-[10px] text-rose-600 dark:text-rose-400 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Change Password Header -->
                    <div class="mt-4 border-t border-slate-100 dark:border-zinc-800/60 pt-4">
                        <h3 class="text-xs font-bold text-slate-707 dark:text-zinc-350 mb-3 uppercase tracking-wider">Update Password (Optional)</h3>
                    </div>

                    <!-- New Password -->
                    <div class="relative mt-2">
                        <input type="password" name="password" id="password" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-800 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                        <label for="password" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">New Password</label>
                        @error('password')
                            <p class="text-[10px] text-rose-600 dark:text-rose-400 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="relative mt-2">
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                        <label for="password_confirmation" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Confirm New Password</label>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-900 font-semibold py-2.5 rounded-lg text-xs tracking-wide shadow-sm hover:shadow-md cursor-pointer transition-all mt-4 hover:-translate-y-0.5 active:translate-y-0">
                        Update Login Info
                    </button>
                </form>
            </div>

            <!-- TAB 2: BUSINESS DEFAULTS -->
            <div x-show="settingsTab === 'business'" x-transition>
                <h2 class="text-sm font-black text-slate-800 dark:text-zinc-50 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-briefcase text-indigo-600"></i>
                    Business Defaults (Bill From)
                </h2>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mb-6 font-bold uppercase tracking-wider">Configure default sender information and branding for your invoices.</p>

                <form action="{{ route('profile.update') }}" method="POST" class="flex flex-col gap-5">
                    @csrf
                    @method('PUT')

                    <!-- Keep login details unchanged -->
                    <input type="hidden" name="name" value="{{ $user->name }}" />
                    <input type="hidden" name="email" value="{{ $user->email }}" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Company Name -->
                        <div class="relative mt-2">
                            <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $user->business_name) }}" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label for="business_name" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Business Name</label>
                        </div>

                        <!-- Billing Email -->
                        <div class="relative mt-2">
                            <input type="email" name="business_email" id="business_email" value="{{ old('business_email', $user->business_email) }}" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label for="business_email" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Billing Email</label>
                        </div>

                        <!-- Phone -->
                        <div class="relative mt-2">
                            <input type="text" name="business_phone" id="business_phone" value="{{ old('business_phone', $user->business_phone) }}" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label for="business_phone" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Business Phone</label>
                        </div>

                        <!-- Tax ID / VAT -->
                        <div class="relative mt-2">
                            <input type="text" name="business_tax_id" id="business_tax_id" value="{{ old('business_tax_id', $user->business_tax_id) }}" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label for="business_tax_id" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Tax ID / VAT Registration</label>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="relative mt-2">
                        <textarea name="business_address" id="business_address" rows="3" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600 resize-none">{{ old('business_address', $user->business_address) }}</textarea>
                        <label for="business_address" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Company Physical Address</label>
                    </div>

                    <!-- Website -->
                    <div class="relative mt-2">
                        <input type="text" name="business_website" id="business_website" value="{{ old('business_website', $user->business_website) }}" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                        <label for="business_website" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Website URL</label>
                    </div>

                    <!-- Defaults section -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-slate-100 dark:border-zinc-800/60 pt-4 mt-2">
                        <!-- Default Currency -->
                        <div class="relative mt-2">
                            <select name="business_currency" id="business_currency" class="w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-800 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600">
                                <option value="USD" {{ $user->business_currency === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ $user->business_currency === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="GBP" {{ $user->business_currency === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                <option value="INR" {{ $user->business_currency === 'INR' ? 'selected' : '' }}>INR (₹)</option>
                            </select>
                            <label for="business_currency" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Default Currency</label>
                        </div>

                        <!-- Default Tax Rate -->
                        <div class="relative mt-2">
                            <input type="number" name="business_tax_rate" id="business_tax_rate" value="{{ old('business_tax_rate', $user->business_tax_rate) }}" min="0" max="100" step="any" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label for="business_tax_rate" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Default Tax (%)</label>
                        </div>

                        <!-- Default Discount Rate -->
                        <div class="relative mt-2">
                            <input type="number" name="business_discount_rate" id="business_discount_rate" value="{{ old('business_discount_rate', $user->business_discount_rate) }}" min="0" max="100" step="any" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label for="business_discount_rate" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Default Disc (%)</label>
                        </div>
                    </div>

                    <!-- Default Notes -->
                    <div class="relative mt-2">
                        <textarea name="business_notes" id="business_notes" rows="2" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600 resize-none">{{ old('business_notes', $user->business_notes) }}</textarea>
                        <label for="business_notes" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Default Invoice Notes</label>
                    </div>

                    <!-- Default Terms -->
                    <div class="relative mt-2">
                        <textarea name="business_terms" id="business_terms" rows="2" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600 resize-none">{{ old('business_terms', $user->business_terms) }}</textarea>
                        <label for="business_terms" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Default Terms & Conditions</label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2 pt-4 border-t border-slate-100 dark:border-zinc-800/60">
                        <!-- Logo Upload Widget -->
                        <div x-data="{ logoPreview: business_logo }" class="relative">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 rounded-lg border border-slate-205 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-950 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                                    <template x-if="logoPreview">
                                        <img :src="logoPreview" class="h-full w-full object-contain" />
                                    </template>
                                    <template x-if="!logoPreview">
                                        <i class="fa-solid fa-image text-slate-300 dark:text-zinc-700 text-lg"></i>
                                    </template>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 dark:bg-zinc-800 dark:hover:bg-zinc-750 text-slate-800 dark:text-zinc-200 text-[10px] font-bold rounded-lg border border-slate-205 dark:border-zinc-700 cursor-pointer shadow-sm text-center select-none active:scale-95 transition-all">
                                        Default Logo
                                        <input type="file" accept="image/*" @change="
                                            const file = $event.target.files[0];
                                            if (file) {
                                                const reader = new FileReader();
                                                reader.onload = (e) => {
                                                    logoPreview = e.target.result;
                                                    business_logo = e.target.result;
                                                };
                                                reader.readAsDataURL(file);
                                            }
                                        " class="hidden" />
                                    </label>
                                    <button x-show="logoPreview" type="button" @click="logoPreview = ''; business_logo = ''" class="text-[9px] font-bold text-rose-600 hover:underline text-left cursor-pointer">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="business_logo" :value="business_logo" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Default Brand Logo</label>
                        </div>

                        <!-- Signature Draw Widget -->
                        <div x-data="settingsSignaturePad" class="relative">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-32 rounded-lg border border-slate-205 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-950 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                                    <template x-if="signatureValue">
                                        <img :src="signatureValue" class="h-full w-full object-contain" />
                                    </template>
                                    <template x-if="!signatureValue">
                                        <span class="text-[9px] font-bold text-slate-400 dark:text-zinc-600 uppercase tracking-wider">No signature</span>
                                    </template>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <button type="button" @click="openDrawModal" class="px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 dark:bg-zinc-855 dark:hover:bg-zinc-800 text-slate-808 dark:text-zinc-200 text-[10px] font-bold rounded-lg border border-slate-205 dark:border-zinc-700 shadow-sm cursor-pointer select-none active:scale-95 transition-all">
                                        Draw Signature
                                    </button>
                                    <button x-show="signatureValue" type="button" @click="clearSignature" class="text-[9px] font-bold text-rose-600 hover:underline text-left cursor-pointer">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="business_signature" :value="signatureValue" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Default Signature</label>

                            <!-- Draw Signature Modal -->
                            <div x-show="drawModalOpen" 
                                 x-transition 
                                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                                 x-cloak>
                                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl w-full max-w-md shadow-mui-24 overflow-hidden" @click.away="closeDrawModal()">
                                    <div class="px-4 py-3 border-b border-slate-100 dark:border-zinc-800/80 flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-808 dark:text-zinc-50 uppercase tracking-wider">Draw Default Signature</span>
                                        <button type="button" @click="closeDrawModal" class="text-slate-400 hover:text-slate-655 dark:hover:text-white p-1">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                    <div class="p-4 bg-slate-50 dark:bg-zinc-950 flex flex-col items-center">
                                        <canvas x-ref="canvas" width="360" height="150" class="border border-slate-200 dark:border-zinc-800 bg-white rounded-lg cursor-crosshair shadow-inner max-w-full"></canvas>
                                        <p class="text-[10px] text-slate-400 mt-2">Draw inside the canvas. It will save as your default billing signature.</p>
                                    </div>
                                    <div class="px-4 py-3 border-t border-slate-100 dark:border-zinc-800/80 flex justify-between bg-slate-50/50">
                                        <button type="button" @click="resetCanvas" class="px-3 py-1.5 text-xs font-bold text-slate-655 dark:text-zinc-400 hover:bg-slate-150 border border-slate-200 dark:border-zinc-800 rounded-lg">
                                            Clear
                                        </button>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="closeDrawModal" class="px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg">
                                                Cancel
                                            </button>
                                            <button type="button" @click="saveCanvas" class="px-4 py-1.5 text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg">
                                                Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-xs tracking-wide shadow-mui-1 hover:shadow-mui-2 cursor-pointer transition-all mt-6 hover:-translate-y-0.5 active:translate-y-0">
                        Update Business Defaults
                    </button>
                </form>
            </div>

            <!-- TAB 3: PRODUCT DYNAMIC FIELDS BUILDER -->
            <div x-show="settingsTab === 'fields'" x-transition>
                <h2 class="text-sm font-black text-slate-800 dark:text-zinc-50 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-indigo-600"></i>
                    Product Custom Fields Builder
                </h2>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mb-6 font-bold uppercase tracking-wider">Define dynamic attributes, inputs, validations, and helper instructions for products.</p>

                <form action="{{ route('profile.update') }}" method="POST" class="flex flex-col gap-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $user->name }}" />
                    <input type="hidden" name="email" value="{{ $user->email }}" />
                    <input type="hidden" name="product_custom_fields_json" :value="JSON.stringify(fields)" />

                    <!-- Field Cards List -->
                    <div class="flex flex-col gap-4">
                        <template x-for="(field, index) in fields" :key="index">
                            <div class="p-4 bg-slate-50 dark:bg-zinc-950/30 border border-slate-205 dark:border-zinc-800/80 rounded-xl flex flex-col gap-3.5 relative group shadow-sm transition-all hover:border-slate-300 dark:hover:border-zinc-700">
                                
                                <!-- Delete Button -->
                                <button type="button" @click="fields.splice(index, 1)" class="absolute right-3 top-3 text-slate-400 hover:text-rose-600 transition-colors p-1 cursor-pointer" title="Delete Field">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Field Name/Label -->
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Attribute Name / Label *</label>
                                        <input type="text" x-model="field.name" required placeholder="e.g., Color" class="w-full bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white" />
                                    </div>

                                    <!-- Field Input Type -->
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Input Field Type *</label>
                                        <select x-model="field.type" class="w-full bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white">
                                            <option value="text">Text Input</option>
                                            <option value="number">Numeric Value</option>
                                            <option value="url">URL Address</option>
                                            <option value="email">Email Address</option>
                                        </select>
                                    </div>

                                    <!-- Required Switch -->
                                    <div class="flex items-center md:pt-6 select-none">
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="field.required" class="rounded border-slate-300 dark:border-zinc-800 text-indigo-650 focus:ring-indigo-500 focus:outline-none h-4 w-4" />
                                            <span class="text-xs font-bold text-slate-705 dark:text-zinc-300">Required Input</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Min Limit -->
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" x-text="field.type === 'number' ? 'Minimum Value' : 'Minimum Length'"></label>
                                        <input type="number" x-model="field.min" placeholder="e.g. 0" class="w-full bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white font-mono" />
                                    </div>

                                    <!-- Max Limit -->
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" x-text="field.type === 'number' ? 'Maximum Value' : 'Maximum Length'"></label>
                                        <input type="number" x-model="field.max" placeholder="e.g. 100" class="w-full bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white font-mono" />
                                    </div>

                                    <!-- Help tip text -->
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Inline Help Tip / Instruction</label>
                                        <input type="text" x-model="field.tip" placeholder="e.g., Color tag of item" class="w-full bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white" />
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <div x-show="fields.length === 0" class="text-center py-10 border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-2xl select-none">
                            <i class="fa-solid fa-list-check text-3xl text-slate-300 dark:text-zinc-700 mb-3 animate-pulse"></i>
                            <p class="text-xs font-black text-slate-600 dark:text-zinc-400 uppercase tracking-wider">No dynamic fields configured.</p>
                            <p class="text-[10px] text-slate-400 dark:text-zinc-550 mt-1 leading-normal max-w-xs mx-auto">
                                Click the button below to configure fields like Warranty, Color, Weight, or Serial Numbers.
                            </p>
                        </div>
                    </div>

                    <!-- Builder actions -->
                    <div class="flex justify-between items-center mt-3 border-t border-slate-100 dark:border-zinc-800/60 pt-4">
                        <button type="button" @click="fields.push({ name: '', type: 'text', required: false, tip: '', min: '', max: '' })" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 dark:bg-zinc-850 dark:hover:bg-zinc-800 text-slate-700 dark:text-zinc-300 text-xs font-bold rounded-lg border border-slate-200 dark:border-zinc-800 transition-all cursor-pointer">
                            <i class="fa-solid fa-plus mr-1"></i> Add Custom Attribute
                        </button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-750 text-white text-xs font-bold rounded-lg shadow-sm transition-all cursor-pointer">
                            Save Dynamic Schema
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB 4: API INTEGRATIONS -->
            <div x-show="settingsTab === 'integrations'" x-transition>
                <h2 class="text-sm font-black text-slate-800 dark:text-zinc-50 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-key text-indigo-600"></i>
                    API Integrations & Storage
                </h2>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mb-6 font-bold uppercase tracking-wider">Manage external storage APIs and credentials for file hosting services.</p>

                <form action="{{ route('profile.update') }}" method="POST" class="flex flex-col gap-5 max-w-md">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="name" value="{{ $user->name }}" />
                    <input type="hidden" name="email" value="{{ $user->email }}" />

                    <!-- ImgBB API Settings -->
                    <div class="relative mt-2">
                        <input type="text" name="imgbb_api_key" id="imgbb_api_key" value="{{ old('imgbb_api_key', $user->settings['imgbb_api_key'] ?? '') }}" placeholder="Using system-configured default key" class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3.5 py-2.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600 font-mono" />
                        <label for="imgbb_api_key" class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-3 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">ImgBB API Key (Optional)</label>
                    </div>
                    
                    <div class="bg-indigo-50/50 dark:bg-indigo-950/10 border border-indigo-100/20 rounded-xl p-4 flex flex-col gap-1.5 select-none">
                        <div class="flex items-center gap-1.5 text-indigo-700 dark:text-indigo-455 text-[10px] font-extrabold uppercase tracking-wider">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Secure Backend CDN Uploads</span>
                        </div>
                        <p class="text-[10px] text-slate-500 dark:text-zinc-400 leading-normal font-medium">
                            If you leave this field empty, the system default key is securely used from the server. Enter a custom key if you want uploads hosted on your personal ImgBB account.
                        </p>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-xs tracking-wide shadow-mui-1 hover:shadow-mui-2 cursor-pointer transition-all mt-2 hover:-translate-y-0.5 active:translate-y-0">
                        Update API Keys
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Signature Pad for Settings
        Alpine.data('settingsSignaturePad', () => ({
            drawModalOpen: false,
            signatureValue: '',
            isDrawing: false,
            ctx: null,

            init() {
                this.signatureValue = this.$parent.business_signature || '';
                this.$watch('signatureValue', value => {
                    this.$parent.business_signature = value;
                });
            },

            openDrawModal() {
                this.drawModalOpen = true;
                this.$nextTick(() => {
                    const canvas = this.$refs.canvas;
                    this.ctx = canvas.getContext('2d');
                    this.ctx.strokeStyle = '#000000';
                    this.ctx.lineWidth = 2.5;
                    this.ctx.lineCap = 'round';
                    
                    const getMousePos = (e) => {
                        const rect = canvas.getBoundingClientRect();
                        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                        return {
                            x: clientX - rect.left,
                            y: clientY - rect.top
                        };
                    };

                    const startDrawing = (e) => {
                        this.isDrawing = true;
                        const pos = getMousePos(e);
                        this.ctx.beginPath();
                        this.ctx.moveTo(pos.x, pos.y);
                        e.preventDefault();
                    };

                    const draw = (e) => {
                        if (!this.isDrawing) return;
                        const pos = getMousePos(e);
                        this.ctx.lineTo(pos.x, pos.y);
                        this.ctx.stroke();
                        e.preventDefault();
                    };

                    const stopDrawing = () => {
                        this.isDrawing = false;
                    };

                    canvas.addEventListener('mousedown', startDrawing);
                    canvas.addEventListener('mousemove', draw);
                    canvas.addEventListener('mouseup', stopDrawing);
                    canvas.addEventListener('mouseleave', stopDrawing);

                    canvas.addEventListener('touchstart', startDrawing, { passive: false });
                    canvas.addEventListener('touchmove', draw, { passive: false });
                    canvas.addEventListener('touchend', stopDrawing);
                });
            },

            closeDrawModal() {
                this.drawModalOpen = false;
            },

            resetCanvas() {
                const canvas = this.$refs.canvas;
                this.ctx.clearRect(0, 0, canvas.width, canvas.height);
            },

            clearSignature() {
                this.signatureValue = '';
            },

            saveCanvas() {
                const canvas = this.$refs.canvas;
                const dataUrl = canvas.toDataURL();
                this.signatureValue = dataUrl;
                this.closeDrawModal();
            }
        }));

        // Profile Data Scope
        Alpine.data('profileSettings', () => ({
            business_logo: '{{ $user->business_logo ?? '' }}',
            business_signature: '{{ $user->business_signature ?? '' }}',
            settingsTab: 'profile',
            fields: @json($user->settings['product_custom_fields'] ?? []),

            init() {
                // Migrate legacy simple string fields to full object structure
                this.fields = this.fields.map(f => {
                    if (typeof f === 'string') {
                        return { name: f, type: 'text', required: false, tip: '', min: '', max: '' };
                    }
                    // Coerce boolean inputs
                    if (f.required === 'true') f.required = true;
                    if (f.required === 'false') f.required = false;
                    return f;
                });
            }
        }));
    });
</script>
@endpush
