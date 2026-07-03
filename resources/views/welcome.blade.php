@extends('invoices.layout')

@push('styles')
<style>
    .preserve-3d {
        transform-style: preserve-3d !important;
    }
    .float-3d-stamp {
        transform: translateZ(40px);
        transform-style: preserve-3d;
    }
    .float-3d-icon {
        transform: translateZ(30px);
        transform-style: preserve-3d;
    }
    .float-3d-text {
        transform: translateZ(20px);
        transform-style: preserve-3d;
    }
    /* Hero Text Mouse Parallax */
    .hero-parallax-content {
        transition: transform 0.25s cubic-bezier(0.25, 1, 0.5, 1);
        transform-style: preserve-3d;
    }
    /* Premium Hover Shine Effect */
    .shine-button {
        position: relative;
        overflow: hidden;
    }
    .shine-button::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle 120px at var(--x, 0px) var(--y, 0px), rgba(255, 255, 255, 0.32), transparent);
        opacity: 0;
        transition: opacity 0.25s ease;
        pointer-events: none;
        mix-blend-mode: overlay;
    }
    .shine-button:hover::after {
        opacity: 1;
    }
    /* Secondary button custom glare */
    .shine-button-secondary::after {
        background: radial-gradient(circle 120px at var(--x, 0px) var(--y, 0px), rgba(99, 102, 241, 0.16), transparent);
        mix-blend-mode: normal;
    }
    .shine-button-secondary:hover::after {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<!-- High-Tech 3D WebGL Background Canvas -->
<canvas id="marketing-three-canvas" class="fixed inset-0 w-full h-full -z-10 pointer-events-none bg-transparent opacity-60 dark:opacity-40"></canvas>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative overflow-hidden select-none hero-interactive-zone">
    
    <!-- Hero Section -->
    <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-12 items-center pt-8 pb-16 lg:py-20">
        <!-- Hero Text (Left 7 Cols) -->
        <div class="lg:col-span-7 flex flex-col gap-6 text-left relative z-10 hero-parallax-content">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                    <i class="fa-solid fa-sparkles animate-pulse"></i> Invoicing, Refined.
                </span>
            </div>
            
            <h1 id="hero-heading" class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-slate-900 dark:text-zinc-50 leading-[1.1] transition-all duration-300">
                Create Professional <br class="hidden sm:inline" />
                <span class="bg-gradient-to-r from-indigo-600 via-purple-650 to-pink-600 bg-clip-text text-transparent">Invoices in Seconds</span>
            </h1>
            
            <p class="text-sm sm:text-base text-slate-600 dark:text-zinc-400 max-w-xl leading-relaxed">
                Invoicify is an interactive workspace that helps you design, sign, stamp, and track beautiful client bills. Save templates, manage client records, and print pixel-perfect receipts with zero math errors.
            </p>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mt-2">
                @auth
                    <a href="{{ route('invoices.index') }}" class="tilt-button shine-button inline-flex items-center justify-center gap-2 text-xs font-extrabold uppercase tracking-wider bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white px-7 py-4 rounded-xl shadow-mui-2 hover:shadow-mui-8 transition-all">
                        <span>Go to Dashboard</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="tilt-button shine-button inline-flex items-center justify-center gap-2 text-xs font-extrabold uppercase tracking-wider bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white px-7 py-4 rounded-xl shadow-mui-2 hover:shadow-mui-8 transition-all">
                        <span>Start Creating Free</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="#features" class="tilt-button shine-button shine-button-secondary inline-flex items-center justify-center gap-2 text-xs font-extrabold uppercase tracking-wider bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-850 text-slate-700 dark:text-zinc-300 px-7 py-4 rounded-xl shadow-sm hover:shadow-mui-1 transition-all">
                        <span>Explore Features</span>
                    </a>
                @endauth
            </div>
            
            <!-- Trust indicators -->
            <div class="flex items-center gap-6 mt-4 border-t border-slate-200/60 dark:border-zinc-850 pt-6">
                <div>
                    <span class="text-xl font-black text-slate-900 dark:text-zinc-50 block">100%</span>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Free to use</span>
                </div>
                <div class="h-8 w-px bg-slate-200 dark:bg-zinc-850"></div>
                <div>
                    <span class="text-xl font-black text-slate-900 dark:text-zinc-50 block">Pixel-Perfect</span>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">A4 PDF Exports</span>
                </div>
                <div class="h-8 w-px bg-slate-200 dark:bg-zinc-850"></div>
                <div>
                    <span class="text-xl font-black text-slate-900 dark:text-zinc-50 block">Interactive</span>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">Live Preview</span>
                </div>
            </div>
        </div>
        
        <!-- Hero Invoice Floating Mockup (Right 5 Cols) -->
        <div class="lg:col-span-5 relative flex justify-center items-center">
            <!-- Background glow behind mockup -->
            <div class="absolute w-96 h-96 bg-indigo-500/20 dark:bg-indigo-600/10 blur-3xl rounded-full -z-10 animate-pulse"></div>
            
            <!-- Interactive 3D WebGL Canvas Card container (Scaled up container wrapper for larger, legible rendering) -->
            <div class="w-full max-w-[450px] aspect-[3/4] relative">
                <canvas id="hero-card-canvas" class="w-full h-full cursor-grab active:cursor-grabbing"></canvas>
                <!-- Drag to Spin Indicator -->
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-slate-900/80 dark:bg-zinc-950/80 backdrop-blur-sm px-3 py-1.5 rounded-full text-[9px] font-extrabold text-white uppercase tracking-wider flex items-center gap-1.5 pointer-events-none shadow-md border border-white/10">
                    <i class="fa-solid fa-arrows-spin animate-spin"></i> Drag to Spin 3D Card
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-16 border-t border-slate-200/60 dark:border-zinc-850 mt-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-zinc-50 tracking-tight">
                Packed with Features for Fast Billing
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mt-2">
                Invoicify gives you everything you need to request client payments quickly and present your business professionally.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Feature 1 -->
            <div class="tilt-card preserve-3d bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 shadow-sm hover:shadow-mui-2 transition-all duration-300">
                <div class="float-3d-icon h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 mb-4 shadow-sm">
                    <i class="fa-solid fa-wand-magic-sparkles text-base"></i>
                </div>
                <h3 class="float-3d-text text-sm font-bold text-slate-800 dark:text-zinc-100">Live Interactive Builder</h3>
                <p class="float-3d-text text-[11px] text-slate-500 dark:text-zinc-400 mt-1.5 leading-relaxed">
                    Create invoices interactively. Changes in fields, client notes, or tax rates recalculate totals on the fly without page reloads.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="tilt-card preserve-3d bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 shadow-sm hover:shadow-mui-2 transition-all duration-300">
                <div class="float-3d-icon h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-955/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 mb-4 shadow-sm">
                    <i class="fa-solid fa-signature text-base"></i>
                </div>
                <h3 class="float-3d-text text-sm font-bold text-slate-800 dark:text-zinc-100">Branded Logos & Signatures</h3>
                <p class="float-3d-text text-[11px] text-slate-500 dark:text-zinc-400 mt-1.5 leading-relaxed">
                    Personalize your bills by uploading a business logo image and drawing/typing your digital signature directly onto the page.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="tilt-card preserve-3d bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 shadow-sm hover:shadow-mui-2 transition-all duration-300">
                <div class="float-3d-icon h-10 w-10 rounded-xl bg-emerald-50 dark:bg-emerald-955/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mb-4 shadow-sm">
                    <i class="fa-solid fa-palette text-base"></i>
                </div>
                <h3 class="float-3d-text text-sm font-bold text-slate-800 dark:text-zinc-100">Custom Accent Themes</h3>
                <p class="float-3d-text text-[11px] text-slate-500 dark:text-zinc-400 mt-1.5 leading-relaxed">
                    Match client branding. Select color themes (Indigo, Emerald, Violet, Rose, or Charcoal) and template layouts with one click.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="tilt-card preserve-3d bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 shadow-sm hover:shadow-mui-2 transition-all duration-300">
                <div class="float-3d-icon h-10 w-10 rounded-xl bg-purple-50 dark:bg-purple-955/40 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 mb-4 shadow-sm">
                    <i class="fa-solid fa-chart-pie text-base"></i>
                </div>
                <h3 class="float-3d-text text-sm font-bold text-slate-800 dark:text-zinc-100">Revenue Analytics</h3>
                <p class="float-3d-text text-[11px] text-slate-500 dark:text-zinc-400 mt-1.5 leading-relaxed">
                    View total billed revenue, outstanding payments, and paid statuses instantly through an interactive dashboard statistic overview.
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="tilt-card preserve-3d bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-850 rounded-2xl p-6 shadow-sm hover:shadow-mui-2 transition-all duration-300">
                <div class="float-3d-icon h-10 w-10 rounded-xl bg-amber-50 dark:bg-amber-955/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 mb-4 shadow-sm">
                    <i class="fa-solid fa-print text-base"></i>
                </div>
                <h3 class="float-3d-text text-sm font-bold text-slate-800 dark:text-zinc-100">Pixel-Perfect Prints</h3>
                <p class="float-3d-text text-[11px] text-slate-500 dark:text-zinc-400 mt-1.5 leading-relaxed">
                    Built-in print stylesheet handles margin calculations, hiding controls, and resizing. Export as clean A4 PDF or print physical copies.
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="tilt-card preserve-3d bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-850 rounded-2xl p-6 shadow-sm hover:shadow-mui-2 transition-all duration-300">
                <div class="float-3d-icon h-10 w-10 rounded-xl bg-rose-50 dark:bg-rose-955/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 mb-4 shadow-sm">
                    <i class="fa-solid fa-briefcase text-base"></i>
                </div>
                <h3 class="float-3d-text text-sm font-bold text-slate-800 dark:text-zinc-100">Business Preferences</h3>
                <p class="float-3d-text text-[11px] text-slate-500 dark:text-zinc-400 mt-1.5 leading-relaxed">
                    Save defaults like currency ($, €, £, ₹), tax rates, payment terms, and client profiles to pre-populate drafts instantly.
                </p>
            </div>
        </div>
    </div>

    <!-- Live Interactive Sandbox Calculator Widget -->
    <div class="py-16 border-t border-slate-200/60 dark:border-zinc-850">
        <div class="tilt-container preserve-3d relative bg-slate-900/95 dark:bg-zinc-950/90 backdrop-blur-xl border border-slate-800 dark:border-zinc-800 rounded-[32px] p-8 sm:p-12 shadow-mui-24 overflow-hidden flex flex-col lg:flex-row items-center justify-between gap-12">
            <!-- Decorative canvas overlay or grid -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-purple-500/5 to-slate-950/30 -z-10"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/15 rounded-full blur-[100px] -z-10 animate-pulse"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-purple-500/15 rounded-full blur-[100px] -z-10 animate-pulse"></div>
            
            <div class="w-full lg:w-1/2 flex flex-col gap-5 float-3d-text">
                <span class="text-[10px] font-black uppercase text-indigo-400 tracking-widest flex items-center gap-1.5">
                    <i class="fa-solid fa-sparkles animate-pulse"></i> Try it instantly
                </span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-white leading-tight">
                    Interactive Billing Estimator
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-lg">
                    Need to run some quick billing math? Type your service details, adjust quantities, rates, and values to see how Invoicify computes taxes and totals in real-time.
                </p>
                
                <!-- Detailed bullet points with high contrast colors -->
                <ul class="flex flex-col gap-3.5 mt-2 text-xs text-slate-200">
                    <li class="flex items-center gap-2.5">
                        <div class="h-6 w-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </div>
                        <span class="font-medium">Real-time compound tax and discount calculations</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <div class="h-6 w-6 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-500/30">
                            <i class="fa-solid fa-bolt text-[10px]"></i>
                        </div>
                        <span class="font-medium">Zero latency, reactive Alpine.js engine</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <div class="h-6 w-6 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center shrink-0 border border-purple-500/30">
                            <i class="fa-solid fa-fingerprint text-[10px]"></i>
                        </div>
                        <span class="font-medium">No registration or account credentials needed to test</span>
                    </li>
                </ul>
            </div>
            
            <!-- Interactive Alpine-based Calculator Widget with high-contrast text and inputs -->
            <div class="float-3d-icon w-full lg:w-5/12 bg-slate-950/80 backdrop-blur-md border border-white/[0.08] rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden" x-data="{ qty: 2, rate: 150, tax: 10, discount: 5, currency: 'USD', shipping: 0 }">
                <h3 class="text-xs font-black text-slate-200 uppercase tracking-widest mb-6 pb-3 border-b border-white/[0.08] flex items-center justify-between">
                    <span>Quick Estimate</span>
                    <i class="fa-solid fa-calculator text-indigo-400"></i>
                </h3>
                
                <div class="space-y-4">
                    <!-- Qty & Rate Inputs -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Quantity</label>
                            <input type="number" x-model.number="qty" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-4 py-3 text-xs focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none text-white font-semibold transition-all duration-300" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Rate (Per Unit)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold" x-text="currency === 'USD' ? '$' : (currency === 'EUR' ? '€' : (currency === 'GBP' ? '£' : '₹'))"></span>
                                <input type="number" x-model.number="rate" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-7 pr-3 py-3 text-xs focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none text-white font-semibold transition-all duration-300" />
                            </div>
                        </div>
                    </div>

                    <!-- Currency & Shipping Inputs -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Currency</label>
                            <select x-model="currency" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-3 text-xs focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none text-white font-semibold transition-all duration-300 cursor-pointer">
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                                <option value="INR">INR (₹)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Shipping Fee</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold" x-text="currency === 'USD' ? '$' : (currency === 'EUR' ? '€' : (currency === 'GBP' ? '£' : '₹'))"></span>
                                <input type="number" x-model.number="shipping" class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-7 pr-3 py-3 text-xs focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none text-white font-semibold transition-all duration-300" />
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tax & Discount sliders -->
                    <div>
                        <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                            <span>Tax Rate</span>
                            <span class="text-indigo-400 font-mono font-bold" x-text="tax + '%'"></span>
                        </div>
                        <input type="range" min="0" max="30" x-model.number="tax" class="w-full appearance-none bg-slate-800 h-1.5 rounded-lg [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-indigo-500 [&::-webkit-slider-thumb]:shadow-[0_0_10px_rgba(99,102,241,0.5)] [&::-webkit-slider-thumb]:cursor-pointer [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:bg-indigo-500 [&::-moz-range-thumb]:cursor-pointer transition-all duration-300" />
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                            <span>Discount</span>
                            <span class="text-indigo-400 font-mono font-bold" x-text="discount + '%'"></span>
                        </div>
                        <input type="range" min="0" max="50" x-model.number="discount" class="w-full appearance-none bg-slate-800 h-1.5 rounded-lg [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-indigo-500 [&::-webkit-slider-thumb]:shadow-[0_0_10px_rgba(99,102,241,0.5)] [&::-webkit-slider-thumb]:cursor-pointer [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:bg-indigo-500 [&::-moz-range-thumb]:cursor-pointer transition-all duration-300" />
                    </div>
                    
                    <!-- Result Box with extremely clear high-contrast coloring -->
                    <div class="bg-gradient-to-br from-indigo-950/40 to-slate-900/40 border border-indigo-500/30 rounded-2xl p-5 mt-6 flex flex-col gap-3 relative overflow-hidden text-left">
                        <!-- Decorative glow corner -->
                        <div class="absolute -right-8 -bottom-8 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl pointer-events-none"></div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-300 font-medium">Subtotal:</span>
                            <span class="font-mono font-bold text-white" x-text="(currency === 'USD' ? '$' : (currency === 'EUR' ? '€' : (currency === 'GBP' ? '£' : '₹'))) + (qty * rate).toFixed(2)"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-rose-300 font-medium" x-text="'Discount (' + discount + '%):'"></span>
                            <span class="font-mono font-bold text-rose-400" x-text="'-' + (currency === 'USD' ? '$' : (currency === 'EUR' ? '€' : (currency === 'GBP' ? '£' : '₹'))) + ((qty * rate) * (discount / 100)).toFixed(2)"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-emerald-300 font-medium" x-text="'Tax (' + tax + '%):'"></span>
                            <span class="font-mono font-bold text-emerald-400" x-text="'+' + (currency === 'USD' ? '$' : (currency === 'EUR' ? '€' : (currency === 'GBP' ? '£' : '₹'))) + (((qty * rate) - ((qty * rate) * (discount / 100))) * (tax / 100)).toFixed(2)"></span>
                        </div>
                        <div x-show="shipping > 0" class="flex items-center justify-between text-xs" x-cloak>
                            <span class="text-slate-300 font-medium">Shipping:</span>
                            <span class="font-mono font-bold text-slate-200" x-text="'+' + (currency === 'USD' ? '$' : (currency === 'EUR' ? '€' : (currency === 'GBP' ? '£' : '₹'))) + shipping.toFixed(2)"></span>
                        </div>
                        
                        <div class="flex items-center justify-between border-t border-white/[0.08] pt-4 mt-2">
                            <span class="text-xs font-black text-white uppercase tracking-wider">Estimated Total</span>
                            <span class="text-lg font-black text-indigo-400 font-mono tracking-tight" x-text="(currency === 'USD' ? '$' : (currency === 'EUR' ? '€' : (currency === 'GBP' ? '£' : '₹'))) + (((qty * rate) - ((qty * rate) * (discount / 100))) * (1 + (tax / 100)) + (shipping || 0)).toFixed(2)"></span>
                        </div>
                    </div>

                    <!-- Convert to Invoice button (extremely user friendly link bridge) -->
                    <a :href="'/invoices/create?qty=' + qty + '&rate=' + rate + '&tax=' + tax + '&discount=' + discount + '&currency=' + currency + '&shipping=' + shipping" 
                       class="shine-button w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white text-xs font-extrabold uppercase tracking-wider shadow-md hover:shadow-lg transition-all duration-300">
                        <span>Convert to Invoice</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Table: The Old Way vs The Invoicify Way -->
    <div class="py-16 border-t border-slate-200/60 dark:border-zinc-850">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-zinc-50 tracking-tight">
                Designed to Upgrade Your Process
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mt-2">
                Why wrestle with manual templates when you can use a workspace designed exactly for the job?
            </p>
        </div>

        <div class="max-w-4xl mx-auto overflow-hidden bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/60 dark:border-zinc-800/80 rounded-2xl shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-850/60 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider">
                        <th class="p-4 sm:p-5">Feature</th>
                        <th class="p-4 sm:p-5">Word / Excel Templates</th>
                        <th class="p-4 sm:p-5 text-indigo-600 dark:text-indigo-400 bg-indigo-50/10">Invoicify Workspace</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-700 dark:text-zinc-350 divide-y divide-slate-100 dark:divide-zinc-800/65">
                    <tr>
                        <td class="p-4 sm:p-5 font-bold text-slate-800 dark:text-zinc-150">Calculations</td>
                        <td class="p-4 sm:p-5 text-slate-450"><i class="fa-solid fa-circle-xmark text-rose-500 mr-1.5"></i> Manual entry; prone to errors</td>
                        <td class="p-4 sm:p-5 bg-indigo-50/10 font-medium text-slate-800 dark:text-zinc-100"><i class="fa-solid fa-circle-check text-emerald-500 mr-1.5"></i> Instant, error-free automated math</td>
                    </tr>
                    <tr>
                        <td class="p-4 sm:p-5 font-bold text-slate-800 dark:text-zinc-150">Business Branding</td>
                        <td class="p-4 sm:p-5 text-slate-450"><i class="fa-solid fa-circle-xmark text-rose-500 mr-1.5"></i> Distorted images; poor layouts</td>
                        <td class="p-4 sm:p-5 bg-indigo-50/10 font-medium text-slate-800 dark:text-zinc-100"><i class="fa-solid fa-circle-check text-emerald-500 mr-1.5"></i> Pre-set structures with exact dimensions</td>
                    </tr>
                    <tr>
                        <td class="p-4 sm:p-5 font-bold text-slate-800 dark:text-zinc-150">Signatures & Seals</td>
                        <td class="p-4 sm:p-5 text-slate-450"><i class="fa-solid fa-circle-xmark text-rose-500 mr-1.5"></i> Hard to upload; requires external tools</td>
                        <td class="p-4 sm:p-5 bg-indigo-50/10 font-medium text-slate-800 dark:text-zinc-100"><i class="fa-solid fa-circle-check text-emerald-500 mr-1.5"></i> Draw or type your digital signature inline</td>
                    </tr>
                    <tr>
                        <td class="p-4 sm:p-5 font-bold text-slate-800 dark:text-zinc-150">A4 PDF Printing</td>
                        <td class="p-4 sm:p-5 text-slate-450"><i class="fa-solid fa-circle-xmark text-rose-500 mr-1.5"></i> Clunky margins; spills across pages</td>
                        <td class="p-4 sm:p-5 bg-indigo-50/10 font-medium text-slate-800 dark:text-zinc-100"><i class="fa-solid fa-circle-check text-emerald-500 mr-1.5"></i> CSS Print Media formatted strictly for A4</td>
                    </tr>
                    <tr>
                        <td class="p-4 sm:p-5 font-bold text-slate-800 dark:text-zinc-150">Status Tracking</td>
                        <td class="p-4 sm:p-5 text-slate-450"><i class="fa-solid fa-circle-xmark text-rose-500 mr-1.5"></i> Scattered in email folders and files</td>
                        <td class="p-4 sm:p-5 bg-indigo-50/10 font-medium text-slate-800 dark:text-zinc-100"><i class="fa-solid fa-circle-check text-emerald-500 mr-1.5"></i> Central database dashboard logs</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FAQ Accordion Section -->
    <div class="py-16 border-t border-slate-200/60 dark:border-zinc-850">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-zinc-50 tracking-tight">
                Frequently Asked Questions
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mt-2">
                Have questions about how Invoicify works? Read our quick answers below.
            </p>
        </div>

        <div class="max-w-3xl mx-auto space-y-4" x-data="{ activeFaq: null }">
            <!-- FAQ Item 1 -->
            <div class="tilt-card preserve-3d bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl overflow-hidden transition-all shadow-sm">
                <button @click="activeFaq === 1 ? activeFaq = null : activeFaq = 1" class="float-3d-text w-full text-left p-5 flex items-center justify-between font-bold text-xs sm:text-sm text-slate-800 dark:text-zinc-150 focus:outline-none select-none cursor-pointer">
                    <span>Is Invoicify completely free?</span>
                    <i class="fa-solid text-indigo-500" :class="activeFaq === 1 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <div x-show="activeFaq === 1" x-collapse x-transition.duration.300ms class="px-5 pb-5 text-xs text-slate-500 dark:text-zinc-400 leading-relaxed border-t border-slate-50 dark:border-zinc-850 pt-3">
                    Yes, Invoicify is 100% free to use. You can create as many invoices as you like, stamp logos/signatures, export them as print A4 PDFs, and manage drafts without any payment.
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="tilt-card preserve-3d bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-850 rounded-2xl overflow-hidden transition-all shadow-sm">
                <button @click="activeFaq === 2 ? activeFaq = null : activeFaq = 2" class="float-3d-text w-full text-left p-5 flex items-center justify-between font-bold text-xs sm:text-sm text-slate-800 dark:text-zinc-150 focus:outline-none select-none cursor-pointer">
                    <span>Do I need to sign up to create invoices?</span>
                    <i class="fa-solid text-indigo-500" :class="activeFaq === 2 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <div x-show="activeFaq === 2" x-collapse x-transition.duration.300ms class="px-5 pb-5 text-xs text-slate-500 dark:text-zinc-400 leading-relaxed border-t border-slate-50 dark:border-zinc-850 pt-3">
                    You can try out our live Sandbox widget on this landing page without creating an account. However, creating a free account is recommended if you wish to save invoice drafts, register persistent client/business records, and view revenue statistics over time.
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="tilt-card preserve-3d bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-850 rounded-2xl overflow-hidden transition-all shadow-sm">
                <button @click="activeFaq === 3 ? activeFaq = null : activeFaq = 3" class="float-3d-text w-full text-left p-5 flex items-center justify-between font-bold text-xs sm:text-sm text-slate-800 dark:text-zinc-150 focus:outline-none select-none cursor-pointer">
                    <span>Are my invoices private and secure?</span>
                    <i class="fa-solid text-indigo-500" :class="activeFaq === 3 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <div x-show="activeFaq === 3" x-collapse x-transition.duration.300ms class="px-5 pb-5 text-xs text-slate-500 dark:text-zinc-400 leading-relaxed border-t border-slate-50 dark:border-zinc-850 pt-3">
                    Absolutely. Your billing records are stored in a secured database scoped strictly to your authenticated session. We never distribute, sell, or publicly share your invoice statistics.
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom CTA Banner Section -->
    <div class="py-16 border-t border-slate-200/60 dark:border-zinc-850">
        <div class="tilt-container preserve-3d relative bg-gradient-to-r from-indigo-700 via-indigo-600 to-indigo-800 text-white rounded-3xl p-8 sm:p-12 text-center overflow-hidden shadow-mui-8">
            <!-- Decorative circle glow overlays -->
            <div class="absolute -top-24 -left-24 w-60 h-60 bg-white/5 blur-2xl rounded-full"></div>
            <div class="absolute -bottom-24 -right-24 w-60 h-60 bg-white/5 blur-2xl rounded-full"></div>
            
            <div class="relative z-10 max-w-2xl mx-auto flex flex-col gap-5 items-center float-3d-text">
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    Upgrade Your Billing Workflow Today
                </h2>
                <p class="text-xs sm:text-sm text-indigo-100 max-w-lg leading-relaxed">
                    Join freelancers and growing businesses that use Invoicify to look professional and speed up payouts. Create your free account in less than a minute.
                </p>
                <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full justify-center float-3d-text">
                    @auth
                        <a href="{{ route('invoices.index') }}" class="tilt-button shine-button inline-block text-xs font-extrabold uppercase tracking-wider bg-white text-indigo-700 hover:bg-slate-50 px-8 py-4 rounded-xl shadow-md transition-all">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="tilt-button shine-button inline-block text-xs font-extrabold uppercase tracking-wider bg-white text-indigo-700 hover:bg-slate-50 px-8 py-4 rounded-xl shadow-md transition-all">
                            Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="tilt-button inline-block text-xs font-extrabold uppercase tracking-wider bg-transparent border border-white/40 hover:bg-white/10 px-8 py-4 rounded-xl transition-all">
                            Log In to Account
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Three.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- 1. Init Three.js Background ---
        const canvas = document.getElementById('marketing-three-canvas');
        if (canvas) {
            const scene = new THREE.Scene();
            
            // Adjust camera based on aspect ratio
            const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 100);
            camera.position.z = 5;
            
            const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

            // Ambient Light
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
            scene.add(ambientLight);

            // Colored Directional Lights for futuristic highlights
            const light1 = new THREE.DirectionalLight(0x6366f1, 1.2); // Indigo
            light1.position.set(5, 5, 5);
            scene.add(light1);

            const light2 = new THREE.DirectionalLight(0xec4899, 0.8); // Pink
            light2.position.set(-5, -5, 5);
            scene.add(light2);

            // High-tech Constellation Network of Nodes
            const maxPoints = 85;
            const pointsGeometry = new THREE.BufferGeometry();
            const positions = new Float32Array(maxPoints * 3);
            const velocities = [];

            // Boundary parameters for points
            const bounds = { x: 8, y: 5, z: 4 };

            // Initialize point positions and velocities
            for (let i = 0; i < maxPoints; i++) {
                positions[i * 3] = (Math.random() - 0.5) * bounds.x;
                positions[i * 3 + 1] = (Math.random() - 0.5) * bounds.y;
                positions[i * 3 + 2] = (Math.random() - 0.5) * bounds.z;
                
                velocities.push({
                    x: (Math.random() - 0.5) * 0.004,
                    y: (Math.random() - 0.5) * 0.004,
                    z: (Math.random() - 0.5) * 0.004
                });
            }

            pointsGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

            // Custom glowing point material
            const pointsMaterial = new THREE.PointsMaterial({
                color: 0x6366f1,
                size: 0.08,
                transparent: true,
                opacity: 0.75,
                blending: THREE.AdditiveBlending
            });

            const pointCloud = new THREE.Points(pointsGeometry, pointsMaterial);
            scene.add(pointCloud);

            // Dynamic Lines connection geometry and material
            const linesGeometry = new THREE.BufferGeometry();
            const maxConnections = maxPoints * 5; 
            const linePositions = new Float32Array(maxConnections * 2 * 3);
            linesGeometry.setAttribute('position', new THREE.BufferAttribute(linePositions, 3));

            const linesMaterial = new THREE.LineBasicMaterial({
                color: 0x818cf8,
                transparent: true,
                opacity: 0.22,
                blending: THREE.AdditiveBlending
            });

            const lineSegments = new THREE.LineSegments(linesGeometry, linesMaterial);
            scene.add(lineSegments);

            // Mouse coordinates tracking
            let mouseX = 0;
            let mouseY = 0;
            let targetMouseX = 0;
            let targetMouseY = 0;

            window.addEventListener('mousemove', (e) => {
                targetMouseX = (e.clientX / window.innerWidth) - 0.5;
                targetMouseY = (e.clientY / window.innerHeight) - 0.5;
            });

            // Animation Loop
            const clock = new THREE.Clock();
            function animate() {
                requestAnimationFrame(animate);
                
                const time = clock.getElapsedTime();
                const positionsArray = pointsGeometry.attributes.position.array;

                // Move nodes and bounce them inside bounds with interactive mouse gravitational fields
                const mX = mouseX * bounds.x;
                const mY = -mouseY * bounds.y;

                for (let i = 0; i < maxPoints; i++) {
                    positionsArray[i * 3] += velocities[i].x;
                    positionsArray[i * 3 + 1] += velocities[i].y;
                    positionsArray[i * 3 + 2] += velocities[i].z;

                    // Organic swaying physics
                    positionsArray[i * 3] += Math.sin(time * 0.4 + i) * 0.0015;
                    positionsArray[i * 3 + 1] += Math.cos(time * 0.4 + i) * 0.0015;

                    // Boundary Bounces
                    if (Math.abs(positionsArray[i * 3]) > bounds.x / 2) {
                        velocities[i].x *= -1;
                    }
                    if (Math.abs(positionsArray[i * 3 + 1]) > bounds.y / 2) {
                        velocities[i].y *= -1;
                    }
                    if (Math.abs(positionsArray[i * 3 + 2]) > bounds.z / 2) {
                        velocities[i].z *= -1;
                    }

                    // Interactive cursor gravitational force field
                    const dx = positionsArray[i * 3] - mX;
                    const dy = positionsArray[i * 3 + 1] - mY;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 2.5 && dist > 0.1) {
                        const pushForce = (2.5 - dist) * 0.004;
                        positionsArray[i * 3] += (dx / dist) * pushForce;
                        positionsArray[i * 3 + 1] += (dy / dist) * pushForce;
                    }
                }
                pointsGeometry.attributes.position.needsUpdate = true;

                // Connect nearby nodes with lines
                let lineIdx = 0;
                const connectionDist = 2.1;
                const linePositionsArray = linesGeometry.attributes.position.array;

                for (let i = 0; i < maxPoints; i++) {
                    const x1 = positionsArray[i * 3];
                    const y1 = positionsArray[i * 3 + 1];
                    const z1 = positionsArray[i * 3 + 2];

                    for (let j = i + 1; j < maxPoints; j++) {
                        const x2 = positionsArray[j * 3];
                        const y2 = positionsArray[j * 3 + 1];
                        const z2 = positionsArray[j * 3 + 2];

                        const dx = x1 - x2;
                        const dy = y1 - y2;
                        const dz = z1 - z2;
                        const dist = Math.sqrt(dx * dx + dy * dy + dz * dz);

                        if (dist < connectionDist && lineIdx < maxConnections) {
                            linePositionsArray[lineIdx * 6] = x1;
                            linePositionsArray[lineIdx * 6 + 1] = y1;
                            linePositionsArray[lineIdx * 6 + 2] = z1;
                            linePositionsArray[lineIdx * 6 + 3] = x2;
                            linePositionsArray[lineIdx * 6 + 4] = y2;
                            linePositionsArray[lineIdx * 6 + 5] = z2;
                            lineIdx++;
                        }
                    }
                }
                linesGeometry.attributes.position.needsUpdate = true;
                linesGeometry.setDrawRange(0, lineIdx * 2);

                mouseX += (targetMouseX - mouseX) * 0.05;
                mouseY += (targetMouseY - mouseY) * 0.05;

                scene.rotation.y = mouseX * 0.25;
                scene.rotation.x = -mouseY * 0.20;

                pointCloud.rotation.y = time * 0.012;
                lineSegments.rotation.y = time * 0.012;

                renderer.render(scene, camera);
            }
            animate();

            // Resize viewport binding
            window.addEventListener('resize', () => {
                const width = window.innerWidth;
                const height = window.innerHeight;
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
                renderer.setSize(width, height);
            });
        }

        // --- 2. Interactive Magnetic Parallax Tilt for Buttons & Cards ---
        const applyInteractiveEffects = () => {
            // Magnetic Tilt for Buttons
            const buttons = document.querySelectorAll('.tilt-button');
            buttons.forEach(btn => {
                btn.style.transition = 'transform 0.15s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.15s ease-out';
                btn.style.transformStyle = 'preserve-3d';
                
                btn.addEventListener('mousemove', (e) => {
                    const rect = btn.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    // 1. Perspective 3D Tilt angles
                    const maxDegree = 12;
                    const rotateX = ((centerY - y) / centerY) * maxDegree;
                    const rotateY = ((x - centerX) / centerX) * maxDegree;
                    
                    // 2. Magnetic Pull translation offsets
                    const pullX = (x - centerX) * 0.25;
                    const pullY = (y - centerY) * 0.25;
                    
                    // 3. Dynamic Shadow shift
                    const shadowX = -rotateY * 2.0;
                    const shadowY = rotateX * 2.0;
                    const shadowBlur = 18 + Math.abs(rotateX) + Math.abs(rotateY);
                    
                    btn.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translate3d(${pullX}px, ${pullY}px, 10px) scale3d(1.06, 1.06, 1.06)`;
                    btn.style.boxShadow = `${shadowX}px ${shadowY}px ${shadowBlur}px rgba(99, 102, 241, 0.35)`;
                    
                    // 4. Update CSS radial coordinates for glass gloss shine glare follows cursor
                    btn.style.setProperty('--x', `${x}px`);
                    btn.style.setProperty('--y', `${y}px`);
                });
                
                btn.addEventListener('mouseleave', () => {
                    btn.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translate3d(0, 0, 0) scale3d(1, 1, 1)';
                    btn.style.boxShadow = '';
                });

                // Elastic scale compression click
                btn.addEventListener('mousedown', () => {
                    btn.style.transform += ' scale3d(0.92, 0.92, 0.92)';
                });
            });

            // Standard Tilt for Cards & Containers
            const apply3DTilt = (selector, maxDegree = 10, scale = 1.02) => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(el => {
                    el.style.transition = 'transform 0.2s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.2s ease-out';
                    
                    el.addEventListener('mousemove', (e) => {
                        const rect = el.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        const centerX = rect.width / 2;
                        const centerY = rect.height / 2;
                        
                        const rotateX = ((centerY - y) / centerY) * maxDegree;
                        const rotateY = ((x - centerX) / centerX) * maxDegree;
                        
                        el.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(${scale}, ${scale}, ${scale})`;
                    });
                    
                    el.addEventListener('mouseleave', () => {
                        el.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
                    });
                });
            };

            apply3DTilt('.tilt-card', 12, 1.03);
            apply3DTilt('.tilt-container', 4, 1.015);
        };

        applyInteractiveEffects();


        // --- 3. Hero Invoice Card Three.js Rendering ---
        const cardCanvas = document.getElementById('hero-card-canvas');
        if (cardCanvas) {
            const cardScene = new THREE.Scene();
            
            const cardCamera = new THREE.PerspectiveCamera(50, cardCanvas.clientWidth / cardCanvas.clientHeight, 0.1, 100);
            // Calibrate camera to z=4.3 (makes card look extremely large and prominent while remaining bounds protected during hover)
            cardCamera.position.z = 4.3;

            const cardRenderer = new THREE.WebGLRenderer({ canvas: cardCanvas, alpha: true, antialias: true });
            cardRenderer.setSize(cardCanvas.clientWidth, cardCanvas.clientHeight);
            cardRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

            // Clean white bright ambient light for legible rendering
            const ambient = new THREE.AmbientLight(0xffffff, 1.25);
            cardScene.add(ambient);

            // Camera-front white directional light to ensure perfect face lighting
            const dirLight = new THREE.DirectionalLight(0xffffff, 0.45);
            dirLight.position.set(0, 0, 5);
            cardScene.add(dirLight);

            // Helper to draw clean rounded rectangles on 2D context
            const drawRoundedRect = (gCtx, x, y, width, height, radius) => {
                gCtx.beginPath();
                gCtx.moveTo(x + radius, y);
                gCtx.lineTo(x + width - radius, y);
                gCtx.quadraticCurveTo(x + width, y, x + width, y + radius);
                gCtx.lineTo(x + width, y + height - radius);
                gCtx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
                gCtx.lineTo(x + radius, y + height);
                gCtx.quadraticCurveTo(x, y + height, x, y + height - radius);
                gCtx.lineTo(x, y + radius);
                gCtx.quadraticCurveTo(x, y, x + radius, y);
                gCtx.closePath();
            };

            // Draw HTML Invoice onto 2D texture canvas dynamically (2K high-resolution)
            const tCanvas = document.createElement('canvas');
            tCanvas.width = 2048;
            tCanvas.height = 2732;
            const ctx = tCanvas.getContext('2d');

            // Clear first for transparent corners
            ctx.clearRect(0, 0, 2048, 2732);

            // Set rounded clipping mask path (Radius 90px on 2K canvas)
            ctx.save();
            drawRoundedRect(ctx, 0, 0, 2048, 2732, 90);
            ctx.clip();

            // Draw background card (White)
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, 2048, 2732);

            // Top Bar decoration
            ctx.fillStyle = '#4f46e5';
            ctx.fillRect(0, 0, 2048, 50);

            // Logo Shield (Hexagon)
            ctx.fillStyle = '#4f46e5';
            ctx.beginPath();
            ctx.moveTo(260, 160);
            ctx.lineTo(370, 220);
            ctx.lineTo(370, 330);
            ctx.lineTo(260, 390);
            ctx.lineTo(150, 330);
            ctx.lineTo(150, 220);
            ctx.closePath();
            ctx.fill();

            // Inner Letter I
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 128px system-ui, -apple-system, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('I', 260, 320);
            ctx.textAlign = 'left';

            // Brand Title
            ctx.fillStyle = '#0f172a';
            ctx.font = 'bold 116px system-ui, -apple-system, sans-serif';
            ctx.fillText('Invoicify', 420, 270);
            ctx.fillStyle = '#6366f1';
            ctx.font = 'bold 48px system-ui, -apple-system, sans-serif';
            ctx.fillText('PROFESSIONAL BILLING', 420, 350);

            // PAID stamp
            ctx.fillStyle = '#ecfdf5';
            ctx.fillRect(1440, 160, 448, 170);
            ctx.strokeStyle = '#059669';
            ctx.lineWidth = 8;
            ctx.strokeRect(1440, 160, 448, 170);
            ctx.fillStyle = '#059669';
            ctx.font = 'bold 72px system-ui, -apple-system, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('PAID', 1664, 270);
            ctx.textAlign = 'left';

            // Metadata Block (Invoice Number, Date) - Pure Black/Solid Navy
            ctx.fillStyle = '#000000';
            ctx.font = 'bold 54px system-ui, -apple-system, sans-serif';
            ctx.textAlign = 'right';
            ctx.fillText('INVOICE NO: #INV-2026-0042', 1888, 430);
            ctx.font = 'bold 48px system-ui, -apple-system, sans-serif';
            ctx.fillStyle = '#0f172a';
            ctx.fillText('Date: Jul 03, 2026', 1888, 500);
            ctx.fillText('Due Date: Aug 02, 2026', 1888, 570);
            ctx.textAlign = 'left';

            // Divider line
            ctx.strokeStyle = '#cbd5e1';
            ctx.lineWidth = 6;
            ctx.beginPath();
            ctx.moveTo(160, 640);
            ctx.lineTo(1888, 640);
            ctx.stroke();

            // Light gray billing panels behind columns
            ctx.fillStyle = '#f8fafc';
            ctx.fillRect(160, 680, 820, 360);
            ctx.fillRect(1068, 680, 820, 360);
            ctx.strokeStyle = '#e2e8f0';
            ctx.lineWidth = 4;
            ctx.strokeRect(160, 680, 820, 360);
            ctx.strokeRect(1068, 680, 820, 360);

            // Address Columns (FROM & BILL TO) - Bold Solid Colors
            ctx.fillStyle = '#000000';
            ctx.font = 'bold 54px system-ui, -apple-system, sans-serif';
            ctx.fillText('ISSUED BY', 220, 750);
            ctx.fillText('BILL TO', 1128, 750);

            ctx.font = 'bold 60px system-ui, -apple-system, sans-serif';
            ctx.fillText('Acme Studio Inc.', 220, 830);
            ctx.fillText('Wayne Enterprises', 1128, 830);

            ctx.fillStyle = '#1e293b';
            ctx.font = 'bold 48px system-ui, -apple-system, sans-serif';
            ctx.fillText('123 Creative St, NY 10001', 220, 910);
            ctx.fillText('1007 Mountain Drive', 1128, 910);
            ctx.fillText('billing@acmestudio.com', 220, 980);
            ctx.fillText('gotham@waynecorp.com', 1128, 980);

            // Table Header Row
            ctx.fillStyle = '#0f172a'; // slate-900 header
            ctx.fillRect(160, 1100, 1728, 110);
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 44px system-ui, -apple-system, sans-serif';
            ctx.fillText('ITEM DESCRIPTION', 220, 1170);
            ctx.textAlign = 'center';
            ctx.fillText('QTY', 1150, 1170);
            ctx.textAlign = 'right';
            ctx.fillText('RATE', 1550, 1170);
            ctx.fillText('AMOUNT', 1820, 1170);

            // Table Rows
            const renderRow = (y, desc, qty, rate, amount) => {
                ctx.fillStyle = '#000000';
                ctx.font = 'bold 46px system-ui, -apple-system, sans-serif';
                ctx.textAlign = 'left';
                ctx.fillText(desc, 220, y);
                
                ctx.font = 'bold 46px system-ui, -apple-system, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(qty, 1150, y);
                
                ctx.textAlign = 'right';
                ctx.fillText(rate, 1550, y);
                
                ctx.font = 'bold 48px system-ui, -apple-system, sans-serif';
                ctx.fillText(amount, 1820, y);

                // Row border
                ctx.strokeStyle = '#e2e8f0';
                ctx.lineWidth = 3;
                ctx.beginPath();
                ctx.moveTo(160, y + 60);
                ctx.lineTo(1888, y + 60);
                ctx.stroke();
            };

            renderRow(1320, 'SaaS Architecture & Design', '1', '$4,500.00', '$4,500.00');
            renderRow(1480, 'Cloud Infrastructure Deployment', '2', '$450.00', '$900.00');

            // Financial Summary Block - Bold High Contrast
            ctx.fillStyle = '#000000';
            ctx.font = 'bold 44px system-ui, -apple-system, sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText('Subtotal:', 1160, 1700);
            ctx.fillText('Tax (10%):', 1160, 1780);
            
            ctx.font = 'black 64px system-ui, -apple-system, sans-serif';
            ctx.fillText('Grand Total:', 1160, 1890);

            ctx.font = 'bold 44px system-ui, -apple-system, sans-serif';
            ctx.textAlign = 'right';
            ctx.fillText('$5,400.00', 1820, 1700);
            ctx.fillText('$540.00', 1820, 1780);
            
            ctx.fillStyle = '#4f46e5';
            ctx.font = 'black 68px system-ui, -apple-system, sans-serif';
            ctx.fillText('$5,940.00', 1820, 1890);

            // Double accounting lines under grand total
            ctx.strokeStyle = '#4f46e5';
            ctx.lineWidth = 6;
            ctx.beginPath();
            ctx.moveTo(1160, 1915);
            ctx.lineTo(1820, 1915);
            ctx.moveTo(1160, 1928);
            ctx.lineTo(1820, 1928);
            ctx.stroke();

            // Signatures Block
            ctx.textAlign = 'left';
            ctx.fillStyle = '#000000';
            ctx.font = 'bold 44px system-ui, -apple-system, sans-serif';
            ctx.fillText('GENERATED BY', 160, 2080);
            ctx.fillText('AUTHORIZED SIGNATURE', 1280, 2080);

            ctx.font = 'bold 56px system-ui, -apple-system, sans-serif';
            ctx.fillText('Invoicify Billing Bot', 160, 2240);

            // Authorizer line
            ctx.strokeStyle = '#cbd5e1';
            ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.moveTo(1280, 2260);
            ctx.lineTo(1888, 2260);
            ctx.stroke();

            // Vector Signature in deep navy ink
            ctx.strokeStyle = '#0f172a';
            ctx.lineWidth = 10;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.beginPath();
            ctx.moveTo(1340, 2220);
            ctx.bezierCurveTo(1400, 2140, 1440, 2300, 1480, 2200);
            ctx.bezierCurveTo(1520, 2100, 1560, 2320, 1620, 2210);
            ctx.bezierCurveTo(1660, 2120, 1720, 2220, 1780, 2180);
            ctx.bezierCurveTo(1800, 2140, 1840, 2160, 1870, 2210);
            ctx.stroke();

            // Signature label under line
            ctx.fillStyle = '#1e293b';
            ctx.font = 'bold 38px system-ui, -apple-system, sans-serif';
            ctx.fillText('Bruce Wayne, Managing Director', 1280, 2315);

            // Terms Footnote
            ctx.fillStyle = '#1e293b';
            ctx.font = 'bold 38px system-ui, -apple-system, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('Thank you for choosing Invoicify. For payment instructions, refer to customer portal.', 1024, 2500);

            // Clean, thin card border
            ctx.strokeStyle = '#cbd5e1';
            ctx.lineWidth = 10;
            drawRoundedRect(ctx, 0, 0, 2048, 2732, 90);
            ctx.stroke();

            // Restore canvas context to complete clipping masks
            ctx.restore();

            // Generate texture with Mipmaps enabled for extreme legibility and sharpness
            const cardTexture = new THREE.CanvasTexture(tCanvas);
            cardTexture.colorSpace = THREE.SRGBColorSpace;
            cardTexture.minFilter = THREE.LinearMipmapLinearFilter;
            cardTexture.magFilter = THREE.LinearFilter;
            cardTexture.generateMipmaps = true;
            
            // Set Anisotropic Filtering to maximum supported by GPU
            const maxAnisotropy = cardRenderer.capabilities.getMaxAnisotropy();
            cardTexture.anisotropy = maxAnisotropy;
            cardTexture.needsUpdate = true;
            
            // Build the card mesh with PlaneGeometry scaled back to (2.35, 3.13) to maximize size and legibility when flat
            const cardGeo = new THREE.PlaneGeometry(2.35, 3.13);
            const cardMat = new THREE.MeshBasicMaterial({
                map: cardTexture,
                side: THREE.DoubleSide,
                transparent: true // Enable transparent rounded corners in 3D
            });
            const cardMesh = new THREE.Mesh(cardGeo, cardMat);
            cardScene.add(cardMesh);

            // Create texture for card backing with rounded corners
            const backCanvas = document.createElement('canvas');
            backCanvas.width = 256;
            backCanvas.height = 256;
            const bCtx = backCanvas.getContext('2d');
            bCtx.clearRect(0, 0, 256, 256);
            bCtx.fillStyle = '#0f172a'; // dark slate backing
            bCtx.beginPath();
            // rounded rect on backing canvas
            const r = 24;
            bCtx.moveTo(r, 0);
            bCtx.lineTo(256 - r, 0);
            bCtx.quadraticCurveTo(256, 0, 256, r);
            bCtx.lineTo(256, 256 - r);
            bCtx.quadraticCurveTo(256, 256, 256 - r, 256);
            bCtx.lineTo(r, 256);
            bCtx.quadraticCurveTo(0, 256, 0, 256 - r);
            bCtx.lineTo(0, r);
            bCtx.quadraticCurveTo(0, 0, r, 0);
            bCtx.closePath();
            bCtx.fill();

            const backTexture = new THREE.CanvasTexture(backCanvas);
            backTexture.colorSpace = THREE.SRGBColorSpace;

            // Double sided thickness backing frame
            const cardBackGeo = new THREE.PlaneGeometry(2.35, 3.13);
            const cardBackMat = new THREE.MeshBasicMaterial({
                map: backTexture,
                side: THREE.BackSide,
                transparent: true // Enable transparent backing rounded corners
            });
            const cardBackMesh = new THREE.Mesh(cardBackGeo, cardBackMat);
            cardBackMesh.position.z = -0.005; 
            cardScene.add(cardBackMesh);

            // Realistic 3D drop shadow plane behind card using the backing rounded texture silhouette
            const shadowGeo = new THREE.PlaneGeometry(2.35, 3.13);
            const shadowMat = new THREE.MeshBasicMaterial({
                map: backTexture, // Mapped backing canvas texture to gain rounded shadow corners
                color: 0x000000,  // Tint to solid black
                transparent: true,
                opacity: 0.16,
                blending: THREE.NormalBlending
            });
            const shadowMesh = new THREE.Mesh(shadowGeo, shadowMat);
            shadowMesh.position.set(0.08, -0.08, -0.06); 
            cardScene.add(shadowMesh);

            // Card Clock for tick calculations
            const cardClock = new THREE.Clock();

            // Spring Physics State for ultra-premium wobbly movements
            const springState = {
                rotX: 0, rotY: 0, posZ: 0,
                velRotX: 0, velRotY: 0, velPosZ: 0
            };
            const springStiffness = 0.08;
            const springDamping = 0.82;

            const updateSpring = (current, target, velocity) => {
                const force = (target - current) * springStiffness;
                velocity = (velocity + force) * springDamping;
                current += velocity;
                return [current, velocity];
            };

            // Interactive Drag Controls variables
            let isDragging = false;
            let isHovered = false;
            let previousMousePosition = { x: 0, y: 0 };
            
            let targetRotX = 0;
            let targetRotY = 0;
            let targetPosZ = 0;
            
            let lastInteractionTime = 0;

            // Track cursor position on hero interactive zone
            const heroInteractiveZone = document.querySelector('.hero-interactive-zone');
            let hZoneMouseX = 0;
            let hZoneMouseY = 0;

            if (heroInteractiveZone) {
                heroInteractiveZone.addEventListener('mousemove', (e) => {
                    const rect = heroInteractiveZone.getBoundingClientRect();
                    hZoneMouseX = ((e.clientX - rect.left) / rect.width) - 0.5;
                    hZoneMouseY = ((e.clientY - rect.top) / rect.height) - 0.5;

                    // Parallax text shadow and shifting on hero heading
                    const heading = document.getElementById('hero-heading');
                    if (heading) {
                        const moveX = hZoneMouseX * 18;
                        const moveY = hZoneMouseY * 12;
                        heading.style.textShadow = `${-hZoneMouseX * 12}px ${-hZoneMouseY * 12}px 24px rgba(99, 102, 241, 0.22)`;
                        
                        const textContent = document.querySelector('.hero-parallax-content');
                        if (textContent) {
                            textContent.style.transform = `translate3d(${moveX}px, ${moveY}px, 0)`;
                        }
                    }
                });
            }

            // Mouse enter/leave events to scale and lift card
            cardCanvas.addEventListener('mouseenter', () => {
                isHovered = true;
            });
            cardCanvas.addEventListener('mouseleave', () => {
                isHovered = false;
                isDragging = false;
            });

            // Bind drag events to cardCanvas
            cardCanvas.addEventListener('mousedown', (e) => {
                isDragging = true;
                lastInteractionTime = cardClock.getElapsedTime();
                previousMousePosition = { x: e.clientX, y: e.clientY };
            });

            window.addEventListener('mouseup', () => {
                isDragging = false;
            });

            cardCanvas.addEventListener('mousemove', (e) => {
                if (isDragging) {
                    lastInteractionTime = cardClock.getElapsedTime();
                    
                    const deltaMove = {
                        x: e.clientX - previousMousePosition.x,
                        y: e.clientY - previousMousePosition.y
                    };

                    cardMesh.rotation.y += deltaMove.x * 0.008;
                    cardMesh.rotation.x += deltaMove.y * 0.008;
                    
                    cardBackMesh.rotation.y = cardMesh.rotation.y;
                    cardBackMesh.rotation.x = cardMesh.rotation.x;

                    previousMousePosition = { x: e.clientX, y: e.clientY };
                }
            });

            // Card rendering ticks
            function tickCard() {
                requestAnimationFrame(tickCard);
                const time = cardClock.getElapsedTime();

                // Gentle vertical hover float offset
                const hoverOffset = Math.sin(time * 1.5) * 0.08;
                
                // Spring position Z targeting (lifting on hover, compressing on drag click)
                targetPosZ = isDragging ? -0.12 : (isHovered ? 0.30 : 0.0);
                const resPosZ = updateSpring(cardMesh.position.z, targetPosZ, springState.velPosZ);
                cardMesh.position.z = resPosZ[0];
                springState.velPosZ = resPosZ[1];

                cardMesh.position.y = hoverOffset;
                cardBackMesh.position.y = hoverOffset;
                
                // Rotation spring controls
                if (!isDragging) {
                    const idleTime = time - lastInteractionTime;
                    if (idleTime > 1.8) {
                        targetRotY = Math.sin(time * 0.6) * 0.16 + (hZoneMouseX * 0.45);
                        targetRotX = (hZoneMouseY * 0.35);
                    } else {
                        targetRotY = cardMesh.rotation.y;
                        targetRotX = cardMesh.rotation.x;
                    }
                    
                    const resRotY = updateSpring(cardMesh.rotation.y, targetRotY, springState.velRotY);
                    cardMesh.rotation.y = resRotY[0];
                    springState.velRotY = resRotY[1];

                    const resRotX = updateSpring(cardMesh.rotation.x, targetRotX, springState.velRotX);
                    cardMesh.rotation.x = resRotX[0];
                    springState.velRotX = resRotX[1];
                }

                cardBackMesh.position.z = cardMesh.position.z - 0.005;
                cardBackMesh.rotation.y = cardMesh.rotation.y;
                cardBackMesh.rotation.x = cardMesh.rotation.x;

                // Track and match 3D shadow mesh to card transformations
                shadowMesh.rotation.y = cardMesh.rotation.y;
                shadowMesh.rotation.x = cardMesh.rotation.x;
                shadowMesh.position.y = cardMesh.position.y - 0.06;
                shadowMesh.position.x = cardMesh.position.x + 0.06;
                shadowMesh.position.z = cardMesh.position.z - 0.04;

                cardRenderer.render(cardScene, cardCamera);
            }
            tickCard();

            // Resize viewport binding
            window.addEventListener('resize', () => {
                const width = cardCanvas.clientWidth;
                const height = cardCanvas.clientHeight;
                cardCamera.aspect = width / height;
                cardCamera.updateProjectionMatrix();
                cardRenderer.setSize(width, height);
            });
        }
    });
</script>
@endpush
