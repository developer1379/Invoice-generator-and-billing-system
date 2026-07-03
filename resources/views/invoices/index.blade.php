@extends('invoices.layout')

@section('content')
@php
    // Calculate dashboard statistics
    $grandTotal = $invoices->sum('total') ?: 1;
    $totalPaid = $invoices->where('status', 'paid')->sum('total');
    $totalSent = $invoices->where('status', 'sent')->sum('total');
    $totalOverdue = $invoices->where('status', 'overdue')->sum('total');
    $totalDraft = $invoices->where('status', 'draft')->sum('total');

    $paidPercent = min(100, round(($totalPaid / $grandTotal) * 100));
    $sentPercent = min(100, round(($totalSent / $grandTotal) * 100));
    $overduePercent = min(100, round(($totalOverdue / $grandTotal) * 100));
    $draftPercent = min(100, round(($totalDraft / $grandTotal) * 100));

    // Currency Symbols Configuration
    $currencySymbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'INR' => '₹',
    ];
    $userCurrency = auth()->check() ? auth()->user()->business_currency : 'USD';
    $userSymbol = $currencySymbols[$userCurrency] ?? '$';

    // Prepare monthly analytics data for the last 6 months
    $monthlyData = collect();
    for ($i = 5; $i >= 0; $i--) {
        $monthStart = now()->subMonths($i)->startOfMonth();
        $monthEnd = now()->subMonths($i)->endOfMonth();
        $monthName = now()->subMonths($i)->format('M');
        
        // Convert Carbon/date objects to string comparisons
        $startStr = $monthStart->format('Y-m-d');
        $endStr = $monthEnd->format('Y-m-d');
        
        $monthInvoices = $invoices->filter(function ($inv) use ($startStr, $endStr) {
            $invDate = $inv->invoice_date ? $inv->invoice_date->format('Y-m-d') : '';
            return $invDate >= $startStr && $invDate <= $endStr;
        });
        
        $paid = $monthInvoices->where('status', 'paid')->sum('total');
        $outstanding = $monthInvoices->where('status', '!=', 'paid')->sum('total');
        
        $monthlyData->push([
            'month' => $monthName,
            'paid' => $paid,
            'outstanding' => $outstanding,
            'total' => $paid + $outstanding
        ]);
    }
    $maxVal = $monthlyData->max('total') ?: 1000;
    $hasAnalyticsData = $monthlyData->sum('total') > 0;
@endphp

<div x-data="{ searchQuery: '', statusFilter: 'all' }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 relative">
    
    <!-- Hero / Header Section with 3D Three.js WebGL canvas background -->
    <div class="relative bg-slate-900 text-white rounded-2xl p-6 sm:p-8 shadow-mui-2 overflow-hidden mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 min-h-[140px] select-none border border-slate-800">
        
        <!-- Three.js Canvas -->
        <canvas id="three-canvas" class="absolute inset-0 w-full h-full -z-10 bg-slate-950 pointer-events-none"></canvas>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/50 to-transparent -z-10"></div>
        
        <div class="relative z-10">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Billing & Invoices</h1>
            <p class="text-xs sm:text-sm text-indigo-250 opacity-90 mt-1 max-w-xl">Manage business revenue, outstanding receipts, and clients in an interactive 3D workspace.</p>
        </div>
        <div class="relative z-10 shrink-0">
            <a href="{{ route('invoices.create') }}" class="shine-button w-full sm:w-auto inline-flex items-center justify-center gap-2 text-xs font-extrabold uppercase tracking-wider bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white px-5 py-3 rounded-xl shadow-mui-2 hover:shadow-mui-8 active:scale-95 transition-all duration-300">
                <i class="fa-solid fa-circle-plus text-sm"></i>
                <span>Create Invoice</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid (Material Design Card Elevation) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- Stat 1: Total Invoices -->
        <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-xl p-4 shadow-sm hover:shadow-mui-2 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider block">Total Invoices</span>
                <span class="text-xl font-extrabold text-slate-800 dark:text-zinc-100 mt-0.5 block">
                    {{ $invoices->count() }}
                </span>
            </div>
            <div class="h-9 w-9 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-file-invoice text-sm"></i>
            </div>
        </div>

        <!-- Stat 2: Total Value -->
        <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-xl p-4 shadow-sm hover:shadow-mui-2 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider block">Total Billed</span>
                <span class="text-xl font-extrabold text-slate-800 dark:text-zinc-100 mt-0.5 block">
                    {{ $userSymbol }}{{ number_format($invoices->sum('total'), 2) }}
                </span>
            </div>
            <div class="h-9 w-9 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-sack-dollar text-sm"></i>
            </div>
        </div>

        <!-- Stat 3: Paid Invoices -->
        <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-xl p-4 shadow-sm hover:shadow-mui-2 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider block">Paid Invoices</span>
                <span class="text-xl font-extrabold text-slate-800 dark:text-zinc-100 mt-0.5 block">
                    {{ $invoices->where('status', 'paid')->count() }}
                </span>
            </div>
            <div class="h-9 w-9 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-circle-check text-sm"></i>
            </div>
        </div>

        <!-- Stat 4: Unpaid Amount -->
        <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-xl p-4 shadow-sm hover:shadow-mui-2 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider block">Outstanding</span>
                <span class="text-xl font-extrabold text-slate-800 dark:text-zinc-100 mt-0.5 block">
                    {{ $userSymbol }}{{ number_format($invoices->where('status', '!=', 'paid')->sum('total'), 2) }}
                </span>
            </div>
            <div class="h-9 w-9 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-clock-rotate-left text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Revenue Analytics Chart Card (MUI / Modern Premium Style) -->
    <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-xl p-5 shadow-sm mb-6 flex flex-col gap-6" x-data="{ activeMonth: null }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xs font-black text-slate-800 dark:text-zinc-100 uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-indigo-500"></i> Revenue Analytics
                </h2>
                <p class="text-[10px] text-slate-450 dark:text-zinc-500 mt-0.5">Collected earnings vs outstanding billing allocations for the last 6 months.</p>
            </div>
            
            <!-- Legend indicators -->
            <div class="flex items-center gap-4 text-[10px] font-bold">
                <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                    <span class="h-2.5 w-2.5 rounded bg-gradient-to-tr from-emerald-600 to-emerald-500 shadow-sm block"></span>
                    Paid Revenue
                </span>
                <span class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400">
                    <span class="h-2.5 w-2.5 rounded bg-gradient-to-tr from-amber-500 to-amber-400 shadow-sm block"></span>
                    Outstanding
                </span>
            </div>
        </div>

        @if($hasAnalyticsData)
            <!-- High-fidelity bar chart container -->
            <div class="flex items-end justify-between h-44 pt-4 border-b border-slate-100 dark:border-zinc-800/60 px-2 sm:px-8 relative">
                <!-- Grid Lines Backdrop -->
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-6 pt-4">
                    <div class="border-b border-dashed border-slate-100 dark:border-zinc-800/40 w-full h-0"></div>
                    <div class="border-b border-dashed border-slate-100 dark:border-zinc-800/40 w-full h-0"></div>
                    <div class="border-b border-dashed border-slate-100 dark:border-zinc-800/40 w-full h-0"></div>
                    <div class="border-b border-dashed border-slate-100 dark:border-zinc-800/40 w-full h-0"></div>
                </div>

                <!-- Monthly Bars -->
                @foreach($monthlyData as $index => $data)
                    @php
                        $paidHeight = ($data['paid'] / $maxVal) * 100;
                        $outstandingHeight = ($data['outstanding'] / $maxVal) * 100;
                        $totalHeight = ($data['total'] / $maxVal) * 100;
                    @endphp
                    <div class="flex flex-col items-center gap-2 flex-1 group z-10 relative"
                         @mouseenter="activeMonth = {{ $index }}"
                         @mouseleave="activeMonth = null">
                        
                        <!-- Tooltip Overlay -->
                        <div x-show="activeMonth === {{ $index }}"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute bottom-36 bg-slate-900 dark:bg-zinc-950 text-white text-[10px] rounded-xl p-3 shadow-mui-8 flex flex-col gap-1.5 w-40 pointer-events-none z-30 border border-slate-800"
                             x-cloak>
                            <span class="font-black border-b border-slate-800 dark:border-zinc-900 pb-1 mb-1 block text-slate-200">{{ $data['month'] }} Statement</span>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Paid:</span>
                                <span class="font-mono font-bold text-emerald-400">{{ $userSymbol }}{{ number_format($data['paid'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Outstanding:</span>
                                <span class="font-mono font-bold text-amber-400">{{ $userSymbol }}{{ number_format($data['outstanding'], 2) }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-800 dark:border-zinc-900 pt-1.5 mt-1 font-black text-white">
                                <span>Total:</span>
                                <span class="font-mono text-indigo-400">{{ $userSymbol }}{{ number_format($data['total'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Stacked columns visual -->
                        <div class="h-32 flex flex-col justify-end w-8 sm:w-12 bg-slate-50 dark:bg-zinc-850 rounded-t-lg overflow-hidden border border-slate-100 dark:border-zinc-800/40 relative shadow-sm group-hover:shadow-md transition-all duration-300">
                            <!-- Outstanding (Amber) -->
                            <div class="bg-gradient-to-t from-amber-500 to-amber-400 w-full transition-all duration-500 hover:brightness-105"
                                 style="height: {{ $outstandingHeight }}%"></div>
                            <!-- Paid (Emerald) -->
                            <div class="bg-gradient-to-t from-emerald-600 to-emerald-500 w-full transition-all duration-500 hover:brightness-105"
                                 style="height: {{ $paidHeight }}%"></div>
                        </div>

                        <!-- Month Tag -->
                        <span class="text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-widest mt-1 group-hover:text-indigo-650 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $data['month'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Elegant Empty State Placeholder Chart -->
            <div class="flex flex-col items-center justify-center py-10 relative bg-slate-50 dark:bg-zinc-850/40 rounded-2xl border border-dashed border-slate-200 dark:border-zinc-800">
                <div class="h-10 w-10 bg-slate-100 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 rounded-full flex items-center justify-center mb-2.5">
                    <i class="fa-solid fa-chart-line text-sm"></i>
                </div>
                <span class="text-xs font-bold text-slate-700 dark:text-zinc-300">No monthly metrics recorded</span>
                <span class="text-[10px] text-slate-400 dark:text-zinc-500 mt-1 max-w-xs text-center px-4 leading-relaxed">
                    Generate and save your first business invoices to automatically populate your monthly revenue analysis.
                </span>
            </div>
        @endif
    </div>

    <!-- main 2-column workspace layout -->
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <!-- Left: Main list & interactive controls (2/3 width) -->
        <div class="w-full lg:w-2/3 flex flex-col gap-4">
            
            <!-- Controls card (Search & Filters - MUI Style) -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-xl p-3 shadow-sm flex flex-col sm:flex-row items-center gap-3">
                <!-- Search Input -->
                <div class="w-full sm:w-60 relative">
                    <input type="text" x-model="searchQuery" placeholder="Search invoices..." class="w-full rounded-lg border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-950/40 pl-8 pr-3 py-1.5 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all dark:text-zinc-100 placeholder-slate-400 dark:placeholder-zinc-650" />
                    <div class="absolute left-2.5 top-2 text-slate-400 dark:text-zinc-600 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                </div>

                <!-- Filters Navigation Bar -->
                <div class="flex flex-wrap items-center gap-1 w-full sm:w-auto sm:ml-auto">
                    <button type="button" @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-605 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800'" class="px-2.5 py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer">
                        All
                    </button>
                    <button type="button" @click="statusFilter = 'paid'" :class="statusFilter === 'paid' ? 'bg-emerald-600 text-white' : 'text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10'" class="px-2.5 py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer">
                        Paid
                    </button>
                    <button type="button" @click="statusFilter = 'sent'" :class="statusFilter === 'sent' ? 'bg-blue-600 text-white' : 'text-blue-600 dark:text-blue-400 hover:bg-blue-500/10'" class="px-2.5 py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer">
                        Sent
                    </button>
                    <button type="button" @click="statusFilter = 'overdue'" :class="statusFilter === 'overdue' ? 'bg-rose-600 text-white' : 'text-rose-600 dark:text-rose-400 hover:bg-rose-500/10'" class="px-2.5 py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer">
                        Overdue
                    </button>
                    <button type="button" @click="statusFilter = 'draft'" :class="statusFilter === 'draft' ? 'bg-slate-500 text-white' : 'text-slate-500 dark:text-zinc-400 hover:bg-slate-500/10'" class="px-2.5 py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer">
                        Draft
                    </button>
                </div>
            </div>

            <!-- Invoices Cards Grid -->
            @if($invoices->isEmpty())
                <!-- Empty State -->
                <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-xl shadow-sm overflow-hidden text-center py-16 px-4">
                    <div class="h-12 w-12 bg-slate-50 dark:bg-zinc-800/50 text-slate-400 dark:text-zinc-500 rounded-xl flex items-center justify-center mx-auto mb-3 border border-slate-200/50 dark:border-zinc-800/20">
                        <i class="fa-solid fa-folder-open text-lg"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-zinc-200">No invoices generated yet</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1 max-w-xs mx-auto">Create a professional invoice using our blueprints.</p>
                    <a href="{{ route('invoices.create') }}" class="shine-button inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white px-5 py-3 rounded-xl shadow-mui-2 hover:shadow-mui-8 active:scale-95 transition-all mt-5 duration-300">
                        <i class="fa-solid fa-circle-plus text-sm"></i>
                        <span>Generate First Invoice</span>
                    </a>
                </div>
            @else
                <!-- Responsive Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($invoices as $invoice)
                        <div 
                            x-show="(statusFilter === 'all' || '{{ $invoice->status }}' === statusFilter) && 
                                    ('{{ strtolower($invoice->client_name) }}'.includes(searchQuery.toLowerCase()) || 
                                     '{{ strtolower($invoice->invoice_number) }}'.includes(searchQuery.toLowerCase()) ||
                                     '{{ strtolower($invoice->client_additional['company'] ?? '') }}'.includes(searchQuery.toLowerCase()))"
                            class="bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-805 rounded-xl p-4 shadow-sm hover:shadow-mui-2 hover:-translate-y-0.5 active:translate-y-0 transition-all flex flex-col justify-between gap-4 group relative overflow-hidden"
                        >
                            <!-- Status Side Accent Line -->
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 
                                @if($invoice->status === 'paid') bg-emerald-500 
                                @elseif($invoice->status === 'sent') bg-blue-500 
                                @elseif($invoice->status === 'overdue') bg-rose-500 
                                @else bg-slate-400 @endif">
                            </div>

                            <!-- Card Header (Invoice # & Status Badge) -->
                            <div class="pl-2 flex items-center justify-between gap-2">
                                <span class="text-xs font-black text-slate-905 dark:text-zinc-100 tracking-wider">
                                    {{ $invoice->invoice_number }}
                                </span>
                                
                                @if($invoice->status === 'paid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-250/20 dark:border-emerald-900/20">Paid</span>
                                @elseif($invoice->status === 'sent')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border border-blue-250/20 dark:border-blue-900/20">Sent</span>
                                @elseif($invoice->status === 'overdue')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-250/20 dark:border-rose-900/20">Overdue</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-700 dark:bg-zinc-800 dark:text-zinc-400 border border-slate-200/60 dark:border-zinc-700/30">Draft</span>
                                @endif
                            </div>

                            <!-- Card Body (Client name & Company) -->
                            <div class="pl-2 flex flex-col gap-0.5">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-user text-slate-400 dark:text-zinc-650 text-[10px]"></i>
                                    <span class="text-xs font-bold text-slate-750 dark:text-zinc-200 truncate">{{ $invoice->client_name }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 dark:text-zinc-500 pl-4 truncate">
                                    {{ $invoice->client_additional['company'] ?? 'Individual client' }}
                                </span>

                                <div class="mt-2.5 font-sans not-italic">
                                    <span class="text-base font-black text-slate-900 dark:text-zinc-50">
                                        {{ $invoice->currency }} {{ $currencySymbols[$invoice->currency] ?? '$' }}{{ number_format($invoice->total, 2) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Details (Issue & Due dates) -->
                            <div class="pl-2 border-t border-slate-100 dark:border-zinc-800/60 pt-3 grid grid-cols-2 gap-2 text-[10px]">
                                <div>
                                    <span class="text-slate-400 dark:text-zinc-500 block uppercase tracking-wider text-[8px] font-bold">Issued</span>
                                    <span class="font-bold text-slate-655 dark:text-zinc-400">{{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '-' }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-slate-400 dark:text-zinc-500 block uppercase tracking-wider text-[8px] font-bold">Due</span>
                                    <span class="font-bold text-slate-655 dark:text-zinc-400">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Receipt' }}</span>
                                </div>
                            </div>

                            <!-- Card Footer Action Buttons -->
                            <div class="pl-2 border-t border-slate-100 dark:border-zinc-800/60 pt-3 flex items-center justify-between gap-2 mt-auto">
                                <a href="{{ route('invoices.show', $invoice) }}" class="flex-grow inline-flex items-center justify-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/30 dark:hover:bg-indigo-950/50 dark:text-indigo-400 py-1.5 rounded-lg text-[10px] font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-eye text-[9px]"></i>
                                    <span>View & Print</span>
                                </a>

                                <div class="flex items-center gap-1">
                                    <!-- Edit -->
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="h-7 w-7 rounded-lg border border-slate-200 dark:border-zinc-800 text-slate-500 hover:text-amber-600 dark:text-zinc-400 dark:hover:text-amber-400 flex items-center justify-center bg-slate-50 dark:bg-zinc-850 hover:bg-white dark:hover:bg-zinc-800 transition-all" title="Edit">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </a>
                                    <!-- Delete -->
                                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Delete this invoice?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-7 w-7 rounded-lg border border-slate-200 dark:border-zinc-800 text-slate-400 hover:text-rose-600 dark:text-zinc-500 dark:hover:text-rose-400 flex items-center justify-center bg-slate-50 dark:bg-zinc-850 hover:bg-white dark:hover:bg-zinc-800 cursor-pointer transition-all" title="Delete">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
        
        <!-- Right: Side columns (1/3 width) -->
        <div class="w-full lg:w-1/3 flex flex-col gap-4">
            
            <!-- Revenue Status Distribution Visual Panel -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-xl p-4 shadow-sm flex flex-col gap-4">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 dark:text-zinc-100 uppercase tracking-wider">Revenue Distribution</h3>
                    <p class="text-[10px] text-slate-400 dark:text-zinc-500 mt-0.5">Summary of billing status allocations.</p>
                </div>
                
                <div class="flex flex-col gap-3">
                    <!-- Progress item 1: Paid -->
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <span class="h-1 w-1 rounded-full bg-emerald-500"></span>
                                Paid ({{ $paidPercent }}%)
                            </span>
                            <span class="text-slate-800 dark:text-zinc-200 font-bold">{{ $userSymbol }}{{ number_format($totalPaid, 2) }}</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $paidPercent }}%"></div>
                        </div>
                    </div>

                    <!-- Progress item 2: Sent -->
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                <span class="h-1 w-1 rounded-full bg-blue-500"></span>
                                Sent ({{ $sentPercent }}%)
                            </span>
                            <span class="text-slate-800 dark:text-zinc-200 font-bold">{{ $userSymbol }}{{ number_format($totalSent, 2) }}</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full transition-all duration-300" style="width: {{ $sentPercent }}%"></div>
                        </div>
                    </div>

                    <!-- Progress item 3: Overdue -->
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                <span class="h-1 w-1 rounded-full bg-rose-500"></span>
                                Overdue ({{ $overduePercent }}%)
                            </span>
                            <span class="text-slate-800 dark:text-zinc-200 font-bold">{{ $userSymbol }}{{ number_format($totalOverdue, 2) }}</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-rose-500 rounded-full transition-all duration-300" style="width: {{ $overduePercent }}%"></div>
                        </div>
                    </div>

                    <!-- Progress item 4: Draft -->
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-slate-500 dark:text-zinc-400 flex items-center gap-1">
                                <span class="h-1 w-1 rounded-full bg-slate-400"></span>
                                Draft ({{ $draftPercent }}%)
                            </span>
                            <span class="text-slate-800 dark:text-zinc-200 font-bold">{{ $userSymbol }}{{ number_format($totalDraft, 2) }}</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-slate-400 rounded-full transition-all duration-300" style="width: {{ $draftPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Audit Feed Card -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-xl p-4 shadow-sm flex flex-col gap-4">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 dark:text-zinc-100 uppercase tracking-wider">System Feed</h3>
                    <p class="text-[10px] text-slate-400 dark:text-zinc-500 mt-0.5">Latest account event logs.</p>
                </div>
                
                <div class="flex flex-col gap-3">
                    @if($invoices->isNotEmpty())
                        @foreach($invoices->take(3) as $activity)
                            <div class="flex gap-2 text-[11px] leading-snug">
                                <div class="h-6 w-6 bg-slate-50 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 rounded flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-slate-700 dark:text-zinc-300">
                                        Invoice <span class="font-bold text-slate-900 dark:text-zinc-100">{{ $activity->invoice_number }}</span> saved
                                    </span>
                                    <span class="text-[9px] text-slate-400 dark:text-zinc-500">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-[10px] text-slate-400 dark:text-zinc-650">
                            No logs recorded.
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

    <!-- Material Floating Action Button (FAB) (Sticky bottom-right) -->
    <a href="{{ route('invoices.create') }}" class="shine-button no-print fixed bottom-6 right-6 h-14 w-14 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center shadow-mui-8 hover:shadow-mui-24 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all z-40 cursor-pointer" title="Create New Invoice">
        <i class="fa-solid fa-plus text-lg"></i>
    </a>
</div>
@endsection

@push('scripts')
<!-- Three.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('three-canvas');
        if (canvas) {
            const scene = new THREE.Scene();
            
            // Adjust camera based on aspect ratio
            const camera = new THREE.PerspectiveCamera(70, canvas.clientWidth / canvas.clientHeight, 0.1, 100);
            
            const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
            renderer.setSize(canvas.clientWidth, canvas.clientHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

            // Ambient Light
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.65);
            scene.add(ambientLight);

            // Colored Directional Lights for futuristic highlights
            const light1 = new THREE.DirectionalLight(0x6366f1, 1.5);
            light1.position.set(4, 3, 2);
            scene.add(light1);

            const light2 = new THREE.DirectionalLight(0x3b82f6, 1.0);
            light2.position.set(-4, -3, 2);
            scene.add(light2);

            // Spawning floating 3D document geometries
            const geometry = new THREE.PlaneGeometry(1.4, 1.9);
            
            const materials = [
                new THREE.MeshPhongMaterial({ color: 0x4f46e5, side: THREE.DoubleSide, transparent: true, opacity: 0.8, shininess: 60 }),
                new THREE.MeshPhongMaterial({ color: 0x6366f1, side: THREE.DoubleSide, transparent: true, opacity: 0.7, shininess: 60 }),
                new THREE.MeshPhongMaterial({ color: 0x3b82f6, side: THREE.DoubleSide, transparent: true, opacity: 0.6, shininess: 60 }),
                new THREE.MeshPhongMaterial({ color: 0x1e1b4b, side: THREE.DoubleSide, transparent: true, opacity: 0.9, shininess: 80 })
            ];
            
            const documents = [];
            for (let i = 0; i < 4; i++) {
                const doc = new THREE.Mesh(geometry, materials[i % materials.length]);
                doc.position.set(
                    (Math.random() - 0.5) * 5,
                    (Math.random() - 0.5) * 2,
                    -1 - Math.random() * 2
                );
                doc.rotation.set(Math.random() * 1.5, Math.random() * 1.5, Math.random() * 1.5);
                scene.add(doc);
                documents.push({
                    mesh: doc,
                    rotX: (Math.random() - 0.5) * 0.006,
                    rotY: (Math.random() - 0.5) * 0.006,
                    rotZ: (Math.random() - 0.5) * 0.006,
                    floatOffset: Math.random() * 100
                });
            }

            // Interactive constellation stars
            const particleGeo = new THREE.BufferGeometry();
            const count = 120;
            const positions = new Float32Array(count * 3);
            for (let i = 0; i < count * 3; i++) {
                positions[i] = (Math.random() - 0.5) * 8;
            }
            particleGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
            
            const particleMat = new THREE.PointsMaterial({
                size: 0.025,
                color: 0x818cf8,
                transparent: true,
                opacity: 0.55
            });
            const particles = new THREE.Points(particleGeo, particleMat);
            scene.add(particles);

            camera.position.z = 3.5;

            // Mouse coordinates tracking
            let mouseX = 0;
            let mouseY = 0;
            window.addEventListener('mousemove', (e) => {
                mouseX = (e.clientX / window.innerWidth) - 0.5;
                mouseY = (e.clientY / window.innerHeight) - 0.5;
            });

            // Rendering animation loop
            const clock = new THREE.Clock();
            function tick() {
                requestAnimationFrame(tick);
                const time = clock.getElapsedTime();

                // Documents movement & gentle floating
                documents.forEach(doc => {
                    doc.mesh.rotation.x += doc.rotX;
                    doc.mesh.rotation.y += doc.rotY;
                    doc.mesh.rotation.z += doc.rotZ;
                    doc.mesh.position.y += Math.sin(time + doc.floatOffset) * 0.0008;
                });

                // Particle orbit rotation
                particles.rotation.y = time * 0.015;
                particles.rotation.x = time * 0.008;

                // Subtle camera viewport panning based on mouse cursor position
                camera.position.x += (mouseX * 1.5 - camera.position.x) * 0.05;
                camera.position.y += (-mouseY * 1.0 - camera.position.y) * 0.05;
                camera.lookAt(0, 0, 0);

                renderer.render(scene, camera);
            }
            tick();

            // Resize viewport binding
            window.addEventListener('resize', () => {
                const width = canvas.clientWidth;
                const height = canvas.clientHeight;
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
                renderer.setSize(width, height);
            });
        }
    });
</script>
@endpush
