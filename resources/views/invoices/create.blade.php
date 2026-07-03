@extends('invoices.layout')

@section('content')
<div x-data="invoiceEditor" class="min-h-screen flex flex-col">
    
    <!-- Top Action Bar -->
    <div class="no-print border-b border-slate-200/60 dark:border-zinc-800/60 bg-white dark:bg-zinc-900 sticky top-14 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-12 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('invoices.index') }}" class="text-xs font-bold text-slate-500 hover:text-indigo-600 dark:text-zinc-400 dark:hover:text-indigo-400 flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Invoices</span>
                </a>
                <span class="text-slate-300 dark:text-zinc-800">|</span>
                <span class="text-xs font-bold text-slate-800 dark:text-zinc-200" x-text="invoice_number || 'New Invoice'"></span>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Print Preview Button -->
                @auth
                    <button type="button" @click="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg border border-slate-200 dark:border-zinc-850 shadow-sm cursor-pointer transition-all">
                        <i class="fa-solid fa-print"></i>
                        <span>Print / Save PDF</span>
                    </button>
                @else
                    <div x-data="{ showWarning: false }" class="relative inline-block text-left">
                        <button type="button" @click="showWarning = !showWarning" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold bg-slate-400 hover:bg-slate-500 text-white rounded-lg shadow-sm cursor-pointer transition-all">
                            <i class="fa-solid fa-lock text-[10px]"></i>
                            <span>Print / Save PDF</span>
                        </button>
                        <!-- Warning Popover -->
                        <div x-show="showWarning" 
                             @click.away="showWarning = false"
                             x-transition
                             class="absolute right-0 mt-2 w-72 rounded-xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 shadow-mui-8 z-50 p-4 font-sans text-left"
                             x-cloak>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-zinc-100 flex items-center gap-1.5">
                                <i class="fa-solid fa-lock text-indigo-500"></i> Account Required
                            </h4>
                            <p class="text-[10px] text-slate-500 dark:text-zinc-400 mt-1 leading-relaxed">
                                Sign up or log in to print and download PDFs. Your current invoice draft will be automatically linked to your new account dashboard!
                            </p>
                            <div class="mt-3 flex gap-2">
                                <a href="{{ route('register') }}" class="flex-1 text-center py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold rounded-lg transition-all">
                                    Register Free
                                </a>
                                <a href="{{ route('login') }}" class="flex-1 text-center py-1.5 border border-slate-200 dark:border-zinc-800 text-slate-700 dark:text-zinc-300 text-[10px] font-bold rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                                    Log In
                                </a>
                            </div>
                        </div>
                    </div>
                @endauth

                <!-- Submit Button -->
                <button type="button" @click="$refs.invoiceForm.submit()" class="shine-button inline-flex items-center gap-2 px-5 py-2 text-xs font-extrabold uppercase tracking-wider bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-mui-2 hover:shadow-mui-8 active:scale-95 transition-all cursor-pointer duration-300">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                    <span>Save Invoice</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex-grow flex flex-col lg:flex-row gap-6">
        
        <!-- Left: Form Editor (Sidebar) -->
        <div class="no-print w-full lg:w-1/2 flex flex-col gap-4 editor-sidebar">
            
            <form x-ref="invoiceForm" action="{{ isset($invoice) ? route('invoices.update', $invoice) : route('invoices.store') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                @if(isset($invoice))
                    @method('PUT')
                @endif

                <!-- Hidden computed totals for backend validation -->
                <input type="hidden" name="subtotal" :value="subtotal" />
                <input type="hidden" name="total" :value="total" />

                <!-- Validation Errors Alert Block -->
                @if ($errors->any())
                    <div class="bg-rose-50 dark:bg-rose-950/20 border border-rose-200/35 dark:border-rose-900/30 rounded-xl p-3.5 shadow-sm">
                        <div class="flex items-start gap-2.5">
                            <i class="fa-solid fa-triangle-exclamation text-rose-500 mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-bold text-rose-800 dark:text-rose-400 uppercase tracking-wider">Please check validation errors:</h4>
                                <ul class="list-disc list-inside text-[10px] text-rose-700 dark:text-rose-300/80 mt-1 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 1. Layout & Styling Settings -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-850 rounded-xl p-4 shadow-sm flex flex-col gap-4">
                    <h2 class="text-xs font-bold text-slate-800 dark:text-zinc-50 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-zinc-800/40 pb-2">
                        <i class="fa-solid fa-palette text-indigo-600"></i>
                        Invoice Design & Layout
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Template Selector (Trigger Modal) -->
                        <div class="relative mt-2">
                            <button type="button" @click="templateModalOpen = true" class="w-full flex items-center justify-between rounded-lg border border-slate-350 dark:border-zinc-805 bg-transparent px-3.5 py-2 text-xs text-slate-800 dark:text-zinc-100 hover:bg-slate-50 dark:hover:bg-zinc-850/50 transition-all text-left select-none">
                                <span class="font-bold flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-signature text-indigo-600"></i>
                                    <span x-text="getTemplateLabel(template_id)"></span>
                                </span>
                                <i class="fa-solid fa-angle-down text-slate-400"></i>
                            </button>
                            <input type="hidden" name="template_id" :value="template_id" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Design Template</label>
                        </div>
                        
                        <!-- Font Selector -->
                        <div class="relative mt-2">
                            <select x-model="selected_font" class="w-full rounded-lg border border-slate-305 dark:border-zinc-800 bg-transparent px-3 py-2 text-xs text-slate-850 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600">
                                <option value="font-sans">Instrument Sans (Modern)</option>
                                <option value="font-serif">Playfair Display (Elegant)</option>
                                <option value="font-mono">JetBrains Mono (Technical)</option>
                            </select>
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Typography Font</label>
                        </div>

                        <!-- Brand Primary Color Picker -->
                        <div class="relative mt-2">
                            <div class="flex items-center gap-2">
                                <input type="color" name="theme_color" x-model="theme_color" class="h-8 w-10 rounded border border-slate-300 dark:border-zinc-800 cursor-pointer p-0.5 bg-slate-50 dark:bg-zinc-950" />
                                <input type="text" x-model="theme_color" class="flex-grow rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-855 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600 uppercase" maxlength="7" />
                            </div>
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Brand Accent Color</label>
                        </div>

                        <!-- Invoice Status -->
                        <div class="relative mt-2">
                            <select name="status" x-model="status" class="w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-2 text-xs text-slate-855 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600">
                                <option value="draft">Draft (Unsent)</option>
                                <option value="sent">Sent (Awaiting Payment)</option>
                                <option value="paid">Paid (Settled)</option>
                                <option value="overdue">Overdue</option>
                            </select>
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Invoice Status</label>
                        </div>
                    </div>
                </div>

                <!-- 2. Invoice Meta Details -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-850 rounded-xl p-4 shadow-sm flex flex-col gap-4">
                    <h2 class="text-xs font-bold text-slate-800 dark:text-zinc-50 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-zinc-800/40 pb-2">
                        <i class="fa-solid fa-fingerprint text-indigo-600"></i>
                        Identification & Dates
                    </h2>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <!-- Invoice number -->
                        <div class="relative mt-2">
                            <input type="text" name="invoice_number" x-model="invoice_number" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Invoice #</label>
                        </div>
                        
                        <!-- Issue Date -->
                        <div class="relative mt-2">
                            <input type="date" name="invoice_date" x-model="invoice_date" required class="w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1 text-xs text-slate-808 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Issue Date</label>
                        </div>

                        <!-- Due Date -->
                        <div class="relative mt-2">
                            <input type="date" name="due_date" x-model="due_date" class="w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1 text-xs text-slate-808 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Due Date</label>
                        </div>
                    </div>
                </div>

                <!-- 3. Sender Details -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-850 rounded-xl p-4 shadow-sm flex flex-col gap-4">
                    <h2 class="text-xs font-bold text-slate-800 dark:text-zinc-50 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-zinc-800/40 pb-2">
                        <i class="fa-solid fa-building-user text-amber-600"></i>
                        Bill From (Sender)
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Sender Name -->
                        <div class="relative mt-2">
                            <input type="text" name="sender_name" x-model="sender_name" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-450 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Business Name</label>
                        </div>

                        <!-- Sender Email -->
                        <div class="relative mt-2">
                            <input type="email" name="sender_email" x-model="sender_email" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-450 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Email Address</label>
                        </div>

                        <!-- Phone -->
                        <div class="relative mt-2">
                            <input type="text" name="sender_phone" x-model="sender_phone" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-450 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Phone Number</label>
                        </div>

                        <!-- Tax ID -->
                        <div class="relative mt-2">
                            <input type="text" name="sender_additional[tax_id]" x-model="sender_tax_id" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-450 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Tax ID / VAT Registration</label>
                        </div>
                    </div>

                    <!-- Website URL -->
                    <div class="relative mt-2">
                        <input type="text" name="sender_additional[website]" x-model="sender_website" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                        <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-450 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Website URL</label>
                    </div>

                    <!-- Address -->
                    <div class="relative mt-2">
                        <textarea name="sender_address" x-model="sender_address" rows="2" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-2 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600 resize-none"></textarea>
                        <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-455 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Sender Physical Address</label>
                    </div>

                    <!-- Logo Upload widget -->
                    <div x-data="{ logoPreview: sender_logo }" class="relative mt-3 pt-2 border-t border-slate-100 dark:border-zinc-800/40">
                        <div class="flex items-center gap-4">
                            <!-- Logo Preview Bubble -->
                            <div class="h-14 w-14 rounded-lg border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-950/60 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" class="h-full w-full object-contain" />
                                </template>
                                <template x-if="!logoPreview">
                                    <i class="fa-solid fa-image text-slate-300 dark:text-zinc-700 text-lg"></i>
                                </template>
                            </div>
                            <!-- Action Buttons -->
                            <div class="flex flex-col gap-1">
                                <label class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 dark:bg-zinc-800 dark:hover:bg-zinc-750 text-slate-800 dark:text-zinc-200 text-[10px] font-bold rounded-lg border border-slate-205 dark:border-zinc-700 cursor-pointer shadow-sm text-center select-none active:scale-95 transition-all">
                                    Upload Logo
                                    <input type="file" accept="image/*" @change="
                                        const file = $event.target.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                logoPreview = e.target.result;
                                                sender_logo = e.target.result;
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    " class="hidden" />
                                </label>
                                <button x-show="logoPreview" type="button" @click="logoPreview = ''; sender_logo = ''; logo_width = 110; logo_height = 40; logo_x = 0; logo_y = 0;" class="text-[9px] font-bold text-rose-600 hover:underline text-left cursor-pointer select-none">
                                    Remove Logo
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="sender_additional[logo]" :value="sender_logo" />
                        <input type="hidden" name="sender_additional[logo_width]" :value="logo_width" />
                        <input type="hidden" name="sender_additional[logo_height]" :value="logo_height" />
                        <input type="hidden" name="sender_additional[logo_x]" :value="logo_x" />
                        <input type="hidden" name="sender_additional[logo_y]" :value="logo_y" />
                        <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Business Logo</label>
                    </div>
                </div>

                <!-- 4. Client Details -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-850 rounded-xl p-4 shadow-sm flex flex-col gap-4">
                    <h2 class="text-xs font-bold text-slate-850 dark:text-zinc-50 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-zinc-800/40 pb-2">
                        <i class="fa-solid fa-users text-emerald-600"></i>
                        Bill To (Client)
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Client Name -->
                        <div class="relative mt-2">
                            <input type="text" name="client_name" x-model="client_name" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-450 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Client Name</label>
                        </div>

                        <!-- Client Company -->
                        <div class="relative mt-2">
                            <input type="text" name="client_additional[company]" x-model="client_company" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-455 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Client Company</label>
                        </div>

                        <!-- Client Email -->
                        <div class="relative mt-2">
                            <input type="email" name="client_email" x-model="client_email" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-455 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Email Address</label>
                        </div>

                        <!-- Phone -->
                        <div class="relative mt-2">
                            <input type="text" name="client_phone" x-model="client_phone" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-455 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Phone Number</label>
                        </div>
                    </div>

                    <!-- Client VAT -->
                    <div class="relative mt-2">
                        <input type="text" name="client_additional[vat_number]" x-model="client_vat_number" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                        <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-455 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Client VAT / Registration</label>
                    </div>

                    <!-- Address -->
                    <div class="relative mt-2">
                        <textarea name="client_address" x-model="client_address" rows="2" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-2 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600 resize-none"></textarea>
                        <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-450 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Client Address</label>
                    </div>
                </div>

                <!-- 5. Line Items & Calculations -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-850 rounded-xl p-4 shadow-sm">
                    <div class="flex justify-between items-center border-b border-slate-100 dark:border-zinc-800/40 pb-2 mb-3">
                        <h2 class="text-xs font-bold text-slate-808 dark:text-zinc-50 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-list-ol text-rose-500"></i>
                            Invoice Line Items
                        </h2>
                        <button type="button" @click="addItem" class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:hover:bg-indigo-950/60 dark:text-indigo-400 px-2.5 py-1.5 rounded text-[10px] font-bold transition-all cursor-pointer">
                            <i class="fa-solid fa-plus"></i>
                            Add Row
                        </button>
                    </div>

                    <div class="flex flex-col gap-3.5 divide-y divide-slate-100 dark:divide-zinc-800/40">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="pt-3.5 first:pt-0 flex flex-col gap-3">
                                <div class="flex items-start gap-2">
                                    <!-- Description -->
                                    <div class="relative flex-grow mt-2">
                                        <input type="text" x-model="item.description" :name="'items['+index+'][description]'" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-808 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                                        <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Description / Service</label>
                                    </div>
                                    <!-- Delete Item Button -->
                                    <button type="button" @click="removeItem(index)" class="mt-4 text-slate-405 hover:text-rose-600 transition-colors p-1.5 rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-850" :disabled="items.length <= 1">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                                <div class="grid grid-cols-4 gap-2">
                                    <!-- Qty -->
                                    <div class="relative mt-2">
                                        <input type="number" x-model="item.quantity" :name="'items['+index+'][quantity]'" step="any" min="0.01" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-2 py-1.5 text-xs text-slate-808 dark:text-zinc-100 text-center placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                                        <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-[11px] peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[9px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Qty</label>
                                    </div>
                                    <!-- Rate -->
                                    <div class="relative mt-2">
                                        <input type="number" x-model="item.rate" :name="'items['+index+'][rate]'" step="0.01" min="0" required placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-2 py-1.5 text-xs text-slate-808 dark:text-zinc-100 text-center placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                                        <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-[11px] peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[9px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Rate</label>
                                    </div>
                                    <!-- Tax % -->
                                    <div class="relative mt-2">
                                        <input type="number" x-model="item.tax_rate" :name="'items['+index+'][tax_rate]'" min="0" max="100" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-2 py-1.5 text-xs text-slate-808 dark:text-zinc-100 text-center placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                                        <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-[11px] peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[9px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Tax %</label>
                                    </div>
                                    <!-- Discount % -->
                                    <div class="relative mt-2">
                                        <input type="number" x-model="item.discount_rate" :name="'items['+index+'][discount_rate]'" min="0" max="100" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-2 py-1.5 text-xs text-slate-808 dark:text-zinc-100 text-center placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                                        <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-[11px] peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[9px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Disc %</label>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 6. General Taxes, Shipping & Totals -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-855 rounded-xl p-4 shadow-sm flex flex-col gap-4">
                    <h2 class="text-xs font-bold text-slate-855 dark:text-zinc-50 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-zinc-800/40 pb-2">
                        <i class="fa-solid fa-calculator text-indigo-600"></i>
                        Global Taxes, Shipping & Currency
                    </h2>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <!-- Global Tax -->
                        <div class="relative mt-2">
                            <input type="number" name="tax_rate" x-model="tax_rate" min="0" max="100" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-2 py-1.5 text-xs text-center placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-[11px] peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[9px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Global Tax %</label>
                        </div>
                        
                        <!-- Global Discount -->
                        <div class="relative mt-2">
                            <input type="number" name="discount_rate" x-model="discount_rate" min="0" max="100" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-2 py-1.5 text-xs text-center placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-[11px] peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[9px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Global Disc %</label>
                        </div>

                        <!-- Shipping -->
                        <div class="relative mt-2">
                            <input type="number" name="shipping_cost" x-model="shipping_cost" min="0" step="0.01" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-2 py-1.5 text-xs text-center placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-[11px] peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[9px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Shipping Cost</label>
                        </div>

                        <!-- Currency -->
                        <div class="relative mt-2">
                            <select name="currency" x-model="currency" class="w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-1.5 text-xs text-slate-800 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600">
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                                <option value="INR">INR (₹)</option>
                            </select>
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Currency</label>
                        </div>
                    </div>

                    <!-- Computed Total Badge -->
                    <div class="bg-slate-50 dark:bg-zinc-950 p-2.5 rounded-lg flex items-center justify-between text-xs border border-slate-200/50 dark:border-zinc-850 shadow-inner">
                        <span class="font-bold text-slate-450 dark:text-zinc-500 uppercase tracking-wider">Estimated Total Due</span>
                        <span class="text-base font-black text-indigo-600 dark:text-indigo-400" x-text="currencySymbol + formatNumber(total)"></span>
                    </div>
                </div>

                <!-- 7. Footer Notes & Contract Terms -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-850 rounded-xl p-4 shadow-sm flex flex-col gap-4">
                    <h2 class="text-xs font-bold text-slate-855 dark:text-zinc-50 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-zinc-800/40 pb-2">
                        <i class="fa-solid fa-file-contract text-violet-500"></i>
                        Terms, Conditions & Notes
                    </h2>
                    
                    <div class="flex flex-col gap-4">
                        <div class="relative mt-2">
                            <textarea name="notes" x-model="notes" rows="2" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-2 text-xs text-slate-805 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600 resize-none"></textarea>
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Invoice Notes (Visible to client)</label>
                        </div>
                        <div class="relative mt-2">
                            <textarea name="terms" x-model="terms" rows="2" placeholder=" " class="peer w-full rounded-lg border border-slate-300 dark:border-zinc-800 bg-transparent px-3 py-2 text-xs text-slate-805 dark:text-zinc-100 placeholder-transparent focus:outline-none focus:ring-1 focus:ring-indigo-600 focus:border-indigo-600 resize-none"></textarea>
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider transition-all peer-placeholder-shown:text-xs peer-placeholder-shown:text-slate-400 peer-placeholder-shown:top-2 peer-focus:-top-2 peer-focus:text-[10px] peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Terms & Conditions / Bank Details</label>
                        </div>

                        <!-- Draw Signature Widget -->
                        <div class="relative mt-3 pt-2 border-t border-slate-100 dark:border-zinc-800/40">
                            <div class="flex items-center gap-4">
                                <!-- Signature Preview Area -->
                                <div class="h-14 w-32 rounded-lg border border-slate-200 dark:border-zinc-805 bg-slate-50 dark:bg-zinc-950/60 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                                    <template x-if="sender_signature">
                                        <img :src="sender_signature" class="h-full w-full object-contain" />
                                    </template>
                                    <template x-if="!sender_signature">
                                        <span class="text-[9px] font-bold text-slate-400 dark:text-zinc-600 uppercase tracking-wider">No signature</span>
                                    </template>
                                </div>
                                <!-- Action Buttons -->
                                <div class="flex flex-col gap-1">
                                    <button type="button" @click="openDrawModal" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-105 dark:bg-zinc-850 dark:hover:bg-zinc-800 text-slate-850 dark:text-zinc-200 text-[10px] font-bold rounded-lg border border-slate-205 dark:border-zinc-705 shadow-sm cursor-pointer select-none active:scale-95 transition-all">
                                        Sign Invoice
                                    </button>
                                    <button x-show="sender_signature" type="button" @click="clearSignature" class="text-[9px] font-bold text-rose-605 hover:underline text-left cursor-pointer select-none">
                                        Remove Signature
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="sender_additional[signature]" :value="sender_signature" />
                            <label class="absolute left-2.5 -top-2 bg-white dark:bg-zinc-900 px-1 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Authorized Signature</label>

                            <!-- Draw Signature Modal Overlay -->
                            <div x-show="drawModalOpen" 
                                 x-transition 
                                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                                 x-cloak>
                                <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl w-full max-w-md shadow-mui-24 overflow-hidden">
                                    <div class="px-4 py-3 border-b border-slate-100 dark:border-zinc-800/80 flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800 dark:text-zinc-50 uppercase tracking-wider">Draw Authorized Signature</span>
                                        <button type="button" @click="closeDrawModal" class="text-slate-400 hover:text-slate-650 dark:hover:text-white p-1">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                    <div class="p-4 bg-slate-50 dark:bg-zinc-950 flex flex-col items-center">
                                        <!-- Canvas -->
                                        <canvas x-ref="canvas" width="360" height="150" class="border border-slate-200 dark:border-zinc-800 bg-white rounded-lg cursor-crosshair shadow-inner max-w-full"></canvas>
                                        <p class="text-[10px] text-slate-400 mt-2">Use your mouse or finger/stylus to draw your signature inside the area.</p>
                                    </div>
                                    <div class="px-4 py-3 border-t border-slate-105 dark:border-zinc-800/80 flex justify-between bg-slate-50/50">
                                        <button type="button" @click="resetCanvas" class="px-3 py-1.5 text-xs font-bold text-slate-600 dark:text-zinc-400 hover:bg-slate-150 border border-slate-200 dark:border-zinc-800 rounded-lg">
                                            Reset/Clear
                                        </button>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="closeDrawModal" class="px-3 py-1.5 text-xs font-bold text-slate-750 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg">
                                                Cancel
                                            </button>
                                            <button type="button" @click="saveCanvas" class="px-4 py-1.5 text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                                                Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <!-- Right: Sticky Live Preview Panel -->
        <div class="w-full lg:w-1/2 preview-container">
            <div class="sticky top-28">
                <!-- Outer styling of preview wrapper -->
                <div class="mb-3 flex items-center justify-between no-print px-2 select-none">
                    <span class="text-xs font-bold text-slate-400 dark:text-zinc-400 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-laptop-code text-emerald-500 animate-pulse"></i>
                        Live Design Canvas
                    </span>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-zinc-400 bg-slate-200/50 dark:bg-zinc-800/85 px-2 py-0.5 rounded shadow-sm" x-text="template_id.toUpperCase() + ' BLUEPRINT'"></span>
                </div>
                
                <!-- High-fidelity preview card -->
                <div class="print-card bg-white text-zinc-855 p-6 shadow-mui-2 hover:shadow-mui-8 rounded-xl aspect-[1/1.41] overflow-y-auto border border-slate-200 dark:border-zinc-900 max-w-full font-sans transition-all duration-300 flex flex-col" :class="selected_font">
                    
                    <!-- ============================================= -->
                    <!-- TEMPLATE 5: OPEN SOURCE BLUEPRINT             -->
                    <!-- ============================================= -->
                    <div x-show="template_id === 'blueprint'" class="flex-grow flex flex-col gap-5 text-xs text-slate-800 leading-normal">
                        <!-- Top Header Banner -->
                        <div class="border-t-4 pt-4 flex justify-between items-start gap-4" :style="'border-color: ' + theme_color">
                            <div>
                                <!-- Logo preview inside blueprint template -->
                                <template x-if="sender_logo">
                                    <div class="group/logo relative inline-block select-none hover:outline hover:outline-dashed hover:outline-indigo-500/50"
                                         :style="'width: ' + logo_width + 'px; height: ' + logo_height + 'px; transform: translate(' + logo_x + 'px, ' + logo_y + 'px); cursor: move; margin-bottom: 0.75rem;'"
                                         @mousedown="startDrag($event)"
                                         @touchstart="startDrag($event)">
                                        <img :src="sender_logo" class="w-full h-full object-contain pointer-events-none" />
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-indigo-600 rounded-full cursor-se-resize flex items-center justify-center shadow no-print opacity-0 group-hover/logo:opacity-100 transition-opacity"
                                             @mousedown.stop="startResize($event)"
                                             @touchstart.stop="startResize($event)">
                                        </div>
                                    </div>
                                </template>
                                <h1 class="text-2xl font-black uppercase tracking-tight" :style="'color: ' + theme_color">INVOICE</h1>
                                <span class="text-xs text-slate-505 block mt-1">Ref: <span x-text="invoice_number" class="font-bold text-slate-800"></span></span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-sm block" x-text="sender_name || 'Business Name'"></span>
                                <span class="text-[11px] text-slate-505 block mt-0.5" x-text="sender_email"></span>
                                <span class="text-[11px] text-slate-505 block" x-text="sender_phone"></span>
                                <span class="text-[11px] text-slate-400 block whitespace-pre-line mt-1" x-text="sender_address"></span>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-3 gap-4 border-y border-slate-200 py-3 bg-slate-50/50 p-3 rounded-lg">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Issued On</span>
                                <span class="text-xs font-semibold mt-0.5 block" x-text="invoice_date"></span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Payment Due</span>
                                <span class="text-xs font-semibold mt-0.5 block" x-text="due_date || 'Due on Receipt'"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Amount Due</span>
                                <span class="text-sm font-bold mt-0.5 block" :style="'color: ' + theme_color" x-text="currencySymbol + formatNumber(total)"></span>
                            </div>
                        </div>

                        <!-- From / To details -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Billing To:</span>
                                <div class="text-[11px] text-slate-700 bg-slate-50/40 p-2.5 rounded-lg border border-slate-200/50">
                                    <strong x-text="client_name" class="text-slate-850 block"></strong>
                                    <span x-show="client_company" x-text="client_company" class="block font-medium"></span>
                                    <span x-show="client_email" x-text="client_email" class="block"></span>
                                    <span x-show="client_phone" x-text="client_phone" class="block"></span>
                                    <span x-text="client_address" class="block whitespace-pre-line mt-1"></span>
                                </div>
                            </div>
                            <div class="text-right flex flex-col justify-between items-end">
                                <div class="text-[10px] text-slate-500 leading-relaxed font-sans not-italic">
                                    <div x-show="sender_tax_id"><span class="font-bold text-slate-400">TAX / VAT ID:</span> <span x-text="sender_tax_id" class="text-slate-700 font-semibold"></span></div>
                                    <div x-show="sender_website" class="mt-0.5"><span class="font-bold text-slate-400">Website:</span> <span x-text="sender_website" class="text-slate-700 font-semibold"></span></div>
                                    <div x-show="client_vat_number" class="mt-2"><span class="font-bold text-slate-400">Client VAT:</span> <span x-text="client_vat_number" class="text-slate-700 font-semibold"></span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="flex-grow">
                            <table class="w-full text-left border border-slate-200 rounded-lg overflow-hidden border-collapse font-sans not-italic">
                                <thead>
                                    <tr class="bg-slate-100/85 border-b border-slate-200 text-[9px] font-bold text-slate-500 uppercase tracking-wider">
                                        <th class="py-2.5 px-3">Description</th>
                                        <th class="py-2.5 px-3 text-center w-[12%]">Qty</th>
                                        <th class="py-2.5 px-3 text-right w-[18%]">Rate</th>
                                        <th class="py-2.5 px-3 text-right w-[15%]">Tax</th>
                                        <th class="py-2.5 px-3 text-right w-[20%]">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-[11px] text-slate-750 font-sans not-italic">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="hover:bg-slate-50/20">
                                            <td class="py-2.5 px-3 font-semibold text-slate-800" x-text="item.description || 'New Service Item'"></td>
                                            <td class="py-2.5 px-3 text-center text-slate-600" x-text="item.quantity"></td>
                                            <td class="py-2.5 px-3 text-right text-slate-600" x-text="currencySymbol + formatNumber(item.rate)"></td>
                                            <td class="py-2.5 px-3 text-right text-slate-500" x-text="item.tax_rate + '%'"></td>
                                            <td class="py-2.5 px-3 text-right font-bold text-slate-900" x-text="currencySymbol + formatNumber(item.quantity * item.rate)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals Block & Signature preview -->
                        <div class="flex justify-between items-end mt-4">
                            <!-- Left: Signature Preview -->
                            <div>
                                <template x-if="sender_signature">
                                    <div class="flex flex-col items-start">
                                        <div class="h-10 w-28 flex items-center justify-center overflow-hidden border-b border-slate-200 pb-1">
                                            <img :src="sender_signature" class="h-full w-full object-contain" />
                                        </div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Authorized Signature</span>
                                    </div>
                                </template>
                            </div>
                            <!-- Right: Totals -->
                            <div class="w-56 text-[11px] flex flex-col gap-1.5 border-t border-slate-200 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-slate-505">Subtotal</span>
                                    <span class="font-semibold text-slate-800" x-text="currencySymbol + formatNumber(itemSubtotal)"></span>
                                </div>
                                <div x-show="totalDiscountAmount > 0" class="flex justify-between text-rose-600">
                                    <span>Discounts</span>
                                    <span>- <span x-text="currencySymbol + formatNumber(totalDiscountAmount)"></span></span>
                                </div>
                                <div x-show="totalTaxAmount > 0" class="flex justify-between">
                                    <span class="text-slate-505">Tax</span>
                                    <span class="font-semibold text-slate-800" x-text="currencySymbol + formatNumber(totalTaxAmount)"></span>
                                </div>
                                <div x-show="shipping_cost > 0" class="flex justify-between">
                                    <span class="text-slate-505">Shipping</span>
                                    <span class="font-semibold text-slate-800" x-text="currencySymbol + formatNumber(shipping_cost)"></span>
                                </div>
                                <div class="flex justify-between text-xs font-bold border-y-2 border-slate-300 py-1.5 text-slate-900 mt-1.5">
                                    <span>Total Due</span>
                                    <span x-text="currencySymbol + formatNumber(total)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer notes -->
                        <div x-show="notes || terms" class="border-t border-slate-200 pt-3 mt-auto">
                            <div class="grid grid-cols-2 gap-4 text-[9px] text-slate-400">
                                <div x-show="notes">
                                    <strong class="text-slate-500 font-bold block mb-1">Invoice Notes</strong>
                                    <p x-text="notes" class="leading-relaxed whitespace-pre-line"></p>
                                </div>
                                <div x-show="terms">
                                    <strong class="text-slate-500 font-bold block mb-1">Terms & Instructions</strong>
                                    <p x-text="terms" class="leading-relaxed whitespace-pre-line"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================= -->
                    <!-- TEMPLATE 1: MODERN MINIMALIST                 -->
                    <!-- ============================================= -->
                    <div x-show="template_id === 'modern'" class="flex-grow flex flex-col gap-6">
                        <!-- Top Header -->
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <!-- Logo preview inside modern template -->
                                <template x-if="sender_logo">
                                    <div class="group/logo relative inline-block select-none hover:outline hover:outline-dashed hover:outline-indigo-500/50"
                                         :style="'width: ' + logo_width + 'px; height: ' + logo_height + 'px; transform: translate(' + logo_x + 'px, ' + logo_y + 'px); cursor: move; margin-bottom: 0.75rem;'"
                                         @mousedown="startDrag($event)"
                                         @touchstart="startDrag($event)">
                                        <img :src="sender_logo" class="w-full h-full object-contain pointer-events-none" />
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-indigo-600 rounded-full cursor-se-resize flex items-center justify-center shadow no-print opacity-0 group-hover/logo:opacity-100 transition-opacity"
                                             @mousedown.stop="startResize($event)"
                                             @touchstart.stop="startResize($event)">
                                        </div>
                                    </div>
                                </template>
                                <h1 class="text-4xl font-extrabold tracking-tight uppercase" :style="'color: ' + theme_color">INVOICE</h1>
                                <span class="text-xs font-semibold text-slate-400 mt-1 block">No: <span x-text="invoice_number" class="text-slate-800 font-bold"></span></span>
                            </div>
                            <div class="text-right text-xs">
                                <span class="text-base font-bold text-slate-800 block" x-text="sender_name || 'Your Company'"></span>
                                <span class="text-xs block text-slate-505" x-text="sender_email"></span>
                                <span class="text-xs block text-slate-505" x-text="sender_phone"></span>
                                <span class="text-xs block text-slate-400 mt-1 max-w-[200px] leading-snug break-words" x-text="sender_address"></span>
                            </div>
                        </div>

                        <!-- Info Bar -->
                        <div class="border-y border-slate-100 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Date Issued</span>
                                <span class="text-xs font-semibold text-slate-800 mt-0.5 block" x-text="invoice_date"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Due Date</span>
                                <span class="text-xs font-semibold text-slate-800 mt-0.5 block" x-text="due_date || 'Due on receipt'"></span>
                            </div>
                            <div class="col-span-2 text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Amount Due</span>
                                <span class="text-xl font-black mt-0.5 block" :style="'color: ' + theme_color" x-text="currencySymbol + formatNumber(total)"></span>
                            </div>
                        </div>

                        <!-- Client and Tax info -->
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Bill To</span>
                                <div class="text-xs text-slate-700 leading-relaxed">
                                    <strong x-text="client_name" class="text-slate-800 block"></strong>
                                    <span x-show="client_company" x-text="client_company" class="block font-medium"></span>
                                    <span x-show="client_email" x-text="client_email" class="block"></span>
                                    <span x-show="client_phone" x-text="client_phone" class="block"></span>
                                    <span x-text="client_address" class="block whitespace-pre-line mt-1 max-w-[220px]"></span>
                                </div>
                            </div>
                            <div class="text-right flex flex-col justify-between items-end">
                                <div class="text-xs text-slate-500 leading-normal">
                                    <div x-show="sender_tax_id"><span class="font-semibold text-slate-400">Sender Tax ID:</span> <span x-text="sender_tax_id"></span></div>
                                    <div x-show="sender_website" class="mt-0.5"><span class="font-semibold text-slate-400">Website:</span> <span x-text="sender_website"></span></div>
                                    <div x-show="client_vat_number" class="mt-2"><span class="font-semibold text-slate-400">Client VAT:</span> <span x-text="client_vat_number"></span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="flex-grow">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-slate-200 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th class="py-2.5">Item Description</th>
                                        <th class="py-2.5 text-center w-[10%]">Qty</th>
                                        <th class="py-2.5 text-right w-[15%]">Rate</th>
                                        <th class="py-2.5 text-right w-[20%]">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="py-3 font-semibold text-slate-800" x-text="item.description || 'New Service/Item'"></td>
                                            <td class="py-3 text-center text-slate-655" x-text="item.quantity"></td>
                                            <td class="py-3 text-right text-slate-655" x-text="currencySymbol + formatNumber(item.rate)"></td>
                                            <td class="py-3 text-right font-bold text-slate-900" x-text="currencySymbol + formatNumber(item.quantity * item.rate)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals Block & Signature preview -->
                        <div class="flex justify-between items-end border-t border-slate-100 pt-4">
                            <!-- Left: Signature Preview -->
                            <div>
                                <template x-if="sender_signature">
                                    <div class="flex flex-col items-start">
                                        <div class="h-10 w-28 flex items-center justify-center overflow-hidden border-b border-slate-200 pb-1">
                                            <img :src="sender_signature" class="h-full w-full object-contain" />
                                        </div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Authorized Signature</span>
                                    </div>
                                </template>
                            </div>
                            <!-- Right: Totals -->
                            <div class="w-64 text-xs flex flex-col gap-2">
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Subtotal</span>
                                    <span class="font-bold text-slate-800" x-text="currencySymbol + formatNumber(itemSubtotal)"></span>
                                </div>
                                <div x-show="totalDiscountAmount > 0" class="flex justify-between text-rose-600">
                                    <span>Line Discounts</span>
                                    <span>- <span x-text="currencySymbol + formatNumber(totalDiscountAmount)"></span></span>
                                </div>
                                <div x-show="discount_rate > 0" class="flex justify-between text-rose-600">
                                    <span>Global Discount (<span x-text="discount_rate"></span>%)</span>
                                    <span>- <span x-text="currencySymbol + formatNumber( (itemSubtotal - totalDiscountAmount) * (discount_rate/100) )"></span></span>
                                </div>
                                <div x-show="totalTaxAmount > 0" class="flex justify-between">
                                    <span class="text-slate-400">Line Tax</span>
                                    <span class="font-bold text-slate-800" x-text="currencySymbol + formatNumber(totalTaxAmount)"></span>
                                </div>
                                <div x-show="tax_rate > 0" class="flex justify-between">
                                    <span class="text-slate-400">Global Tax (<span x-text="tax_rate"></span>%)</span>
                                    <span class="font-bold text-slate-850" x-text="currencySymbol + formatNumber( ((itemSubtotal - totalDiscountAmount) * (1 - discount_rate/100) + totalTaxAmount) * (tax_rate/100) )"></span>
                                </div>
                                <div x-show="shipping_cost > 0" class="flex justify-between">
                                    <span class="text-slate-400">Shipping & Handling</span>
                                    <span class="font-bold text-slate-805" x-text="currencySymbol + formatNumber(shipping_cost)"></span>
                                </div>
                                <div class="flex justify-between text-sm font-bold border-y-4 border-double border-slate-900 py-2.5 text-slate-900 mt-2 font-serif">
                                    <span>Total Due</span>
                                    <span class="text-base" x-text="currencySymbol + formatNumber(total)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-slate-200/80 pt-4 mt-auto font-sans">
                            <div class="grid grid-cols-2 gap-4 text-[9px] text-slate-400 uppercase tracking-wider">
                                <div x-show="notes">
                                    <strong class="text-slate-500 font-bold block mb-1">Invoice Notes</strong>
                                    <p x-text="notes" class="whitespace-pre-line normal-case leading-normal"></p>
                                </div>
                                <div x-show="terms">
                                    <strong class="text-slate-500 font-bold block mb-1">Contract terms</strong>
                                    <p x-text="terms" class="whitespace-pre-line normal-case leading-normal"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================= -->
                    <!-- TEMPLATE 2: CLASSIC CORPORATE                 -->
                    <!-- ============================================= -->
                    <div x-show="template_id === 'classic'" class="flex-grow flex flex-col gap-6 border-2 border-double border-slate-350 p-3 rounded-lg">
                        <!-- Top Header Bar -->
                        <div class="flex justify-between items-start gap-4 border-b-2 border-slate-808 pb-4">
                            <div>
                                <!-- Logo preview inside classic template -->
                                <template x-if="sender_logo">
                                    <div class="group/logo relative inline-block select-none hover:outline hover:outline-dashed hover:outline-indigo-500/50"
                                         :style="'width: ' + logo_width + 'px; height: ' + logo_height + 'px; transform: translate(' + logo_x + 'px, ' + logo_y + 'px); cursor: move; margin-bottom: 0.75rem;'"
                                         @mousedown="startDrag($event)"
                                         @touchstart="startDrag($event)">
                                        <img :src="sender_logo" class="w-full h-full object-contain pointer-events-none" />
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-indigo-600 rounded-full cursor-se-resize flex items-center justify-center shadow no-print opacity-0 group-hover/logo:opacity-100 transition-opacity"
                                             @mousedown.stop="startResize($event)"
                                             @touchstart.stop="startResize($event)">
                                        </div>
                                    </div>
                                </template>
                                <h1 class="text-3xl font-bold tracking-tight text-slate-800 block">INVOICE STATEMENT</h1>
                                <span class="text-xs block text-slate-500 mt-1">Invoice Reference: <strong x-text="invoice_number" class="text-slate-800"></strong></span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-slate-800 block uppercase" :style="'color: ' + theme_color" x-text="sender_name || 'Business Organization'"></span>
                                <span class="text-xs text-slate-505 block" x-text="sender_email"></span>
                                <span class="text-xs text-slate-404 block" x-text="sender_phone"></span>
                            </div>
                        </div>

                        <!-- Details Columns -->
                        <div class="grid grid-cols-2 gap-6 my-2 text-xs">
                            <div>
                                <h3 class="font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-1 mb-2">Sender Address Details</h3>
                                <p x-text="sender_address" class="whitespace-pre-line text-slate-600 leading-relaxed"></p>
                                <div x-show="sender_tax_id" class="mt-2 text-slate-400 font-medium">VAT / Tax Reference: <span x-text="sender_tax_id" class="text-slate-707"></span></div>
                                <div x-show="sender_website" class="mt-0.5 text-slate-400 font-medium font-mono">Web URL: <span x-text="sender_website" class="text-slate-707"></span></div>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-1 mb-2">Client / Bill Recipient</h3>
                                <strong x-text="client_name" class="text-slate-808 block"></strong>
                                <span x-show="client_company" x-text="client_company" class="block font-medium text-slate-600"></span>
                                <p x-text="client_address" class="whitespace-pre-line text-slate-600 mt-1 leading-relaxed"></p>
                                <div x-show="client_vat_number" class="mt-2 text-slate-404">VAT Registration: <span x-text="client_vat_number" class="text-slate-707"></span></div>
                            </div>
                        </div>

                        <!-- Date Grid Info -->
                        <div class="grid grid-cols-3 gap-4 border-y border-slate-200 py-3 bg-slate-50/50">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase block">Invoice Date</span>
                                <span class="text-xs font-semibold text-slate-700 mt-0.5 block" x-text="invoice_date"></span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase block">Payment Term</span>
                                <span class="text-xs font-semibold text-slate-700 mt-0.5 block" x-text="due_date || 'Due Immediately'"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-bold text-slate-400 uppercase block">Outstanding Amount</span>
                                <span class="text-base font-bold text-slate-900 mt-0.5 block" x-text="currencySymbol + formatNumber(total)"></span>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="flex-grow">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-white text-[9px] font-semibold uppercase tracking-wider border-b border-slate-900" :style="'background-color: ' + theme_color">
                                        <th class="py-2 px-3">Description / Specifications</th>
                                        <th class="py-2 px-3 text-center w-[12%]">Qty</th>
                                        <th class="py-2 px-3 text-right w-[15%]">Rate</th>
                                        <th class="py-2 px-3 text-right w-[18%]">Tax Rate</th>
                                        <th class="py-2 px-3 text-right w-[18%]">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-xs text-slate-700">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="odd:bg-white even:bg-slate-50/50">
                                            <td class="py-2.5 px-3 font-semibold text-slate-800" x-text="item.description || 'Service description details'"></td>
                                            <td class="py-2.5 px-3 text-center" x-text="item.quantity"></td>
                                            <td class="py-2.5 px-3 text-right" x-text="currencySymbol + formatNumber(item.rate)"></td>
                                            <td class="py-2.5 px-3 text-right text-slate-400" x-text="item.tax_rate + '%'"></td>
                                            <td class="py-2.5 px-3 text-right font-semibold text-slate-800" x-text="currencySymbol + formatNumber(item.quantity * item.rate)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Calculations block & Signature -->
                        <div class="flex justify-between items-end mt-4">
                            <!-- Left: Signature Preview -->
                            <div>
                                <template x-if="sender_signature">
                                    <div class="flex flex-col items-start">
                                        <div class="h-10 w-28 flex items-center justify-center overflow-hidden border-b border-slate-200 pb-1">
                                            <img :src="sender_signature" class="h-full w-full object-contain" />
                                        </div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Authorized Signature</span>
                                    </div>
                                </template>
                            </div>
                            <!-- Right: Calculations -->
                            <div class="w-60 text-xs flex flex-col gap-1.5 border-t border-slate-300 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Gross Subtotal</span>
                                    <span class="font-semibold text-slate-800" x-text="currencySymbol + formatNumber(itemSubtotal)"></span>
                                </div>
                                <div x-show="totalDiscountAmount > 0" class="flex justify-between text-rose-600">
                                    <span>Accumulated Discounts</span>
                                    <span>- <span x-text="currencySymbol + formatNumber(totalDiscountAmount)"></span></span>
                                </div>
                                <div x-show="totalTaxAmount > 0" class="flex justify-between">
                                    <span class="text-slate-500">VAT / Tax Total</span>
                                    <span class="font-semibold text-slate-800" x-text="currencySymbol + formatNumber(totalTaxAmount)"></span>
                                </div>
                                <div x-show="shipping_cost > 0" class="flex justify-between">
                                    <span class="text-slate-500">Delivery fees</span>
                                    <span class="font-semibold text-slate-800" x-text="currencySymbol + formatNumber(shipping_cost)"></span>
                                </div>
                                <div class="flex justify-between text-xs font-bold border-y-2 border-slate-808 py-2 text-slate-900 mt-2 uppercase tracking-wide">
                                    <span>Grand Total Due</span>
                                    <span class="text-sm font-black" x-text="currencySymbol + formatNumber(total)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div x-show="notes || terms" class="border-t border-slate-200 pt-4 mt-auto">
                            <div class="grid grid-cols-2 gap-4 text-[9px] text-slate-405">
                                <div x-show="notes">
                                    <strong class="text-slate-500 font-bold block mb-1">Contract / Statement Notes</strong>
                                    <p x-text="notes" class="whitespace-pre-line leading-relaxed"></p>
                                </div>
                                <div x-show="terms">
                                    <strong class="text-slate-500 font-bold block mb-1">Remittance Instructions</strong>
                                    <p x-text="terms" class="whitespace-pre-line leading-relaxed"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================= -->
                    <!-- TEMPLATE 3: CREATIVE TECH GRID                -->
                    <!-- ============================================= -->
                    <div x-show="template_id === 'creative'" class="flex-grow flex flex-col gap-6 text-slate-800 animate-fadeIn">
                        <!-- Top header block -->
                        <div class="grid grid-cols-2 gap-4 pb-4">
                            <div>
                                <!-- Logo preview inside creative template -->
                                <template x-if="sender_logo">
                                    <div class="group/logo relative inline-block select-none hover:outline hover:outline-dashed hover:outline-indigo-500/50"
                                         :style="'width: ' + logo_width + 'px; height: ' + logo_height + 'px; transform: translate(' + logo_x + 'px, ' + logo_y + 'px); cursor: move; margin-bottom: 0.75rem;'"
                                         @mousedown="startDrag($event)"
                                         @touchstart="startDrag($event)">
                                        <img :src="sender_logo" class="w-full h-full object-contain pointer-events-none" />
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-indigo-600 rounded-full cursor-se-resize flex items-center justify-center shadow no-print opacity-0 group-hover/logo:opacity-100 transition-opacity"
                                             @mousedown.stop="startResize($event)"
                                             @touchstart.stop="startResize($event)">
                                        </div>
                                    </div>
                                </template>
                                <span class="text-xs font-black block tracking-widest uppercase" :style="'color: ' + theme_color">RECEIPT ENGINE</span>
                                <h1 class="text-3xl font-black tracking-tighter mt-1">#<span x-text="invoice_number"></span></h1>
                            </div>
                            <div class="text-right text-xs">
                                <span class="font-bold text-sm block" x-text="sender_name || 'Dev Studio'"></span>
                                <span class="text-slate-400 block" x-text="sender_email"></span>
                                <span class="text-slate-400 block" x-text="sender_phone"></span>
                                <span class="text-[10px] text-slate-404 block font-mono mt-1" x-text="sender_website"></span>
                            </div>
                        </div>

                        <!-- Visual Total Panel -->
                        <div class="text-white rounded-xl p-4 flex justify-between items-center shadow-inner bg-slate-900" :style="'background-color: ' + theme_color">
                            <div class="text-left">
                                <span class="text-[9px] font-bold text-white/80 uppercase tracking-widest block">GRAND TOTAL</span>
                                <h2 class="text-2xl font-black mt-0.5" x-text="currencySymbol + formatNumber(total)"></h2>
                            </div>
                            <div class="text-right text-[10px] text-white/80 leading-normal font-mono">
                                <div><strong class="text-white">ISSUED:</strong> <span x-text="invoice_date"></span></div>
                                <div class="mt-0.5"><strong class="text-white">DUE DATE:</strong> <span x-text="due_date || 'ON RECEIPT'"></span></div>
                            </div>
                        </div>

                        <!-- From/To Grid -->
                        <div class="grid grid-cols-2 gap-6 my-2 text-xs">
                            <div class="bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">CLIENT BILLING TARGET</span>
                                <strong x-text="client_name" class="text-slate-800 block text-xs"></strong>
                                <span x-show="client_company" x-text="client_company" class="block font-medium text-slate-550"></span>
                                <p x-text="client_address" class="whitespace-pre-line text-slate-400 mt-1 max-w-[200px] leading-relaxed"></p>
                            </div>
                            <div class="flex flex-col justify-between items-end text-right text-[10px]">
                                <div class="text-slate-404 leading-relaxed font-mono">
                                    <div x-show="sender_tax_id">VAT REGISTER: <span x-text="sender_tax_id" class="text-slate-855 font-bold"></span></div>
                                    <div x-show="client_vat_number" class="mt-1">CLIENT VAT: <span x-text="client_vat_number" class="text-slate-855 font-bold"></span></div>
                                    <div class="mt-3 text-slate-350">ADDRESS:</div>
                                    <p x-text="sender_address" class="text-slate-500 whitespace-pre-line mt-0.5 max-w-[180px] break-words"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="flex-grow">
                            <table class="w-full text-left font-mono border-collapse">
                                <thead>
                                    <tr class="border-b-2 border-slate-900 text-[9px] font-bold text-slate-405 uppercase tracking-wider">
                                        <th class="py-2.5">ITEM SUMMARY</th>
                                        <th class="py-2.5 text-center w-[12%]">QTY</th>
                                        <th class="py-2.5 text-right w-[20%]">RATE</th>
                                        <th class="py-2.5 text-right w-[22%]">LINE SUM</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="py-3 font-sans font-bold text-slate-800" x-text="item.description || 'New service task'"></td>
                                            <td class="py-3 text-center" x-text="item.quantity"></td>
                                            <td class="py-3 text-right" x-text="currencySymbol + formatNumber(item.rate)"></td>
                                            <td class="py-3 text-right font-bold text-slate-900" x-text="currencySymbol + formatNumber(item.quantity * item.rate)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals Block & Signature -->
                        <div class="flex justify-between items-end border-t border-slate-150 pt-4 animate-fadeIn">
                            <!-- Left: Signature Preview -->
                            <div>
                                <template x-if="sender_signature">
                                    <div class="flex flex-col items-start">
                                        <div class="h-10 w-28 flex items-center justify-center overflow-hidden border-b border-slate-200 pb-1">
                                            <img :src="sender_signature" class="h-full w-full object-contain" />
                                        </div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Authorized Signature</span>
                                    </div>
                                </template>
                            </div>
                            <!-- Right: Totals -->
                            <div class="w-56 text-[11px] flex flex-col gap-1.5 font-mono">
                                <div class="flex justify-between">
                                    <span class="text-slate-455">SUBTOTAL</span>
                                    <span class="font-bold text-slate-800" x-text="currencySymbol + formatNumber(itemSubtotal)"></span>
                                </div>
                                <div x-show="totalDiscountAmount > 0" class="flex justify-between text-rose-600">
                                    <span>DISCOUNTS</span>
                                    <span>- <span x-text="currencySymbol + formatNumber(totalDiscountAmount)"></span></span>
                                </div>
                                <div x-show="totalTaxAmount > 0" class="flex justify-between">
                                    <span class="text-slate-455">VAT / TAX</span>
                                    <span class="font-bold text-slate-800" x-text="currencySymbol + formatNumber(totalTaxAmount)"></span>
                                </div>
                                <div x-show="shipping_cost > 0" class="flex justify-between">
                                    <span class="text-slate-455">SHIPPING</span>
                                    <span class="font-bold text-slate-800" x-text="currencySymbol + formatNumber(shipping_cost)"></span>
                                </div>
                                <div class="flex justify-between text-xs font-bold border-t border-slate-900 pt-2 text-slate-900 mt-1.5 font-sans">
                                    <span>TOTAL DUE</span>
                                    <span class="text-sm font-black" x-text="currencySymbol + formatNumber(total)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div x-show="notes || terms" class="bg-slate-50 rounded-xl p-4 mt-auto">
                            <div class="grid grid-cols-2 gap-4 text-[9px] text-slate-400">
                                <div x-show="notes">
                                    <strong class="text-slate-655 font-bold block mb-1">DEVELOPER NOTES</strong>
                                    <p x-text="notes" class="leading-relaxed whitespace-pre-line"></p>
                                </div>
                                <div x-show="terms">
                                    <strong class="text-slate-655 font-bold block mb-1">TERMS & DETAILS</strong>
                                    <p x-text="terms" class="leading-relaxed whitespace-pre-line"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================= -->
                    <!-- TEMPLATE 4: ELEGANT SLATE                     -->
                    <!-- ============================================= -->
                    <div x-show="template_id === 'slate'" class="flex-grow flex flex-col gap-6 font-serif text-slate-900 italic">
                        <!-- Top Header block -->
                        <div class="text-center pb-4 border-b border-slate-300">
                            <!-- Logo preview inside slate template -->
                            <template x-if="sender_logo">
                                <div class="group/logo relative select-none hover:outline hover:outline-dashed hover:outline-indigo-500/50"
                                     :style="'width: ' + logo_width + 'px; height: ' + logo_height + 'px; transform: translate(' + logo_x + 'px, ' + logo_y + 'px); cursor: move; margin-left: auto; margin-right: auto; display: block; margin-bottom: 0.75rem;'"
                                     @mousedown="startDrag($event)"
                                     @touchstart="startDrag($event)">
                                    <img :src="sender_logo" class="w-full h-full object-contain pointer-events-none" />
                                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-indigo-600 rounded-full cursor-se-resize flex items-center justify-center shadow no-print opacity-0 group-hover/logo:opacity-100 transition-opacity"
                                         @mousedown.stop="startResize($event)"
                                         @touchstart.stop="startResize($event)">
                                    </div>
                                </div>
                            </template>
                            <h1 class="text-3xl font-bold uppercase tracking-widest text-slate-800 not-italic">INVOICE STATEMENT</h1>
                            <span class="text-xs text-slate-450 mt-1 block">Reference identifier: <span x-text="invoice_number" class="font-bold text-slate-900"></span></span>
                        </div>

                        <!-- Grid billing addresses -->
                        <div class="grid grid-cols-2 gap-8 my-1 text-xs">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block not-italic mb-1">Originator</span>
                                <strong x-text="sender_name || 'Business Studio'" class="text-slate-900 block text-sm"></strong>
                                <span x-text="sender_email" class="block text-slate-500 font-sans text-[11px] mt-0.5"></span>
                                <span x-text="sender_phone" class="block text-slate-500 font-sans text-[11px]"></span>
                                <p x-text="sender_address" class="text-slate-555 whitespace-pre-line mt-1.5 leading-relaxed max-w-[200px]"></p>
                                <div x-show="sender_tax_id" class="mt-2 text-[10px] text-slate-400 font-bold not-italic">TAX ID: <span x-text="sender_tax_id" class="text-slate-707 font-semibold"></span></div>
                                <div x-show="sender_website" class="mt-0.5 text-[10px] text-slate-400 font-bold not-italic">WEB: <span x-text="sender_website" class="text-slate-707 font-semibold"></span></div>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block not-italic mb-1">Addressed to</span>
                                <strong x-text="client_name" class="text-slate-900 block text-sm"></strong>
                                <span x-show="client_company" x-text="client_company" class="block text-slate-655"></span>
                                <p x-text="client_address" class="text-slate-555 whitespace-pre-line mt-1.5 leading-relaxed text-right inline-block max-w-[200px]"></p>
                                <div x-show="client_vat_number" class="mt-2 text-[10px] text-slate-400 font-bold not-italic">CLIENT VAT: <span x-text="client_vat_number" class="text-slate-707 font-semibold"></span></div>
                            </div>
                        </div>

                        <!-- Date parameters table row -->
                        <div class="flex justify-between border-y border-slate-205 py-3 text-xs italic">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase block not-italic tracking-wider">Date generated</span>
                                <span x-text="invoice_date" class="mt-0.5 block font-semibold text-slate-800"></span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase block not-italic tracking-wider">Maturity limit</span>
                                <span x-text="due_date || 'Due immediately'" class="mt-0.5 block font-semibold text-slate-800"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-bold text-slate-400 uppercase block not-italic tracking-wider">Net Amount Due</span>
                                <span x-text="currencySymbol + formatNumber(total)" class="mt-0.5 block font-bold text-slate-900 not-italic text-sm"></span>
                            </div>
                        </div>

                        <!-- Items table -->
                        <div class="flex-grow font-sans not-italic text-xs">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-slate-900 text-[10px] font-bold text-slate-404 uppercase tracking-wider">
                                        <th class="py-2.5">Itemized Description</th>
                                        <th class="py-2.5 text-center w-[12%]">Qty</th>
                                        <th class="py-2.5 text-right w-[18%]">Rate</th>
                                        <th class="py-2.5 text-right w-[18%] font-serif italic text-slate-800">Tax</th>
                                        <th class="py-2.5 text-right w-[20%]">Line sum</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-105 text-slate-705">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="py-3 font-semibold text-slate-855" x-text="item.description || 'Description details'"></td>
                                            <td class="py-3 text-center" x-text="item.quantity"></td>
                                            <td class="py-3 text-right" x-text="currencySymbol + formatNumber(item.rate)"></td>
                                            <td class="py-3 text-right text-slate-400 font-serif italic" x-text="item.tax_rate + '%'"></td>
                                            <td class="py-3 text-right font-semibold text-slate-900 font-serif italic" x-text="currencySymbol + formatNumber(item.quantity * item.rate)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Calculations totals block & Signature -->
                        <div class="flex justify-between items-end border-t border-slate-200 pt-4 font-sans not-italic">
                            <!-- Left: Signature Preview -->
                            <div>
                                <template x-if="sender_signature">
                                    <div class="flex flex-col items-start font-serif italic">
                                        <div class="h-10 w-28 flex items-center justify-center overflow-hidden border-b border-slate-200 pb-1">
                                            <img :src="sender_signature" class="h-full w-full object-contain" />
                                        </div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 block not-italic">Authorized Signature</span>
                                    </div>
                                </template>
                            </div>
                            <!-- Right: Calculations -->
                            <div class="w-56 text-[11px] flex flex-col gap-1.5">
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Statement Subtotal</span>
                                    <span class="font-semibold text-slate-800" x-text="currencySymbol + formatNumber(itemSubtotal)"></span>
                                </div>
                                <div x-show="totalDiscountAmount > 0" class="flex justify-between text-rose-600">
                                    <span>Aggregate Discounts</span>
                                    <span>- <span x-text="currencySymbol + formatNumber(totalDiscountAmount)"></span></span>
                                </div>
                                <div x-show="totalTaxAmount > 0" class="flex justify-between">
                                    <span class="text-slate-400">VAT / Tax sum</span>
                                    <span class="font-semibold text-slate-800" x-text="currencySymbol + formatNumber(totalTaxAmount)"></span>
                                </div>
                                <div x-show="shipping_cost > 0" class="flex justify-between">
                                    <span class="text-slate-400">Delivery fees</span>
                                    <span class="font-semibold text-slate-800" x-text="currencySymbol + formatNumber(shipping_cost)"></span>
                                </div>
                                <div class="flex justify-between text-xs font-bold border-y-2 border-double border-slate-900 py-2 text-slate-900 mt-2 font-serif italic">
                                    <span>Final Payable Balance</span>
                                    <span class="text-sm not-italic font-black" x-text="currencySymbol + formatNumber(total)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div x-show="notes || terms" class="border-t border-slate-200/80 pt-4 mt-auto">
                            <div class="grid grid-cols-2 gap-4 text-[9px] text-slate-455 leading-relaxed uppercase tracking-wider not-italic font-sans">
                                <div x-show="notes">
                                    <strong class="text-slate-650 font-bold block mb-1">Reference Notes</strong>
                                    <p x-text="notes" class="whitespace-pre-line leading-normal lowercase first-letter:uppercase"></p>
                                </div>
                                <div x-show="terms">
                                    <strong class="text-slate-655 font-bold block mb-1">Contract / Bank terms</strong>
                                    <p x-text="terms" class="whitespace-pre-line leading-normal lowercase first-letter:uppercase"></p>
                                </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>

    </div>

    <!-- Template Picker Modal Overlay -->
    <div x-show="templateModalOpen" 
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm no-print"
         x-cloak>
        
        <div @click.away="templateModalOpen = false" 
             class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl w-full max-w-2xl shadow-mui-24 flex flex-col overflow-hidden max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-100 dark:border-zinc-800/80 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-zinc-50 uppercase tracking-wider">Choose Design blueprint</h3>
                    <p class="text-[10px] text-slate-500 dark:text-zinc-400 mt-0.5">Select a pre-designed layout blueprint for your professional billing.</p>
                </div>
                <button type="button" @click="templateModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <!-- Modal Body (Grid of Cards) -->
            <div class="p-5 overflow-y-auto grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Card 1: Open Source Blueprint -->
                <div @click="template_id = 'blueprint'; templateModalOpen = false" 
                     :class="template_id === 'blueprint' ? 'border-indigo-600 ring-1 ring-indigo-600 dark:border-indigo-400 dark:ring-indigo-400 bg-indigo-50/5 dark:bg-indigo-950/5' : 'border-slate-205 dark:border-zinc-800 hover:border-slate-350 dark:hover:border-zinc-700'"
                     class="border-2 rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between group bg-slate-50/10 dark:bg-zinc-950/10">
                    <div>
                        <div class="flex justify-between items-start">
                            <span class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-code"></i>
                            </span>
                            <span x-show="template_id === 'blueprint'" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Active
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-zinc-150 mt-3">Open Source Blueprint</h4>
                        <p class="text-[10px] text-slate-550 dark:text-zinc-400 mt-1.5 leading-relaxed">Clean layout featuring solid header bands, gray grid borders, and monospace numbers. Excellent for technical developers and consulting work.</p>
                    </div>
                    <div class="border-t border-slate-100 dark:border-zinc-800/80 pt-2 mt-4 flex items-center gap-1.5 text-[9px] text-slate-400 font-bold uppercase tracking-wider">
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">Formal</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">Grids</span>
                    </div>
                </div>

                <!-- Card 2: Modern Minimalist -->
                <div @click="template_id = 'modern'; templateModalOpen = false" 
                     :class="template_id === 'modern' ? 'border-indigo-600 ring-1 ring-indigo-600 dark:border-indigo-400 dark:ring-indigo-400 bg-indigo-50/5 dark:bg-indigo-950/5' : 'border-slate-205 dark:border-zinc-800 hover:border-slate-350 dark:hover:border-zinc-700'"
                     class="border-2 rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between group bg-slate-50/10 dark:bg-zinc-950/10">
                    <div>
                        <div class="flex justify-between items-start">
                            <span class="h-8 w-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </span>
                            <span x-show="template_id === 'modern'" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Active
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-zinc-150 mt-3">Modern Minimalist</h4>
                        <p class="text-[10px] text-slate-550 dark:text-zinc-400 mt-1.5 leading-relaxed">Spacious design relying on clean typography margins, colored highlights, and bold sans-serif headers. Perfect for startups and agencies.</p>
                    </div>
                    <div class="border-t border-slate-100 dark:border-zinc-800/80 pt-2 mt-4 flex items-center gap-1.5 text-[9px] text-slate-400 font-bold uppercase tracking-wider">
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">Clean</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">Spacious</span>
                    </div>
                </div>

                <!-- Card 3: Classic Corporate -->
                <div @click="template_id = 'classic'; templateModalOpen = false" 
                     :class="template_id === 'classic' ? 'border-indigo-600 ring-1 ring-indigo-600 dark:border-indigo-400 dark:ring-indigo-400 bg-indigo-50/5 dark:bg-indigo-950/5' : 'border-slate-205 dark:border-zinc-800 hover:border-slate-350 dark:hover:border-zinc-700'"
                     class="border-2 rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between group bg-slate-50/10 dark:bg-zinc-950/10">
                    <div>
                        <div class="flex justify-between items-start">
                            <span class="h-8 w-8 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-briefcase"></i>
                            </span>
                            <span x-show="template_id === 'classic'" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Active
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-zinc-150 mt-3">Classic Corporate</h4>
                        <p class="text-[10px] text-slate-550 dark:text-zinc-400 mt-1.5 leading-relaxed">Traditional layout with a primary colored top banner block and distinct address columns. Ideal for retail and corporations.</p>
                    </div>
                    <div class="border-t border-slate-100 dark:border-zinc-800/80 pt-2 mt-4 flex items-center gap-1.5 text-[9px] text-slate-400 font-bold uppercase tracking-wider">
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">Corporate</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">Banners</span>
                    </div>
                </div>

                <!-- Card 4: Creative Tech Grid -->
                <div @click="template_id = 'creative'; templateModalOpen = false" 
                     :class="template_id === 'creative' ? 'border-indigo-600 ring-1 ring-indigo-600 dark:border-indigo-400 dark:ring-indigo-400 bg-indigo-50/5 dark:bg-indigo-950/5' : 'border-slate-205 dark:border-zinc-800 hover:border-slate-350 dark:hover:border-zinc-700'"
                     class="border-2 rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between group bg-slate-50/10 dark:bg-zinc-950/10">
                    <div>
                        <div class="flex justify-between items-start">
                            <span class="h-8 w-8 rounded-lg bg-pink-50 dark:bg-pink-950/50 text-pink-600 dark:text-pink-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-cubes"></i>
                            </span>
                            <span x-show="template_id === 'creative'" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Active
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-zinc-150 mt-3">Creative Tech Grid</h4>
                        <p class="text-[10px] text-slate-550 dark:text-zinc-400 mt-1.5 leading-relaxed">Modern visual grid featuring a solid total due block, monospace invoice items, and minimal borders. Perfect for tech design.</p>
                    </div>
                    <div class="border-t border-slate-100 dark:border-zinc-800/80 pt-2 mt-4 flex items-center gap-1.5 text-[9px] text-slate-400 font-bold uppercase tracking-wider">
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">Monospace</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">Creative</span>
                    </div>
                </div>

                <!-- Card 5: Elegant Slate -->
                <div @click="template_id = 'slate'; templateModalOpen = false" 
                     :class="template_id === 'slate' ? 'border-indigo-600 ring-1 ring-indigo-600 dark:border-indigo-400 dark:ring-indigo-400 bg-indigo-50/5 dark:bg-indigo-950/5' : 'border-slate-205 dark:border-zinc-800 hover:border-slate-350 dark:hover:border-zinc-700'"
                     class="border-2 rounded-xl p-4 cursor-pointer transition-all flex flex-col justify-between group bg-slate-50/10 dark:bg-zinc-950/10">
                    <div>
                        <div class="flex justify-between items-start">
                            <span class="h-8 w-8 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-feather"></i>
                            </span>
                            <span x-show="template_id === 'slate'" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Active
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-855 dark:text-zinc-100 mt-3">Elegant Slate</h4>
                        <p class="text-[10px] text-slate-550 dark:text-zinc-400 mt-1.5 leading-relaxed">Sophisticated, warm design leveraging italic serif headings, centered reference elements, and double-ruled summaries.</p>
                    </div>
                    <div class="border-t border-slate-100 dark:border-zinc-800/80 pt-2 mt-4 flex items-center gap-1.5 text-[9px] text-slate-400 font-bold uppercase tracking-wider">
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">Serif</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">Italic</span>
                    </div>
                </div>
            </div>

            </div>
            
            <!-- Modal Footer -->
            <div class="px-5 py-3 border-t border-slate-100 dark:border-zinc-800/80 flex justify-end bg-slate-50/50 dark:bg-zinc-900/30">
                <button type="button" @click="templateModalOpen = false" class="px-4 py-1.5 text-xs font-bold text-slate-700 dark:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 border border-slate-205 dark:border-zinc-800 rounded-lg shadow-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Main Editor Data
        Alpine.data('invoiceEditor', () => ({
            // Form states
            invoice_number: '{{ $defaultInvoice['invoice_number'] }}',
            invoice_date: '{{ $defaultInvoice['invoice_date'] }}',
            due_date: '{{ $defaultInvoice['due_date'] }}',
            
            sender_name: '{{ $defaultInvoice['sender_name'] }}',
            sender_email: '{{ $defaultInvoice['sender_email'] }}',
            sender_phone: '{{ $defaultInvoice['sender_phone'] }}',
            sender_address: `{!! addslashes($defaultInvoice['sender_address']) !!}`,
            sender_tax_id: '{{ $defaultInvoice['sender_additional']['tax_id'] ?? '' }}',
            sender_website: '{{ $defaultInvoice['sender_additional']['website'] ?? '' }}',
            sender_logo: '{{ $defaultInvoice['sender_additional']['logo'] ?? '' }}',
            logo_width: {{ $defaultInvoice['sender_additional']['logo_width'] ?? 110 }},
            logo_height: {{ $defaultInvoice['sender_additional']['logo_height'] ?? 40 }},
            logo_x: {{ $defaultInvoice['sender_additional']['logo_x'] ?? 0 }},
            logo_y: {{ $defaultInvoice['sender_additional']['logo_y'] ?? 0 }},
            sender_signature: '{{ $defaultInvoice['sender_additional']['signature'] ?? '' }}',
            
            client_name: '{{ $defaultInvoice['client_name'] }}',
            client_email: '{{ $defaultInvoice['client_email'] }}',
            client_phone: '{{ $defaultInvoice['client_phone'] }}',
            client_address: `{!! addslashes($defaultInvoice['client_address']) !!}`,
            client_company: '{{ $defaultInvoice['client_additional']['company'] ?? '' }}',
            client_vat_number: '{{ $defaultInvoice['client_additional']['vat_number'] ?? '' }}',
            
            items: {!! json_encode($defaultInvoice['items']) !!},
            
            tax_rate: {{ $defaultInvoice['tax_rate'] }},
            discount_rate: {{ $defaultInvoice['discount_rate'] }},
            shipping_cost: {{ $defaultInvoice['shipping_cost'] }},
            currency: '{{ $defaultInvoice['currency'] }}',
            notes: `{!! addslashes($defaultInvoice['notes']) !!}`,
            terms: `{!! addslashes($defaultInvoice['terms']) !!}`,
            
            // Design choices
            template_id: '{{ $defaultInvoice['template_id'] }}',
            theme_color: '{{ $defaultInvoice['theme_color'] }}',
            status: '{{ $defaultInvoice['status'] }}',
            
            // Font setting
            selected_font: 'font-sans',

            // Modal state
            templateModalOpen: false,

            // Signature Pad state
            drawModalOpen: false,
            isDrawing: false,
            ctx: null,

            openDrawModal() {
                this.drawModalOpen = true;
                this.$nextTick(() => {
                    const canvas = this.$refs.canvas;
                    this.ctx = canvas.getContext('2d');
                    this.ctx.strokeStyle = '#000000';
                    this.ctx.lineWidth = 2.5;
                    this.ctx.lineCap = 'round';
                    
                    if (canvas.dataset.initialized) return;
                    canvas.dataset.initialized = 'true';
                    
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
                this.sender_signature = '';
            },

            saveCanvas() {
                const canvas = this.$refs.canvas;
                const dataUrl = canvas.toDataURL();
                this.sender_signature = dataUrl;
                this.closeDrawModal();
            },

            startDrag(e) {
                e.preventDefault();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                
                const startX = clientX - this.logo_x;
                const startY = clientY - this.logo_y;
                
                const onMouseMove = (moveEvent) => {
                    const currentX = moveEvent.touches ? moveEvent.touches[0].clientX : moveEvent.clientX;
                    const currentY = moveEvent.touches ? moveEvent.touches[0].clientY : moveEvent.clientY;
                    this.logo_x = currentX - startX;
                    this.logo_y = currentY - startY;
                };
                
                const onMouseUp = () => {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                    document.removeEventListener('touchmove', onMouseMove);
                    document.removeEventListener('touchend', onMouseUp);
                };
                
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
                document.addEventListener('touchmove', onMouseMove, { passive: true });
                document.addEventListener('touchend', onMouseUp);
            },
            
            startResize(e) {
                e.preventDefault();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                
                const startWidth = this.logo_width;
                const startHeight = this.logo_height;
                
                const startX = clientX;
                const startY = clientY;
                
                const onMouseMove = (moveEvent) => {
                    const currentX = moveEvent.touches ? moveEvent.touches[0].clientX : moveEvent.clientX;
                    const currentY = moveEvent.touches ? moveEvent.touches[0].clientY : moveEvent.clientY;
                    
                    const deltaX = currentX - startX;
                    const deltaY = currentY - startY;
                    
                    this.logo_width = Math.max(30, Math.min(300, startWidth + deltaX));
                    this.logo_height = Math.max(20, Math.min(200, startHeight + deltaY));
                };
                
                const onMouseUp = () => {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                    document.removeEventListener('touchmove', onMouseMove);
                    document.removeEventListener('touchend', onMouseUp);
                };
                
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
                document.addEventListener('touchmove', onMouseMove, { passive: true });
                document.addEventListener('touchend', onMouseUp);
            },

            // Array helper methods
            addItem() {
                this.items.push({
                    description: '',
                    quantity: 1,
                    rate: 0,
                    tax_rate: 0,
                    discount_rate: 0
                });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            
            // Helper method for template label
            getTemplateLabel(id) {
                const labels = {
                    'blueprint': 'Open Source Blueprint',
                    'modern': 'Modern Minimalist',
                    'classic': 'Classic Corporate',
                    'creative': 'Creative Tech Grid',
                    'slate': 'Elegant Slate (Serif)'
                };
                return labels[id] || 'Select Template';
            },

            // Computations
            get itemSubtotal() {
                return this.items.reduce((sum, item) => {
                    let qty = parseFloat(item.quantity) || 0;
                    let rate = parseFloat(item.rate) || 0;
                    return sum + (qty * rate);
                }, 0);
            },
            
            get totalTaxAmount() {
                let totalTax = 0;
                this.items.forEach(item => {
                    let qty = parseFloat(item.quantity) || 0;
                    let rate = parseFloat(item.rate) || 0;
                    let itemSub = qty * rate;
                    let discount = itemSub * ((parseFloat(item.discount_rate) || 0) / 100);
                    let taxable = itemSub - discount;
                    totalTax += taxable * ((parseFloat(item.tax_rate) || 0) / 100);
                });
                return totalTax;
            },
            
            get totalDiscountAmount() {
                return this.items.reduce((sum, item) => {
                    let qty = parseFloat(item.quantity) || 0;
                    let rate = parseFloat(item.rate) || 0;
                    let itemSub = qty * rate;
                    return sum + (itemSub * ((parseFloat(item.discount_rate) || 0) / 100));
                }, 0);
            },

            get subtotal() {
                return this.itemSubtotal;
            },
            
            get total() {
                let totalAmount = 0;
                this.items.forEach(item => {
                    let qty = parseFloat(item.quantity) || 0;
                    let rate = parseFloat(item.rate) || 0;
                    let itemSub = qty * rate;
                    let discount = itemSub * ((parseFloat(item.discount_rate) || 0) / 100);
                    let taxable = itemSub - discount;
                    let tax = taxable * ((parseFloat(item.tax_rate) || 0) / 100);
                    totalAmount += (taxable + tax);
                });

                // Apply global discount
                let globalDiscountAmount = totalAmount * ((parseFloat(this.discount_rate) || 0) / 100);
                let globalTaxableAmount = totalAmount - globalDiscountAmount;
                
                // Apply global tax
                let globalTaxAmount = globalTaxableAmount * ((parseFloat(this.tax_rate) || 0) / 100);
                
                // Final sum
                return Math.max(0, globalTaxableAmount + globalTaxAmount + (parseFloat(this.shipping_cost) || 0));
            },
            
            get currencySymbol() {
                const symbols = {
                    'USD': '$',
                    'EUR': '€',
                    'GBP': '£',
                    'INR': '₹'
                };
                return symbols[this.currency] || '$';
            },
            
            formatNumber(value) {
                return parseFloat(value).toFixed(2);
            }
        }));
    });
</script>
@endpush
