@extends('invoices.layout')

@section('content')
@php
    $currencySymbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'INR' => '₹',
    ];
    $symbol = $currencySymbols[$invoice->currency] ?? '$';
    
    // Resolve font classes
    $fontClass = 'font-sans';
    if ($invoice->template_id === 'slate') {
        $fontClass = 'font-serif';
    } elseif ($invoice->template_id === 'creative') {
        $fontClass = 'font-mono';
    }
@endphp

<div class="min-h-[calc(100vh-4rem)] bg-slate-50/50 dark:bg-zinc-950 py-6 px-4 sm:px-6 lg:px-8 flex flex-col items-center">
    
    <!-- Action Controls (no-print - Compact) -->
    <div class="no-print w-full max-w-4xl bg-white dark:bg-zinc-900 border border-slate-200/60 dark:border-zinc-800/80 rounded-xl p-3 shadow-sm mb-5 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-zinc-400 dark:hover:text-zinc-200 transition-all">
                <i class="fa-solid fa-arrow-left"></i>
                Dashboard
            </a>
            <span class="text-slate-200 dark:text-zinc-800">|</span>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Status:</span>
                @if($invoice->status === 'paid')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200/20 dark:border-emerald-900/20">
                        Paid
                    </span>
                @elseif($invoice->status === 'sent')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-200/20 dark:border-blue-900/20">
                        Sent
                    </span>
                @elseif($invoice->status === 'overdue')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200/20 dark:border-rose-900/20">
                        Overdue
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-zinc-800 dark:text-zinc-400 border border-slate-200/60 dark:border-zinc-700/30">
                        Draft
                    </span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <!-- Share Invoice Dropdown -->
            <div x-data="{ open: false, copied: false }" class="relative inline-block text-left w-full sm:w-auto">
                <button type="button" @click="open = !open" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-zinc-200 hover:bg-slate-50 dark:hover:bg-zinc-800 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-sm transition-all cursor-pointer">
                    <i class="fa-solid fa-share-nodes text-[10px]"></i>
                    <span>Share</span>
                    <i class="fa-solid fa-angle-down text-[9px]"></i>
                </button>
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 rounded-lg bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 shadow-mui-8 z-50 py-1"
                     x-cloak>
                    
                    <a href="https://api.whatsapp.com/send?text=Hi%2C%20here%20is%20invoice%20{{ urlencode($invoice->invoice_number) }}%3A%20{{ urlencode(route('invoices.show', $invoice)) }}" 
                       target="_blank" 
                       class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">
                        <i class="fa-brands fa-whatsapp text-emerald-500 text-sm w-4 text-center"></i>
                        <span>WhatsApp</span>
                    </a>

                    <a href="https://mail.google.com/mail/?view=cm&fs=1&su=Invoice%20{{ urlencode($invoice->invoice_number) }}&body=Hi%2C%20please%20find%20your%20invoice%20here%3A%20{{ urlencode(route('invoices.show', $invoice)) }}" 
                       target="_blank" 
                       class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">
                        <i class="fa-solid fa-envelope text-red-500 text-sm w-4 text-center"></i>
                        <span>Gmail</span>
                    </a>

                    <a href="mailto:?subject=Invoice%20{{ urlencode($invoice->invoice_number) }}&body=Hi%2C%20here%20is%20the%20invoice%20link%3A%20{{ urlencode(route('invoices.show', $invoice)) }}" 
                       class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">
                        <i class="fa-solid fa-envelope-open-text text-slate-550 text-sm w-4 text-center"></i>
                        <span>Default Email</span>
                    </a>

                    <hr class="border-slate-100 dark:border-zinc-800/80 my-1" />

                    <button type="button" 
                            @click="
                                navigator.clipboard.writeText('{{ route('invoices.show', $invoice) }}');
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            " 
                            class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors text-left cursor-pointer">
                        <i class="fa-regular fa-copy text-indigo-500 text-sm w-4 text-center" x-show="!copied"></i>
                        <i class="fa-solid fa-check text-emerald-500 text-sm w-4 text-center" x-show="copied" x-cloak></i>
                        <span x-text="copied ? 'Link Copied!' : 'Copy Link'"></span>
                    </button>

                    <button type="button" 
                            x-show="navigator.share"
                            @click="
                                navigator.share({
                                    title: 'Invoice {{ $invoice->invoice_number }}',
                                    text: 'Here is the invoice link:',
                                    url: '{{ route('invoices.show', $invoice) }}'
                                }).catch(err => console.log(err));
                            " 
                            class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors text-left cursor-pointer">
                        <i class="fa-solid fa-share-nodes text-blue-500 text-sm w-4 text-center"></i>
                        <span>Native Share</span>
                    </button>
                </div>
            </div>

            <!-- Edit Button -->
            <a href="{{ route('invoices.edit', $invoice) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-zinc-200 hover:bg-slate-50 dark:hover:bg-zinc-800 rounded-lg border border-slate-200 dark:border-zinc-800 shadow-sm transition-all">
                <i class="fa-solid fa-pen text-[10px]"></i>
                Edit Invoice
            </a>

            <!-- Download/Print Button -->
            @auth
                <button type="button" onclick="window.print()" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-1.5 text-xs font-bold bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white rounded-lg shadow-sm cursor-pointer transition-all">
                    <i class="fa-solid fa-print"></i>
                    Print / Save PDF
                </button>
            @else
                <!-- Show registration trigger modal/popup for guests -->
                <div x-data="{ showWarning: false }" class="relative inline-block text-left w-full sm:w-auto">
                    <button type="button" @click="showWarning = !showWarning" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-1.5 text-xs font-bold bg-slate-400 hover:bg-slate-500 text-white rounded-lg shadow-sm cursor-pointer transition-all">
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
        </div>
    </div>

    <!-- Printable Invoice Page (Centered) -->
    <div class="print-card bg-white text-zinc-800 p-6 sm:p-8 shadow-lg rounded-xl aspect-[1/1.41] w-full max-w-4xl border border-slate-200/60 transition-all {{ $fontClass }}">
        
        <!-- ============================================= -->
        <!-- TEMPLATE 5: OPEN SOURCE BLUEPRINT             -->
        <!-- ============================================= -->
        @if($invoice->template_id === 'blueprint')
            <div class="h-full flex flex-col gap-6 text-xs text-slate-800 leading-normal">
                <!-- Top Header Banner -->
                <div class="border-t-4 pt-4 flex justify-between items-start gap-4" style="border-color: {{ $invoice->theme_color }}">
                    <div>
                        <!-- Logo -->
                        @if(isset($invoice->sender_additional['logo']) && $invoice->sender_additional['logo'])
                            <div style="display: inline-block;
                                        width: {{ $invoice->sender_additional['logo_width'] ?? 110 }}px;
                                        height: {{ $invoice->sender_additional['logo_height'] ?? 40 }}px;
                                        transform: translate({{ $invoice->sender_additional['logo_x'] ?? 0 }}px, {{ $invoice->sender_additional['logo_y'] ?? 0 }}px);
                                        margin-bottom: 0.75rem;">
                                <img src="{{ $invoice->sender_additional['logo'] }}" class="w-full h-full object-contain" />
                            </div>
                        @endif
                        <h1 class="text-2xl font-black uppercase tracking-tight" style="color: {{ $invoice->theme_color }}">INVOICE</h1>
                        <span class="text-xs text-slate-505 block mt-1">Ref: <span class="font-bold text-slate-800">{{ $invoice->invoice_number }}</span></span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-sm block">{{ $invoice->sender_name }}</span>
                        <span class="text-[11px] text-slate-505 block mt-0.5">{{ $invoice->sender_email }}</span>
                        <span class="text-[11px] text-slate-505 block">{{ $invoice->sender_phone }}</span>
                        <span class="text-[11px] text-slate-400 block whitespace-pre-line mt-1">{{ $invoice->sender_address }}</span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-3 gap-4 border-y border-slate-205 py-3 bg-slate-50/50 p-3 rounded-lg">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Issued On</span>
                        <span class="text-xs font-semibold mt-0.5 block">{{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Payment Due</span>
                        <span class="text-xs font-semibold mt-0.5 block">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Due on Receipt' }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Amount Due</span>
                        <span class="text-sm font-bold mt-0.5 block" style="color: {{ $invoice->theme_color }}">{{ $symbol }}{{ number_format($invoice->total, 2) }}</span>
                    </div>
                </div>

                <!-- From / To details -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Billing To:</span>
                        <div class="text-[11px] text-slate-700 bg-slate-50/40 p-2.5 rounded-lg border border-slate-200/50">
                            <strong class="text-slate-850 block">{{ $invoice->client_name }}</strong>
                            @if(isset($invoice->client_additional['company']))
                                <span class="block font-medium">{{ $invoice->client_additional['company'] }}</span>
                            @endif
                            @if($invoice->client_email)
                                <span class="block">{{ $invoice->client_email }}</span>
                            @endif
                            @if($invoice->client_phone)
                                <span class="block">{{ $invoice->client_phone }}</span>
                            @endif
                            <span class="block whitespace-pre-line mt-1">{{ $invoice->client_address }}</span>
                        </div>
                    </div>
                    <div class="text-right flex flex-col justify-between items-end">
                        <div class="text-[10px] text-slate-500 leading-relaxed font-sans not-italic">
                            @if(isset($invoice->sender_additional['tax_id']))
                                <div><span class="font-bold text-slate-400">TAX / VAT ID:</span> <span class="text-slate-707 font-semibold">{{ $invoice->sender_additional['tax_id'] }}</span></div>
                            @endif
                            @if(isset($invoice->sender_additional['website']))
                                <div class="mt-0.5"><span class="font-bold text-slate-400">Website:</span> <span class="text-slate-707 font-semibold">{{ $invoice->sender_additional['website'] }}</span></div>
                            @endif
                            @if(isset($invoice->client_additional['vat_number']))
                                <div class="mt-2"><span class="font-bold text-slate-400">Client VAT:</span> <span class="text-slate-707 font-semibold">{{ $invoice->client_additional['vat_number'] }}</span></div>
                            @endif
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
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold text-slate-805">{{ $item['description'] }}</td>
                                    <td class="py-2.5 px-3 text-center text-slate-600">{{ $item['quantity'] }}</td>
                                    <td class="py-2.5 px-3 text-right text-slate-600">{{ $symbol }}{{ number_format($item['rate'], 2) }}</td>
                                    <td class="py-2.5 px-3 text-right text-slate-505">{{ $item['tax_rate'] ?? 0 }}%</td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-900">{{ $symbol }}{{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Block & Signature -->
                <div class="flex justify-between items-end mt-4 font-sans not-italic animate-fadeIn">
                    <!-- Left: Signature Preview -->
                    <div>
                        @if(isset($invoice->sender_additional['signature']) && $invoice->sender_additional['signature'])
                            <div class="flex flex-col items-start">
                                <div class="h-10 w-28 flex items-center justify-center overflow-hidden border-b border-slate-200 pb-1">
                                    <img src="{{ $invoice->sender_additional['signature'] }}" class="h-full w-full object-contain" />
                                </div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Authorized Signature</span>
                            </div>
                        @endif
                    </div>
                    <!-- Right: Totals -->
                    <div class="w-56 text-[11px] flex flex-col gap-1.5 border-t border-slate-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-slate-505">Subtotal</span>
                            <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @php
                            $totalItemsDiscount = 0;
                            $totalItemsTax = 0;
                            foreach($invoice->items as $it) {
                                $itemSub = $it['quantity'] * $it['rate'];
                                $discount = $itemSub * (($it['discount_rate'] ?? 0) / 100);
                                $taxable = $itemSub - $discount;
                                $totalItemsDiscount += $discount;
                                $totalItemsTax += $taxable * (($it['tax_rate'] ?? 0) / 100);
                            }
                        @endphp
                        @if($totalItemsDiscount > 0)
                            <div class="flex justify-between text-rose-600">
                                <span>Discounts</span>
                                <span>-{{ $symbol }}{{ number_format($totalItemsDiscount, 2) }}</span>
                            </div>
                        @endif
                        @if($totalItemsTax > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-505">Tax</span>
                                <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($totalItemsTax, 2) }}</span>
                            </div>
                        @endif
                        @if($invoice->shipping_cost > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-505">Shipping</span>
                                <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($invoice->shipping_cost, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xs font-bold border-y-2 border-slate-300 py-1.5 text-slate-900 mt-1.5">
                            <span>Total Due</span>
                            <span>{{ $symbol }}{{ number_format($invoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer notes -->
                @if($invoice->notes || $invoice->terms)
                    <div class="border-t border-slate-200 pt-3 mt-auto">
                        <div class="grid grid-cols-2 gap-4 text-[9px] text-slate-400">
                            @if($invoice->notes)
                                <div>
                                    <strong class="text-slate-505 font-bold block mb-1">Invoice Notes</strong>
                                    <p class="leading-relaxed whitespace-pre-line">{{ $invoice->notes }}</p>
                                </div>
                            @endif
                            @if($invoice->terms)
                                <div>
                                    <strong class="text-slate-505 font-bold block mb-1">Terms & Instructions</strong>
                                    <p class="leading-relaxed whitespace-pre-line">{{ $invoice->terms }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- ============================================= -->
        <!-- TEMPLATE 1: MODERN MINIMALIST                 -->
        <!-- ============================================= -->
        @if($invoice->template_id === 'modern')
            <div class="h-full flex flex-col gap-8">
                <!-- Header -->
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <!-- Logo -->
                        @if(isset($invoice->sender_additional['logo']) && $invoice->sender_additional['logo'])
                            <div style="display: inline-block;
                                        width: {{ $invoice->sender_additional['logo_width'] ?? 110 }}px;
                                        height: {{ $invoice->sender_additional['logo_height'] ?? 40 }}px;
                                        transform: translate({{ $invoice->sender_additional['logo_x'] ?? 0 }}px, {{ $invoice->sender_additional['logo_y'] ?? 0 }}px);
                                        margin-bottom: 0.75rem;">
                                <img src="{{ $invoice->sender_additional['logo'] }}" class="w-full h-full object-contain" />
                            </div>
                        @endif
                        <h1 class="text-4xl font-extrabold tracking-tight uppercase" style="color: {{ $invoice->theme_color }}">INVOICE</h1>
                        <span class="text-xs font-semibold text-slate-400 mt-1 block">No: <span class="text-slate-800 font-bold">{{ $invoice->invoice_number }}</span></span>
                    </div>
                    <div class="text-right text-xs">
                        <span class="text-base font-bold text-slate-800 block">{{ $invoice->sender_name }}</span>
                        <span class="block text-slate-505 mt-0.5">{{ $invoice->sender_email }}</span>
                        <span class="block text-slate-505">{{ $invoice->sender_phone }}</span>
                        <span class="block text-slate-400 mt-1 max-w-[220px] leading-snug break-words">{{ $invoice->sender_address }}</span>
                    </div>
                </div>

                <!-- Summary Bar -->
                <div class="border-y border-slate-100 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4 mt-2">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Date Issued</span>
                        <span class="text-xs font-semibold text-slate-800 mt-0.5 block">{{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Due Date</span>
                        <span class="text-xs font-semibold text-slate-800 mt-0.5 block">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Due on receipt' }}</span>
                    </div>
                    <div class="col-span-2 text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-sans">Amount Due</span>
                        <span class="text-2xl font-black mt-0.5 block" style="color: {{ $invoice->theme_color }}">{{ $symbol }}{{ number_format($invoice->total, 2) }}</span>
                    </div>
                </div>

                <!-- Addresses -->
                <div class="grid grid-cols-2 gap-8 mt-2">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Bill To</span>
                        <div class="text-xs text-slate-705 leading-relaxed">
                            <strong class="text-slate-800 block">{{ $invoice->client_name }}</strong>
                            @if(isset($invoice->client_additional['company']))
                                <span class="block font-semibold">{{ $invoice->client_additional['company'] }}</span>
                            @endif
                            @if($invoice->client_email)
                                <span class="block">{{ $invoice->client_email }}</span>
                            @endif
                            @if($invoice->client_phone)
                                <span class="block">{{ $invoice->client_phone }}</span>
                            @endif
                            <span class="block whitespace-pre-line mt-1 max-w-[240px]">{{ $invoice->client_address }}</span>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <div class="text-xs text-slate-500 leading-normal">
                            @if(isset($invoice->sender_additional['tax_id']))
                                <div><span class="font-semibold text-slate-400">Sender Tax ID:</span> <span>{{ $invoice->sender_additional['tax_id'] }}</span></div>
                            @endif
                            @if(isset($invoice->sender_additional['website']))
                                <div class="mt-0.5"><span class="font-semibold text-slate-400">Website:</span> <span>{{ $invoice->sender_additional['website'] }}</span></div>
                            @endif
                            @if(isset($invoice->client_additional['vat_number']))
                                <div class="mt-2"><span class="font-semibold text-slate-400">Client VAT:</span> <span>{{ $invoice->client_additional['vat_number'] }}</span></div>
                            @endif
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
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-800">{{ $item['description'] }}</td>
                                    <td class="py-3 text-center text-slate-600">{{ $item['quantity'] }}</td>
                                    <td class="py-3 text-right text-slate-600">{{ $symbol }}{{ number_format($item['rate'], 2) }}</td>
                                    <td class="py-3 text-right font-bold text-slate-900">{{ $symbol }}{{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Block & Signature -->
                <div class="flex justify-between items-end border-t border-slate-100 pt-4">
                    <!-- Left: Signature Preview -->
                    <div>
                        @if(isset($invoice->sender_additional['signature']) && $invoice->sender_additional['signature'])
                            <div class="flex flex-col items-start">
                                <div class="h-10 w-28 flex items-center justify-center overflow-hidden border-b border-slate-200 pb-1">
                                    <img src="{{ $invoice->sender_additional['signature'] }}" class="h-full w-full object-contain" />
                                </div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Authorized Signature</span>
                            </div>
                        @endif
                    </div>
                    <!-- Right: Totals -->
                    <div class="w-64 text-xs flex flex-col gap-2">
                        <div class="flex justify-between">
                            <span class="text-slate-450">Subtotal</span>
                            <span class="font-bold text-slate-800">{{ $symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @php
                            $totalItemsDiscount = 0;
                            $totalItemsTax = 0;
                            foreach($invoice->items as $it) {
                                $itemSub = $it['quantity'] * $it['rate'];
                                $discount = $itemSub * (($it['discount_rate'] ?? 0) / 100);
                                $taxable = $itemSub - $discount;
                                $totalItemsDiscount += $discount;
                                $totalItemsTax += $taxable * (($it['tax_rate'] ?? 0) / 100);
                            }
                        @endphp
                        @if($totalItemsDiscount > 0)
                            <div class="flex justify-between text-rose-600">
                                <span>Line Discounts</span>
                                <span>-{{ $symbol }}{{ number_format($totalItemsDiscount, 2) }}</span>
                            </div>
                        @endif

                        @if($invoice->discount_rate > 0)
                            @php
                                $subAfterLineDisc = $invoice->subtotal - $totalItemsDiscount;
                                $globalDiscAmount = $subAfterLineDisc * ($invoice->discount_rate / 100);
                            @endphp
                            <div class="flex justify-between text-rose-600">
                                <span>Global Discount ({{ $invoice->discount_rate }}%)</span>
                                <span>-{{ $symbol }}{{ number_format($globalDiscAmount, 2) }}</span>
                            </div>
                        @endif

                        @if($totalItemsTax > 0)
                            <div class="flex justify-between">
                                <span>Items Tax</span>
                                <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($totalItemsTax, 2) }}</span>
                            </div>
                        @endif

                        @if($invoice->tax_rate > 0)
                            @php
                                $subAfterAllDisc = ($invoice->subtotal - $totalItemsDiscount) - (($invoice->subtotal - $totalItemsDiscount) * ($invoice->discount_rate / 100));
                                $globalTaxAmount = $subAfterAllDisc * ($invoice->tax_rate / 100);
                            @endphp
                            <div class="flex justify-between">
                                <span>Global Tax ({{ $invoice->tax_rate }}%)</span>
                                <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($globalTaxAmount, 2) }}</span>
                            </div>
                        @endif

                        @if($invoice->shipping_cost > 0)
                            <div class="flex justify-between">
                                <span>Shipping & Handling</span>
                                <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($invoice->shipping_cost, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between text-sm font-bold border-y-4 border-double border-slate-900 py-2.5 text-slate-900 mt-2 font-serif">
                            <span>Total Due</span>
                            <span class="text-base">{{ $symbol }}{{ number_format($invoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer notes & terms -->
                <div class="border-t border-slate-200/80 pt-4 mt-auto font-sans">
                    <div class="grid grid-cols-2 gap-6 text-[9px] text-slate-400 uppercase tracking-wider leading-relaxed">
                        @if($invoice->notes)
                            <div>
                                <strong class="text-slate-500 font-bold block mb-1">Invoice Notes</strong>
                                <p class="whitespace-pre-line normal-case leading-normal">{{ $invoice->notes }}</p>
                            </div>
                        @endif
                        @if($invoice->terms)
                            <div>
                                <strong class="text-slate-500 font-bold block mb-1">Contract terms</strong>
                                <p class="whitespace-pre-line normal-case leading-normal">{{ $invoice->terms }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- ============================================= -->
        <!-- TEMPLATE 2: CLASSIC CORPORATE                 -->
        <!-- ============================================= -->
        @if($invoice->template_id === 'classic')
            <div class="h-full flex flex-col gap-6 border-2 border-double border-slate-300 p-3 rounded-lg">
                <!-- Top Header Bar -->
                <div class="flex justify-between items-start gap-4 border-b-2 border-slate-800 pb-4">
                    <div>
                        <!-- Logo -->
                        @if(isset($invoice->sender_additional['logo']) && $invoice->sender_additional['logo'])
                            <div style="display: inline-block;
                                        width: {{ $invoice->sender_additional['logo_width'] ?? 110 }}px;
                                        height: {{ $invoice->sender_additional['logo_height'] ?? 40 }}px;
                                        transform: translate({{ $invoice->sender_additional['logo_x'] ?? 0 }}px, {{ $invoice->sender_additional['logo_y'] ?? 0 }}px);
                                        margin-bottom: 0.75rem;">
                                <img src="{{ $invoice->sender_additional['logo'] }}" class="w-full h-full object-contain" />
                            </div>
                        @endif
                        <h1 class="text-3xl font-bold tracking-tight text-slate-800 block">INVOICE STATEMENT</h1>
                        <span class="text-xs block text-slate-505 mt-1">Invoice Reference: <strong class="text-slate-800">{{ $invoice->invoice_number }}</strong></span>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold block uppercase" style="color: {{ $invoice->theme_color }}">{{ $invoice->sender_name }}</span>
                        <span class="text-xs text-slate-505 block">{{ $invoice->sender_email }}</span>
                        <span class="text-xs text-slate-404 block">{{ $invoice->sender_phone }}</span>
                    </div>
                </div>

                <!-- Details Columns -->
                <div class="grid grid-cols-2 gap-6 my-2 text-xs">
                    <div>
                        <h3 class="font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-1 mb-2">Sender Address Details</h3>
                        <p class="whitespace-pre-line text-slate-600 leading-relaxed">{{ $invoice->sender_address }}</p>
                        @if(isset($invoice->sender_additional['tax_id']))
                            <div class="mt-2 text-slate-404 font-medium">VAT / Tax Reference: <span class="text-slate-707">{{ $invoice->sender_additional['tax_id'] }}</span></div>
                        @endif
                        @if(isset($invoice->sender_additional['website']))
                            <div class="mt-0.5 text-slate-404 font-medium font-mono">Web URL: <span class="text-slate-707">{{ $invoice->sender_additional['website'] }}</span></div>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-1 mb-2">Client / Bill Recipient</h3>
                        <strong class="text-slate-808 block">{{ $invoice->client_name }}</strong>
                        @if(isset($invoice->client_additional['company']))
                            <span class="block font-medium text-slate-600">{{ $invoice->client_additional['company'] }}</span>
                        @endif
                        <p class="whitespace-pre-line text-slate-600 mt-1 leading-relaxed">{{ $invoice->client_address }}</p>
                        @if(isset($invoice->client_additional['vat_number']))
                            <div class="mt-2 text-slate-404">VAT Registration: <span class="text-slate-707">{{ $invoice->client_additional['vat_number'] }}</span></div>
                        @endif
                    </div>
                </div>

                <!-- Date Grid Info -->
                <div class="grid grid-cols-3 gap-4 border-y border-slate-200 py-3 bg-slate-50/50">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Invoice Date</span>
                        <span class="text-xs font-semibold text-slate-700 mt-0.5 block">{{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Payment Term</span>
                        <span class="text-xs font-semibold text-slate-700 mt-0.5 block">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Due on receipt' }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Outstanding Amount</span>
                        <span class="text-base font-bold text-slate-900 mt-0.5 block">{{ $symbol }}{{ number_format($invoice->total, 2) }}</span>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="flex-grow">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-white text-[9px] font-semibold uppercase tracking-wider border-b border-slate-900" style="background-color: {{ $invoice->theme_color }}">
                                <th class="py-2 px-3">Description / Specifications</th>
                                <th class="py-2 px-3 text-center w-[12%]">Qty</th>
                                <th class="py-2 px-3 text-right w-[15%]">Rate</th>
                                <th class="py-2 px-3 text-right w-[18%]">Tax Rate</th>
                                <th class="py-2 px-3 text-right w-[18%]">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-xs text-slate-700">
                            @foreach($invoice->items as $item)
                                <tr class="odd:bg-white even:bg-slate-50/50">
                                    <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $item['description'] }}</td>
                                    <td class="py-2.5 px-3 text-center">{{ $item['quantity'] }}</td>
                                    <td class="py-2.5 px-3 text-right">{{ $symbol }}{{ number_format($item['rate'], 2) }}</td>
                                    <td class="py-2.5 px-3 text-right text-slate-400">{{ $item['tax_rate'] ?? 0 }}%</td>
                                    <td class="py-2.5 px-3 text-right font-semibold text-slate-800">{{ $symbol }}{{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Calculations block & Signature -->
                <div class="flex justify-between items-end mt-4">
                    <!-- Left: Signature Preview -->
                    <div>
                        @if(isset($invoice->sender_additional['signature']) && $invoice->sender_additional['signature'])
                            <div class="flex flex-col items-start">
                                <div class="h-10 w-28 flex items-center justify-center overflow-hidden border-b border-slate-200 pb-1">
                                    <img src="{{ $invoice->sender_additional['signature'] }}" class="h-full w-full object-contain" />
                                </div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Authorized Signature</span>
                            </div>
                        @endif
                    </div>
                    <!-- Right: Calculations -->
                    <div class="w-60 text-xs flex flex-col gap-1.5 border-t border-slate-300 pt-3">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Gross Subtotal</span>
                            <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @php
                            $totalItemsDiscount = 0;
                            $totalItemsTax = 0;
                            foreach($invoice->items as $it) {
                                $itemSub = $it['quantity'] * $it['rate'];
                                $discount = $itemSub * (($it['discount_rate'] ?? 0) / 100);
                                $taxable = $itemSub - $discount;
                                $totalItemsDiscount += $discount;
                                $totalItemsTax += $taxable * (($it['tax_rate'] ?? 0) / 100);
                            }
                        @endphp
                        @if($totalItemsDiscount > 0)
                            <div class="flex justify-between text-rose-600">
                                <span>Accumulated Discounts</span>
                                <span>-{{ $symbol }}{{ number_format($totalItemsDiscount, 2) }}</span>
                            </div>
                        @endif
                        @if($totalItemsTax > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-500">VAT / Tax Total</span>
                                <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($totalItemsTax, 2) }}</span>
                            </div>
                        @endif
                        @if($invoice->shipping_cost > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-500">Delivery fees</span>
                                <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($invoice->shipping_cost, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xs font-bold border-y-2 border-slate-800 py-2 text-slate-900 mt-2 uppercase tracking-wide">
                            <span>Grand Total Due</span>
                            <span class="text-sm font-black">{{ $symbol }}{{ number_format($invoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                @if($invoice->notes || $invoice->terms)
                    <div class="border-t border-slate-200 pt-4 mt-auto">
                        <div class="grid grid-cols-2 gap-4 text-[9px] text-slate-400">
                            @if($invoice->notes)
                                <div>
                                    <strong class="text-slate-500 font-bold block mb-1">Contract / Statement Notes</strong>
                                    <p class="whitespace-pre-line leading-relaxed">{{ $invoice->notes }}</p>
                                </div>
                            @endif
                            @if($invoice->terms)
                                <div>
                                    <strong class="text-slate-500 font-bold block mb-1">Remittance Instructions</strong>
                                    <p class="whitespace-pre-line leading-relaxed">{{ $invoice->terms }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- ============================================= -->
        <!-- TEMPLATE 3: CREATIVE TECH GRID                -->
        <!-- ============================================= -->
        @if($invoice->template_id === 'creative')
            <div class="h-full flex flex-col gap-6 text-slate-800">
                <!-- Top header block -->
                <div class="grid grid-cols-2 gap-4 pb-4">
                    <div>
                        <!-- Logo -->
                        @if(isset($invoice->sender_additional['logo']) && $invoice->sender_additional['logo'])
                            <div style="display: inline-block;
                                        width: {{ $invoice->sender_additional['logo_width'] ?? 110 }}px;
                                        height: {{ $invoice->sender_additional['logo_height'] ?? 40 }}px;
                                        transform: translate({{ $invoice->sender_additional['logo_x'] ?? 0 }}px, {{ $invoice->sender_additional['logo_y'] ?? 0 }}px);
                                        margin-bottom: 0.75rem;">
                                <img src="{{ $invoice->sender_additional['logo'] }}" class="w-full h-full object-contain" />
                            </div>
                        @endif
                        <span class="text-xs font-black block tracking-widest uppercase" style="color: {{ $invoice->theme_color }}">RECEIPT ENGINE</span>
                        <h1 class="text-3xl font-black tracking-tighter mt-1">#{{ $invoice->invoice_number }}</h1>
                    </div>
                    <div class="text-right text-xs">
                        <span class="font-bold text-sm block">{{ $invoice->sender_name }}</span>
                        <span class="text-slate-400 block">{{ $invoice->sender_email }}</span>
                        <span class="text-slate-400 block">{{ $invoice->sender_phone }}</span>
                        @if(isset($invoice->sender_additional['website']))
                            <span class="text-[10px] text-slate-400 block font-mono mt-1">{{ $invoice->sender_additional['website'] }}</span>
                        @endif
                    </div>
                </div>

                <!-- Visual Total Panel -->
                <div class="text-white rounded-xl p-4 flex justify-between items-center shadow-inner" style="background-color: {{ $invoice->theme_color }}">
                    <div class="text-left">
                        <span class="text-[9px] font-bold text-white/80 uppercase tracking-widest block font-sans">GRAND TOTAL</span>
                        <h2 class="text-2xl font-black mt-0.5">{{ $symbol }}{{ number_format($invoice->total, 2) }}</h2>
                    </div>
                    <div class="text-right text-[10px] text-white/80 leading-normal font-mono">
                        <div><strong class="text-white">ISSUED:</strong> <span>{{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '-' }}</span></div>
                        <div class="mt-0.5"><strong class="text-white">DUE DATE:</strong> <span>{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'ON RECEIPT' }}</span></div>
                    </div>
                </div>

                <!-- From/To Grid -->
                <div class="grid grid-cols-2 gap-6 my-2 text-xs">
                    <div class="bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">CLIENT BILLING TARGET</span>
                        <strong class="text-slate-800 block text-xs">{{ $invoice->client_name }}</strong>
                        @if(isset($invoice->client_additional['company']))
                            <span class="block font-medium text-slate-505">{{ $invoice->client_additional['company'] }}</span>
                        @endif
                        <p class="text-slate-405 whitespace-pre-line mt-1 max-w-[200px] leading-relaxed">{{ $invoice->client_address }}</p>
                    </div>
                    <div class="flex flex-col justify-between items-end text-right text-[10px]">
                        <div class="text-slate-400 leading-relaxed font-mono">
                            @if(isset($invoice->sender_additional['tax_id']))
                                <div>VAT REGISTER: <span class="text-slate-800 font-bold">{{ $invoice->sender_additional['tax_id'] }}</span></div>
                            @endif
                            @if(isset($invoice->client_additional['vat_number']))
                                <div class="mt-1">CLIENT VAT: <span class="text-slate-800 font-bold">{{ $invoice->client_additional['vat_number'] }}</span></div>
                            @endif
                            <div class="mt-3 text-slate-300">ADDRESS:</div>
                            <p class="text-slate-500 whitespace-pre-line mt-0.5 max-w-[180px] break-words">{{ $invoice->sender_address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="flex-grow">
                    <table class="w-full text-left font-mono border-collapse">
                        <thead>
                            <tr class="border-b-2 border-slate-900 text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-2.5">ITEM SUMMARY</th>
                                <th class="py-2.5 text-center w-[12%]">QTY</th>
                                <th class="py-2.5 text-right w-[20%]">RATE</th>
                                <th class="py-2.5 text-right w-[22%]">LINE SUM</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="py-3 font-sans font-bold text-slate-800">{{ $item['description'] }}</td>
                                    <td class="py-3 text-center">{{ $item['quantity'] }}</td>
                                    <td class="py-3 text-right">{{ $symbol }}{{ number_format($item['rate'], 2) }}</td>
                                    <td class="py-3 text-right font-bold text-slate-900">{{ $symbol }}{{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Block & Signature -->
                <div class="flex justify-between items-end border-t border-slate-150 pt-4">
                    <!-- Left: Signature Preview -->
                    <div>
                        @if(isset($invoice->sender_additional['signature']) && $invoice->sender_additional['signature'])
                            <div class="flex flex-col items-start">
                                <div class="h-10 w-28 flex items-center justify-center overflow-hidden border-b border-slate-200 pb-1">
                                    <img src="{{ $invoice->sender_additional['signature'] }}" class="h-full w-full object-contain" />
                                </div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Authorized Signature</span>
                            </div>
                        @endif
                    </div>
                    <!-- Right: Totals -->
                    <div class="w-56 text-[11px] flex flex-col gap-1.5 font-mono">
                        <div class="flex justify-between">
                            <span class="text-slate-455">SUBTOTAL</span>
                            <span class="font-bold text-slate-800">{{ $symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @php
                            $totalItemsDiscount = 0;
                            $totalItemsTax = 0;
                            foreach($invoice->items as $it) {
                                $itemSub = $it['quantity'] * $it['rate'];
                                $discount = $itemSub * (($it['discount_rate'] ?? 0) / 100);
                                $taxable = $itemSub - $discount;
                                $totalItemsDiscount += $discount;
                                $totalItemsTax += $taxable * (($it['tax_rate'] ?? 0) / 100);
                            }
                        @endphp
                        @if($totalItemsDiscount > 0)
                            <div class="flex justify-between text-rose-600">
                                <span>DISCOUNTS</span>
                                <span>-{{ $symbol }}{{ number_format($totalItemsDiscount, 2) }}</span>
                            </div>
                        @endif
                        @if($totalItemsTax > 0)
                            <div class="flex justify-between">
                                <span>VAT / TAX</span>
                                <span class="font-bold text-slate-800">{{ $symbol }}{{ number_format($totalItemsTax, 2) }}</span>
                            </div>
                        @endif
                        @if($invoice->shipping_cost > 0)
                            <div class="flex justify-between">
                                <span>SHIPPING</span>
                                <span class="font-bold text-slate-800">{{ $symbol }}{{ number_format($invoice->shipping_cost, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xs font-bold border-t border-slate-900 pt-2 text-slate-900 mt-1.5 font-sans">
                            <span>TOTAL DUE</span>
                            <span class="text-sm font-black">{{ $symbol }}{{ number_format($invoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                @if($invoice->notes || $invoice->terms)
                    <div class="bg-slate-50 rounded-xl p-4 mt-auto">
                        <div class="grid grid-cols-2 gap-4 text-[9px] text-slate-400">
                            @if($invoice->notes)
                                <div>
                                    <strong class="text-slate-655 font-bold block mb-1">TERMS & NOTES</strong>
                                    <p class="leading-relaxed whitespace-pre-line">{{ $invoice->notes }}</p>
                                </div>
                            @endif
                            @if($invoice->terms)
                                <div>
                                    <strong class="text-slate-655 font-bold block mb-1">TERMS & DETAILS</strong>
                                    <p class="leading-relaxed whitespace-pre-line">{{ $invoice->terms }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- ============================================= -->
        <!-- TEMPLATE 4: ELEGANT SLATE                     -->
        <!-- ============================================= -->
        @if($invoice->template_id === 'slate')
            <div class="h-full flex flex-col gap-6 font-serif text-slate-900 italic">
                <!-- Top Header block -->
                <div class="text-center pb-4 border-b border-slate-300">
                    <!-- Logo -->
                    @if(isset($invoice->sender_additional['logo']) && $invoice->sender_additional['logo'])
                        <div style="display: block; margin-left: auto; margin-right: auto;
                                    width: {{ $invoice->sender_additional['logo_width'] ?? 110 }}px;
                                    height: {{ $invoice->sender_additional['logo_height'] ?? 40 }}px;
                                    transform: translate({{ $invoice->sender_additional['logo_x'] ?? 0 }}px, {{ $invoice->sender_additional['logo_y'] ?? 0 }}px);
                                    margin-bottom: 0.75rem;">
                            <img src="{{ $invoice->sender_additional['logo'] }}" class="w-full h-full object-contain" />
                        </div>
                    @endif
                    <h1 class="text-3xl font-bold uppercase tracking-widest text-slate-800 not-italic">INVOICE STATEMENT</h1>
                    <span class="text-xs text-slate-450 mt-1 block">Reference identifier: <span class="font-bold text-slate-900">{{ $invoice->invoice_number }}</span></span>
                </div>

                <!-- Grid billing addresses -->
                <div class="grid grid-cols-2 gap-8 my-1 text-xs">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block not-italic mb-1">Originator</span>
                        <strong class="text-slate-900 block text-sm">{{ $invoice->sender_name }}</strong>
                        <span class="block text-slate-505 font-sans text-[11px] mt-0.5">{{ $invoice->sender_email }}</span>
                        <span class="block text-slate-505 font-sans text-[11px]">{{ $invoice->sender_phone }}</span>
                        <p class="text-slate-550 whitespace-pre-line mt-1.5 leading-relaxed max-w-[200px]">{{ $invoice->sender_address }}</p>
                        @if(isset($invoice->sender_additional['tax_id']))
                            <div class="mt-2 text-[10px] text-slate-400 font-bold not-italic">TAX ID: <span class="text-slate-707 font-semibold">{{ $invoice->sender_additional['tax_id'] }}</span></div>
                        @endif
                        @if(isset($invoice->sender_additional['website']))
                            <div class="mt-0.5 text-[10px] text-slate-400 font-bold not-italic">WEB: <span class="text-slate-707 font-semibold">{{ $invoice->sender_additional['website'] }}</span></div>
                        @endif
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block not-italic mb-1">Addressed to</span>
                        <strong class="text-slate-900 block text-sm">{{ $invoice->client_name }}</strong>
                        @if(isset($invoice->client_additional['company']))
                            <span class="block text-slate-600">{{ $invoice->client_additional['company'] }}</span>
                        @endif
                        <p class="text-slate-550 whitespace-pre-line mt-1.5 leading-relaxed text-right inline-block max-w-[200px]">{{ $invoice->client_address }}</p>
                        @if(isset($invoice->client_additional['vat_number']))
                            <div class="mt-2 text-[10px] text-slate-400 font-bold not-italic">CLIENT VAT: <span class="text-slate-707 font-semibold">{{ $invoice->client_additional['vat_number'] }}</span></div>
                        @endif
                    </div>
                </div>

                <!-- Date parameters table row -->
                <div class="flex justify-between border-y border-slate-205 py-3 text-xs italic">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block not-italic tracking-wider">Date generated</span>
                        <span class="mt-0.5 block font-semibold text-slate-800">{{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block not-italic tracking-wider">Maturity limit</span>
                        <span class="mt-0.5 block font-semibold text-slate-800">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Due immediately' }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-bold text-slate-400 uppercase block not-italic tracking-wider">Net Amount Due</span>
                        <span class="mt-0.5 block font-bold text-slate-900 not-italic text-sm">{{ $symbol }}{{ number_format($invoice->total, 2) }}</span>
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
                        <tbody class="divide-y divide-slate-105 text-slate-700">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-855">{{ $item['description'] }}</td>
                                    <td class="py-3 text-center">{{ $item['quantity'] }}</td>
                                    <td class="py-3 text-right">{{ $symbol }}{{ number_format($item['rate'], 2) }}</td>
                                    <td class="py-3 text-right text-slate-400 font-serif italic">{{ $item['tax_rate'] ?? 0 }}%</td>
                                    <td class="py-3 text-right font-semibold text-slate-900 font-serif italic">{{ $symbol }}{{ number_format($item['quantity'] * $item['rate'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Calculations totals block & Signature -->
                <div class="flex justify-between items-end border-t border-slate-200 pt-4 font-sans not-italic">
                    <!-- Left: Signature Preview -->
                    <div>
                        @if(isset($invoice->sender_additional['signature']) && $invoice->sender_additional['signature'])
                            <div class="flex flex-col items-start font-serif italic">
                                <div class="h-10 w-28 flex items-center justify-center overflow-hidden border-b border-slate-200 pb-1">
                                    <img src="{{ $invoice->sender_additional['signature'] }}" class="h-full w-full object-contain" />
                                </div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 block not-italic">Authorized Signature</span>
                            </div>
                        @endif
                    </div>
                    <!-- Right: Calculations -->
                    <div class="w-56 text-[11px] flex flex-col gap-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Statement Subtotal</span>
                            <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @php
                            $totalItemsDiscount = 0;
                            $totalItemsTax = 0;
                            foreach($invoice->items as $it) {
                                $itemSub = $it['quantity'] * $it['rate'];
                                $discount = $itemSub * (($it['discount_rate'] ?? 0) / 100);
                                $taxable = $itemSub - $discount;
                                $totalItemsDiscount += $discount;
                                $totalItemsTax += $taxable * (($it['tax_rate'] ?? 0) / 100);
                            }
                        @endphp
                        @if($totalItemsDiscount > 0)
                            <div class="flex justify-between text-rose-600">
                                <span>Aggregate Discounts</span>
                                <span>-{{ $symbol }}{{ number_format($totalItemsDiscount, 2) }}</span>
                            </div>
                        @endif
                        @if($totalItemsTax > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-400">VAT / Tax sum</span>
                                <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($totalItemsTax, 2) }}</span>
                            </div>
                        @endif
                        @if($invoice->shipping_cost > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-400">Delivery fees</span>
                                <span class="font-semibold text-slate-800">{{ $symbol }}{{ number_format($invoice->shipping_cost, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xs font-bold border-y-2 border-double border-slate-900 py-2 text-slate-900 mt-2 font-serif italic">
                            <span>Final Payable Balance</span>
                            <span class="text-sm not-italic font-black">{{ $symbol }}{{ number_format($invoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                @if($invoice->notes || $invoice->terms)
                    <div class="border-t border-slate-200/80 pt-4 mt-auto">
                        <div class="grid grid-cols-2 gap-4 text-[9px] text-slate-455 leading-relaxed uppercase tracking-wider not-italic font-sans">
                            @if($invoice->notes)
                                <div>
                                    <strong class="text-slate-650 font-bold block mb-1">Reference Notes</strong>
                                    <p class="whitespace-pre-line leading-normal lowercase first-letter:uppercase">{{ $invoice->notes }}</p>
                                </div>
                            @endif
                            @if($invoice->terms)
                                <div>
                                    <strong class="text-slate-655 font-bold block mb-1">Contract / Bank terms</strong>
                                    <p class="whitespace-pre-line leading-normal lowercase first-letter:uppercase">{{ $invoice->terms }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif


    </div>

    @guest
    <div class="guest-print-warning hidden text-center p-8 bg-white border border-slate-200 rounded-xl max-w-md mx-auto my-12 shadow-sm font-sans">
        <h2 class="text-xl font-bold text-slate-800">Print / Download Locked</h2>
        <p class="text-sm text-slate-500 mt-2">
            To download or print this invoice, please create a free account or log in. Your current draft will be automatically saved to your new dashboard!
        </p>
        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-500">Sign Up Free</a>
            <a href="{{ route('login') }}" class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-50">Log In</a>
        </div>
    </div>
    @endguest
</div>
@endsection
