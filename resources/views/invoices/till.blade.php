@extends('invoices.layout')

@section('content')
<div x-data="{
    searchQuery: '',
    currency: 'USD',
    currencySymbol: '$',
    discountRate: 0,
    taxRate: 10,
    shippingCost: 0,
    themeColor: '#4f46e5',
    templateId: 'blueprint',
    
    // POS Cart
    cart: [],
    
    // Product List (from server)
    products: {{ json_encode($products) }},
    
    // Tour guide state
    tourActive: false,
    tourStep: 1,
    showTourPrompt: false,
    
    init() {
        if (!localStorage.getItem('invoicify_till_tour_completed')) {
            this.showTourPrompt = true;
        }
    },
    
    startTour() {
        this.showTourPrompt = false;
        this.tourStep = 1;
        this.tourActive = true;
        this.$nextTick(() => {
            this.scrollToStep();
        });
    },
    
    nextStep() {
        if (this.tourStep < 6) {
            this.tourStep++;
            this.$nextTick(() => {
                this.scrollToStep();
            });
        } else {
            this.completeTour();
        }
    },
    
    prevStep() {
        if (this.tourStep > 1) {
            this.tourStep--;
            this.$nextTick(() => {
                this.scrollToStep();
            });
        }
    },
    
    completeTour() {
        this.tourActive = false;
        localStorage.setItem('invoicify_till_tour_completed', 'true');
    },
    
    scrollToStep() {
        const targets = {
            1: '#tour-step-catalog',
            2: '.tour-target-card',
            3: '#tour-step-cart',
            4: '#tour-step-config',
            5: '#tour-step-client',
            6: '#tour-step-checkout'
        };
        const selector = targets[this.tourStep];
        const el = document.querySelector(selector);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    },
    
    getTourStyle() {
        const targets = {
            1: '#tour-step-catalog',
            2: '.tour-target-card',
            3: '#tour-step-cart',
            4: '#tour-step-config',
            5: '#tour-step-client',
            6: '#tour-step-checkout'
        };
        const selector = targets[this.tourStep];
        const el = document.querySelector(selector);
        if (!el) {
            return 'top: 50%; left: 50%; transform: translate(-50%, -50%); position: fixed; z-index: 50; width: 320px;';
        }
        
        const rect = el.getBoundingClientRect();
        const scrollY = window.scrollY;
        const scrollX = window.scrollX;
        
        let top = rect.bottom + scrollY + 12;
        let left = rect.left + scrollX;
        
        if (left + 320 > window.innerWidth) {
            left = window.innerWidth - 340;
        }
        
        return `top: ${top}px; left: ${Math.max(16, left)}px; position: absolute; z-index: 50; width: 320px;`;
    },
    
    updateCurrencySymbol() {
        const symbols = { 'USD': '$', 'EUR': '€', 'GBP': '£', 'INR': '₹' };
        this.currencySymbol = symbols[this.currency] || '$';
    },
    
    addToCart(product) {
        let existing = this.cart.find(item => item.product_id === product.id);
        if (existing) {
            existing.quantity++;
        } else {
            this.cart.push({
                product_id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                tax_rate: parseFloat(product.tax_rate || 0),
                image_url: product.image_url || null,
                quantity: 1
            });
        }
    },
    
    removeFromCart(index) {
        this.cart.splice(index, 1);
    },
    
    incrementQty(index) {
        this.cart[index].quantity++;
    },
    
    decrementQty(index) {
        if (this.cart[index].quantity > 1) {
            this.cart[index].quantity--;
        } else {
            this.removeFromCart(index);
        }
    },
    
    // Computations
    getSubtotal() {
        return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },
    
    getDiscountAmount() {
        return this.getSubtotal() * (this.discountRate / 100);
    },
    
    getTaxableAmount() {
        return Math.max(0, this.getSubtotal() - this.getDiscountAmount());
    },
    
    getTaxAmount() {
        return this.cart.reduce((sum, item) => {
            const itemSub = item.price * item.quantity;
            const itemDisc = itemSub * (this.discountRate / 100);
            const itemTaxable = itemSub - itemDisc;
            return sum + (itemTaxable * (item.tax_rate / 100));
        }, 0);
    },
    
    getTotal() {
        return this.getTaxableAmount() + this.getTaxAmount() + parseFloat(this.shippingCost || 0);
    }
}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 relative">

    <!-- Tour Prompt Banner -->
    <div x-show="showTourPrompt" x-transition class="no-print bg-indigo-600 text-white rounded-2xl p-4 shadow-mui-2 mb-6 flex flex-col sm:flex-row items-center justify-between gap-3 border border-indigo-500 select-none" x-cloak>
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 bg-white/10 rounded-lg flex items-center justify-center text-white shrink-0">
                <i class="fa-solid fa-wand-magic-sparkles text-sm animate-pulse"></i>
            </div>
            <div>
                <p class="text-xs font-bold">New to the Point of Sale Register?</p>
                <p class="text-[10px] text-indigo-100 mt-0.5 font-semibold">Start our interactive guide tour to learn how to load products, configure styles, and check out receipts.</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" @click="showTourPrompt = false" class="px-3 py-1.5 text-[10px] font-bold text-indigo-150 hover:text-white transition-colors cursor-pointer">
                Skip
            </button>
            <button type="button" @click="startTour()" class="px-4 py-1.5 bg-white text-indigo-600 hover:bg-indigo-50 rounded-lg text-[10px] font-extrabold shadow-sm transition-all cursor-pointer">
                Take Interactive Tour
            </button>
        </div>
    </div>

    <!-- POS Header Banner -->
    <div class="relative bg-slate-900 text-white rounded-2xl p-6 shadow-mui-2 overflow-hidden mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-slate-800 select-none">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-[100px] -z-10 animate-pulse"></div>
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 bg-indigo-500/15 rounded-xl flex items-center justify-center border border-indigo-500/20 text-indigo-400">
                <i class="fa-solid fa-cash-register text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Interactive Cash Register (Till)</h1>
                <p class="text-xs text-indigo-250 opacity-90">Tap items to ring up custom client cart receipts in real-time.</p>
            </div>
        </div>
        <button type="button" @click="startTour()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white border border-indigo-500 hover:border-indigo-400 rounded-xl text-xs font-bold flex items-center gap-2 transition-all cursor-pointer shadow-sm">
            <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
            Restart Guide Tour
        </button>
    </div>

    <!-- Main Register Workspace Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Pane: Product Catalog Grid (7/12 cols) -->
        <div class="lg:col-span-7 flex flex-col gap-4">
            
            <!-- Search & Catalog Header -->
            <div id="tour-step-catalog" 
                 :class="{ 'ring-4 ring-indigo-500 scale-[1.01] shadow-mui-24 relative z-40 transition-all duration-300': tourActive && tourStep === 1 }"
                 class="bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-805 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full">
                    <input type="text" x-model="searchQuery" placeholder="Search product SKU or name..." class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 rounded-lg pl-9 pr-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none dark:text-zinc-100 placeholder-slate-400" />
                    <div class="absolute left-3 top-2.5 text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                </div>
            </div>

            <!-- Products Catalog Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <template x-for="(product, index) in products.filter(p => p.name.toLowerCase().includes(searchQuery.toLowerCase()) || (p.sku && p.sku.toLowerCase().includes(searchQuery.toLowerCase())))" :key="product.id">
                    <div :class="{ 
                             'ring-4 ring-indigo-500 scale-[1.01] shadow-mui-24 relative z-40 transition-all duration-300 tour-target-card': index === 0 && tourActive && tourStep === 2,
                             'tour-target-card': index === 0 
                         }"
                         class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-xl p-4 shadow-sm hover:shadow-mui-2 hover:-translate-y-0.5 active:translate-y-0 transition-all flex flex-col justify-between gap-3 relative overflow-hidden group">
                        
                        <!-- Top details -->
                        <div>
                            <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 dark:text-zinc-550 select-none">
                                <span class="font-mono uppercase" x-text="product.sku || 'ITEM'"></span>
                                <span class="bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 px-2 py-0.5 rounded" x-text="'Tax: ' + parseFloat(product.tax_rate) + '%'"></span>
                            </div>
                            
                            <div class="flex items-start gap-2.5 mt-2">
                                <template x-if="product.image_url">
                                    <img :src="product.image_url" class="h-10 w-10 rounded-lg object-cover bg-slate-50 border border-slate-205 dark:border-zinc-800 shrink-0" />
                                </template>
                                <template x-if="!product.image_url">
                                    <div class="h-10 w-10 rounded-lg bg-slate-50 dark:bg-zinc-850 border border-slate-205 dark:border-zinc-800 flex items-center justify-center text-slate-400 shrink-0">
                                        <i class="fa-solid fa-box text-xs"></i>
                                    </div>
                                </template>
                                <div class="overflow-hidden">
                                    <h3 class="text-xs font-extrabold text-slate-800 dark:text-zinc-100 truncate" x-text="product.name"></h3>
                                    <p class="text-[10px] text-slate-500 dark:text-zinc-400 line-clamp-2 mt-0.5 leading-normal" x-text="product.description || 'No description provided.'"></p>
                                </div>
                            </div>
                            
                            <!-- Show dynamic fields tags -->
                            <template x-if="product.custom_fields">
                                <div class="flex flex-wrap gap-1 mt-2.5">
                                    <template x-for="(val, key) in product.custom_fields">
                                        <template x-if="val">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400 border border-indigo-200/20 dark:border-indigo-900/10">
                                                <span x-text="key + ': ' + val"></span>
                                            </span>
                                        </template>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Footer: Price & Add Trigger -->
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-zinc-800/60 mt-auto">
                            <span class="text-base font-black text-slate-900 dark:text-zinc-50 font-mono" x-text="currencySymbol + parseFloat(product.price).toFixed(2)"></span>
                            <button type="button" @click="addToCart(product)" class="h-8 w-8 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white flex items-center justify-center shadow-sm hover:shadow-md cursor-pointer transition-all active:scale-90">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right Pane: Cart Register & Checkout (5/12 cols) -->
        <div class="lg:col-span-5">
            <form action="{{ route('till.store') }}" method="POST" class="bg-white dark:bg-zinc-900 border border-slate-205 dark:border-zinc-805 rounded-xl p-5 shadow-sm flex flex-col gap-5">
                @csrf
                
                <!-- Dynamic cart inputs compiled by template -->
                <div class="hidden">
                    <template x-for="(item, index) in cart" :key="index">
                        <div>
                            <input type="hidden" :name="'cart_items[' + index + '][name]'" :value="item.name" />
                            <input type="hidden" :name="'cart_items[' + index + '][quantity]'" :value="item.quantity" />
                            <input type="hidden" :name="'cart_items[' + index + '][price]'" :value="item.price" />
                            <input type="hidden" :name="'cart_items[' + index + '][tax_rate]'" :value="item.tax_rate" />
                        </div>
                    </template>
                </div>

                <h2 class="text-xs font-black text-slate-800 dark:text-zinc-100 uppercase tracking-wider flex items-center gap-1.5 select-none">
                    <i class="fa-solid fa-cart-shopping text-indigo-500"></i> Cashier Cart
                </h2>

                <!-- Cart Items List -->
                <div id="tour-step-cart" 
                     :class="{ 'ring-4 ring-indigo-500 scale-[1.01] shadow-mui-24 relative z-40 transition-all duration-300': tourActive && tourStep === 3 }"
                     class="border border-slate-205 dark:border-zinc-800 rounded-xl p-3 bg-slate-50/50 dark:bg-zinc-950/20 max-h-56 overflow-y-auto overflow-x-hidden flex flex-col gap-2.5 mb-4">
                    <template x-if="cart.length === 0">
                        <div class="text-center py-8 text-slate-400 select-none">
                            <i class="fa-solid fa-receipt text-lg block mb-1"></i>
                            <span class="text-[10px] font-bold uppercase tracking-wider">Register is empty</span>
                        </div>
                    </template>
                    
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="flex items-center justify-between gap-3 text-xs border-b border-slate-100 dark:border-zinc-800 pb-2.5 last:border-b-0 last:pb-0">
                            <!-- Left: Thumbnail & Name -->
                            <div class="flex items-center gap-2.5 max-w-[55%]">
                                <!-- Thumbnail image -->
                                <template x-if="item.image_url">
                                    <img :src="item.image_url" class="h-8 w-8 rounded-lg object-cover bg-slate-100 border border-slate-205 dark:border-zinc-800 shrink-0" />
                                </template>
                                <template x-if="!item.image_url">
                                    <div class="h-8 w-8 rounded-lg bg-slate-100 dark:bg-zinc-800 border border-slate-205 dark:border-zinc-800 flex items-center justify-center text-slate-400 shrink-0 select-none">
                                        <i class="fa-solid fa-box text-[10px]"></i>
                                    </div>
                                </template>
                                <div class="flex flex-col overflow-hidden">
                                    <span class="font-extrabold text-slate-800 dark:text-zinc-150 truncate" x-text="item.name"></span>
                                    <span class="text-[9px] text-slate-400 dark:text-zinc-500 font-mono" x-text="currencySymbol + item.price.toFixed(2) + ' each'"></span>
                                </div>
                            </div>
                            
                            <!-- Right: Quantity Controls, Price and Delete -->
                            <div class="flex items-center gap-3 shrink-0">
                                <!-- Qty controls -->
                                <div class="flex items-center gap-1 bg-slate-100 dark:bg-zinc-950 border border-slate-200/50 dark:border-zinc-800 rounded-lg p-0.5 select-none">
                                    <button type="button" @click="decrementQty(index)" class="w-5 h-5 rounded-md flex items-center justify-center text-slate-500 hover:bg-white dark:hover:bg-zinc-800 hover:shadow-sm transition-all font-black text-xs cursor-pointer select-none">-</button>
                                    <span class="w-6 text-center text-[10px] font-black font-mono text-slate-850 dark:text-zinc-200" x-text="item.quantity"></span>
                                    <button type="button" @click="incrementQty(index)" class="w-5 h-5 rounded-md flex items-center justify-center text-slate-500 hover:bg-white dark:hover:bg-zinc-800 hover:shadow-sm transition-all font-black text-xs cursor-pointer select-none">+</button>
                                </div>
                                
                                <span class="font-mono font-black text-slate-900 dark:text-zinc-50 min-w-[55px] text-right" x-text="currencySymbol + (item.price * item.quantity).toFixed(2)"></span>
                                
                                <button type="button" @click="removeFromCart(index)" class="text-slate-400 hover:text-rose-600 transition-colors p-1 cursor-pointer" title="Remove Item">
                                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <!-- Till Configuration Widgets -->
                <div id="tour-step-config"
                     :class="{ 'ring-4 ring-indigo-500 scale-[1.01] shadow-mui-24 relative z-40 p-4 bg-white dark:bg-zinc-900 rounded-xl border border-indigo-200/20 transition-all duration-300': tourActive && tourStep === 4 }"
                     class="space-y-4">
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Currency</label>
                            <div class="relative">
                                <select name="currency" x-model="currency" @change="updateCurrencySymbol()" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg pl-8 pr-2 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white appearance-none cursor-pointer">
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                    <option value="INR">INR (₹)</option>
                                </select>
                                <div class="absolute left-2.5 top-2.5 text-slate-400 dark:text-zinc-550 text-xs pointer-events-none">
                                    <i class="fa-solid fa-earth-americas"></i>
                                </div>
                                <div class="absolute right-2.5 top-3 text-slate-400 text-[10px] pointer-events-none">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Shipping Cost</label>
                            <div class="relative">
                                <div class="absolute left-3 top-2 text-slate-400 dark:text-zinc-550 text-xs font-mono font-bold select-none" x-text="currencySymbol"></div>
                                <input type="number" step="0.01" name="shipping_cost" x-model.number="shippingCost" placeholder="0.00" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg pl-8 pr-2 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white font-mono" />
                            </div>
                        </div>
                    </div>

                    <!-- Global Tax & Discount Rules -->
                    <div class="grid grid-cols-2 gap-3.5 select-none">
                        <div>
                            <div class="flex justify-between text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-2">
                                <span>Default Tax</span>
                                <span class="font-black text-indigo-600 dark:text-indigo-400 font-mono" x-text="taxRate + '%'"></span>
                            </div>
                            <input type="range" min="0" max="30" name="tax_rate" x-model.number="taxRate" class="w-full appearance-none bg-slate-100 dark:bg-zinc-800 h-1 rounded [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:h-3 [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-indigo-500 [&::-webkit-slider-thumb]:cursor-pointer cursor-pointer" />
                        </div>
                        <div>
                            <div class="flex justify-between text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-2">
                                <span>Discount</span>
                                <span class="font-black text-rose-500 font-mono" x-text="discountRate + '%'"></span>
                            </div>
                            <input type="range" min="0" max="50" name="discount_rate" x-model.number="discountRate" class="w-full appearance-none bg-slate-100 dark:bg-zinc-800 h-1 rounded [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:h-3 [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-indigo-500 [&::-webkit-slider-thumb]:cursor-pointer cursor-pointer" />
                        </div>
                    </div>

                    <!-- Invoice Style Customizer -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Template Style</label>
                            <div class="relative">
                                <select name="template_id" x-model="templateId" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg pl-8 pr-2 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white appearance-none cursor-pointer">
                                    <option value="blueprint">Blueprint 3D</option>
                                    <option value="modern">Modern Glass</option>
                                    <option value="classic">Classic Editorial</option>
                                    <option value="creative">Creative Minimalist</option>
                                    <option value="slate">Slate Professional</option>
                                </select>
                                <div class="absolute left-2.5 top-2.5 text-slate-400 dark:text-zinc-550 text-xs pointer-events-none">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <div class="absolute right-2.5 top-3 text-slate-400 text-[10px] pointer-events-none">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Brand Color</label>
                            <div class="flex items-center gap-2.5 bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 rounded-lg px-3 py-1.5 h-[34px] transition-all hover:border-slate-350">
                                <input type="color" name="theme_color" x-model="themeColor" class="h-5 w-8 rounded border-0 bg-transparent cursor-pointer p-0 shrink-0" />
                                <span class="text-[10px] font-mono uppercase font-black text-slate-600 dark:text-zinc-450 select-none" x-text="themeColor"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Billing Fields -->
                <div id="tour-step-client"
                     :class="{ 'ring-4 ring-indigo-500 scale-[1.01] shadow-mui-24 relative z-40 p-4 bg-white dark:bg-zinc-900 rounded-xl border border-indigo-200/20 transition-all duration-300': tourActive && tourStep === 5 }"
                     class="border-t border-slate-100 dark:border-zinc-800/60 pt-4 flex flex-col gap-3">
                    <span class="text-[10px] font-black text-slate-400 dark:text-zinc-550 uppercase tracking-widest select-none mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-address-card text-indigo-500"></i> Client Billing Details
                    </span>
                    
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Customer / Client Name *</label>
                        <div class="relative">
                            <input type="text" name="client_name" required placeholder="John Doe" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg pl-9 pr-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white transition-all hover:border-slate-300 dark:hover:border-zinc-700" />
                            <div class="absolute left-3 top-2.5 text-slate-400 dark:text-zinc-550 text-xs">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Client Email</label>
                            <div class="relative">
                                <input type="email" name="client_email" placeholder="john@client.com" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg pl-9 pr-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white transition-all hover:border-slate-300 dark:hover:border-zinc-700" />
                                <div class="absolute left-3 top-2.5 text-slate-400 dark:text-zinc-550 text-xs">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Client Phone</label>
                            <div class="relative">
                                <input type="text" name="client_phone" placeholder="+1 (555) 123-4567" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg pl-9 pr-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white transition-all hover:border-slate-300 dark:hover:border-zinc-700" />
                                <div class="absolute left-3 top-2.5 text-slate-400 dark:text-zinc-550 text-xs">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Company Name</label>
                            <div class="relative">
                                <input type="text" name="client_company" placeholder="Acme Corporation" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg pl-9 pr-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white transition-all hover:border-slate-300 dark:hover:border-zinc-700" />
                                <div class="absolute left-3 top-2.5 text-slate-400 dark:text-zinc-550 text-xs">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">VAT / Tax ID</label>
                            <div class="relative">
                                <input type="text" name="client_vat" placeholder="US123456789" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg pl-9 pr-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white transition-all hover:border-slate-300 dark:hover:border-zinc-700" />
                                <div class="absolute left-3 top-2.5 text-slate-400 dark:text-zinc-550 text-xs">
                                    <i class="fa-solid fa-id-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1.5">Billing Address</label>
                        <div class="relative">
                            <textarea name="client_address" rows="2" placeholder="123 Client St, City, Country" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg pl-9 pr-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white resize-none transition-all hover:border-slate-300 dark:hover:border-zinc-700"></textarea>
                            <div class="absolute left-3 top-3.5 text-slate-400 dark:text-zinc-550 text-xs pointer-events-none">
                                <i class="fa-solid fa-map-pin"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cashier Bill Summary Drawer -->
                <div class="border-t border-slate-100 dark:border-zinc-800/60 pt-4 flex flex-col gap-2 select-none">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500 font-medium">Subtotal:</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-zinc-100" x-text="currencySymbol + getSubtotal().toFixed(2)"></span>
                    </div>
                    <div x-show="discountRate > 0" class="flex justify-between text-xs" x-cloak>
                        <span class="text-rose-500 font-medium" x-text="'Discount (' + discountRate + '%):'"></span>
                        <span class="font-mono font-bold text-rose-500" x-text="'-' + currencySymbol + getDiscountAmount().toFixed(2)"></span>
                    </div>
                    <div x-show="getTaxAmount() > 0" class="flex justify-between text-xs" x-cloak>
                        <span class="text-emerald-500 font-medium">Computed Tax:</span>
                        <span class="font-mono font-bold text-emerald-500" x-text="'+' + currencySymbol + getTaxAmount().toFixed(2)"></span>
                    </div>
                    <div x-show="shippingCost > 0" class="flex justify-between text-xs" x-cloak>
                        <span class="text-slate-550 dark:text-zinc-400 font-medium">Shipping & Handling:</span>
                        <span class="font-mono font-bold text-slate-700 dark:text-zinc-300" x-text="'+' + currencySymbol + parseFloat(shippingCost || 0).toFixed(2)"></span>
                    </div>
                    
                    <div class="flex justify-between border-t border-slate-200 dark:border-zinc-800/80 pt-3 mt-1.5">
                        <span class="text-xs font-black text-slate-900 dark:text-zinc-50 uppercase tracking-widest">Grand Total</span>
                        <span class="text-lg font-black text-indigo-600 dark:text-indigo-400 font-mono tracking-tight" x-text="currencySymbol + getTotal().toFixed(2)"></span>
                    </div>
                </div>

                <!-- POS Checkout Submit Trigger -->
                <button id="tour-step-checkout"
                        type="submit" 
                        ::disabled="cart.length === 0"
                        :class="{ 
                            'opacity-50 cursor-not-allowed bg-slate-350 text-slate-500': cart.length === 0,
                            'bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white shadow-md hover:shadow-lg active:scale-[0.98] cursor-pointer': cart.length > 0,
                            'ring-4 ring-indigo-500 scale-[1.01] shadow-mui-24 relative z-40 transition-all duration-300': tourActive && tourStep === 6 
                        }"
                        class="shine-button w-full inline-flex items-center justify-center gap-2 py-3 rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all duration-300">
                    <i class="fa-solid fa-circle-check text-sm animate-pulse"></i>
                    <span>Checkout & Print Invoice</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Dark Backdrop for Tour -->
    <div x-show="tourActive" 
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-[1.5px] z-40 transition-all duration-300 no-print"
         @click="completeTour()"
         x-cloak></div>

    <!-- Interactive Tour Popover Card -->
    <div x-show="tourActive" 
         :style="getTourStyle()" 
         x-transition
         class="bg-slate-900 dark:bg-zinc-900 border border-slate-700/50 dark:border-zinc-800 rounded-2xl shadow-mui-24 p-5 flex flex-col gap-4 text-white z-50 no-print font-sans"
         x-cloak>
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-2 select-none">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles text-indigo-400 text-xs"></i>
                <span class="text-[9px] font-black uppercase tracking-widest text-indigo-400" x-text="'Step ' + tourStep + ' of 6'"></span>
            </div>
            <button type="button" @click="completeTour()" class="text-slate-400 hover:text-white transition-colors cursor-pointer text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Content -->
        <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-100" 
                x-text="tourStep === 1 ? 'Product Search & Filter' : 
                        (tourStep === 2 ? 'Catalog Products & Attributes' : 
                        (tourStep === 3 ? 'Cashier Cart Receipt' : 
                        (tourStep === 4 ? 'Configurations & Styling' : 
                        (tourStep === 5 ? 'Client Billing Details' : 'POS Checkout Billing'))))"></h4>
            <p class="text-[10px] text-slate-350 leading-relaxed font-medium mt-1"
               x-text="tourStep === 1 ? 'Find products instantly by SKU code or item name. Type in the search box to filter presets catalog.' : 
                       (tourStep === 2 ? 'Hover over item cards to review custom attributes (like Color or Warranty size) and click the + button to add products to the checkout drawer.' : 
                       (tourStep === 3 ? 'Review items loaded into the register. Adjust individual quantity columns, delete rows, or inspect unit totals.' : 
                       (tourStep === 4 ? 'Toggle default billing currencies, adjust sliding tax/discount values, specify brand colors, and pick custom invoice templates.' : 
                       (tourStep === 5 ? 'Specify billing credentials including customer name, contact email, telephone, company VAT tax IDs, and mailing addresses.' : 
                        'Complete payment! Click this button to compile invoice inputs, record the database entry, and launch the print preview panel.')))"></p>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800 select-none">
            <button type="button" @click="completeTour()" class="text-[10px] font-bold text-slate-400 hover:text-white transition-colors cursor-pointer">
                Skip Tour
            </button>
            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="prevStep()" 
                        :disabled="tourStep === 1"
                        :class="tourStep === 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-white/10 cursor-pointer'"
                        class="px-2.5 py-1.5 border border-slate-700 rounded-lg text-[9px] font-black uppercase transition-all">
                    Back
                </button>
                <button type="button" 
                        @click="nextStep()" 
                        class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-[9px] font-black uppercase shadow-sm transition-all cursor-pointer"
                        x-text="tourStep === 6 ? 'Finish' : 'Next'"></button>
            </div>
        </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom premium scrollbar for cashier cart */
    #tour-step-cart::-webkit-scrollbar {
        width: 6px;
    }
    #tour-step-cart::-webkit-scrollbar-track {
        background: transparent;
    }
    #tour-step-cart::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.25);
        border-radius: 10px;
    }
    #tour-step-cart::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 0.45);
    }
</style>
@endpush
