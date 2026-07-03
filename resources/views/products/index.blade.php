@extends('invoices.layout')

@section('content')
<div x-data="{ 
    openModal: {{ $errors->any() ? 'true' : 'false' }}, 
    editMode: {{ old('productId') ? 'true' : 'false' }},
    productId: '{{ old('productId', '') }}',
    sku: '{{ old('sku', '') }}',
    name: '{{ old('name', '') }}',
    description: '{{ old('description', '') }}',
    price: '{{ old('price', '') }}',
    taxRate: '{{ old('tax_rate', '0.00') }}',
    imageUrl: '{{ old('image_url', '') }}',
    customFields: @json(old('custom_fields') ?: (object)[]),
    
    // ImgBB Sandbox applet state
    imgbbOpen: false,
    imgbbLoading: false,
    imgbbUploadedUrl: '',
    imgbbPreview: null,
    imgbbFile: null,
    imgbbCallback: null,
    imgbbDragOver: false,
    imgbbTab: 'upload',
    imgbbApiKey: '{{ auth()->user()->settings['imgbb_api_key'] ?? '06ffe6cc0bd7f71c1d8571040b8dd87a' }}',
    
    openImgbbModal(callback) {
        this.imgbbCallback = callback;
        this.imgbbUploadedUrl = '';
        this.imgbbPreview = null;
        this.imgbbFile = null;
        this.imgbbTab = 'upload';
        this.imgbbOpen = true;
    },
    
    handleImgbbFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.imgbbFile = file;
            this.imgbbPreview = URL.createObjectURL(file);
        }
    },
    
    handleImgbbDrop(event) {
        this.imgbbDragOver = false;
        const file = event.dataTransfer.files[0];
        if (file) {
            this.imgbbFile = file;
            this.imgbbPreview = URL.createObjectURL(file);
        }
    },
    
    simulateImgbbUpload() {
        if (!this.imgbbFile) return;
        this.imgbbLoading = true;
        
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);
                
                canvas.toBlob((blob) => {
                    if (!blob) {
                        this.imgbbLoading = false;
                        alert('Failed to convert image to WebP format.');
                        return;
                    }
                    
                    const formData = new FormData();
                    formData.append('image', blob, 'product.webp');
                    
                    fetch('{{ route('imgbb.upload') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('API response status not OK');
                        }
                        return res.json();
                    })
                    .then(data => {
                        this.imgbbLoading = false;
                        if (data && data.data && data.data.url) {
                            this.imgbbUploadedUrl = data.data.url;
                        } else {
                            alert('Upload failed: ' + (data.error ? data.error.message : 'Unknown response error'));
                        }
                    })
                    .catch(err => {
                        this.imgbbLoading = false;
                        alert('ImgBB API Upload error: ' + err.message);
                    });
                }, 'image/webp', 0.85);
            };
            img.onerror = () => {
                this.imgbbLoading = false;
                alert('Failed to load image file.');
            };
            img.src = e.target.result;
        };
        reader.onerror = () => {
            this.imgbbLoading = false;
            alert('Failed to read file.');
        };
        reader.readAsDataURL(this.imgbbFile);
    },
    
    selectImgbbTemplate(url) {
        this.imgbbUploadedUrl = url;
    },
    
    insertImgbbUrl() {
        if (this.imgbbCallback && this.imgbbUploadedUrl) {
            this.imgbbCallback(this.imgbbUploadedUrl);
        }
        this.imgbbOpen = false;
    },
    
    resetForm() {
        this.editMode = false;
        this.productId = '';
        this.sku = '';
        this.name = '';
        this.description = '';
        this.price = '';
        this.taxRate = '0.00';
        this.imageUrl = '';
        this.customFields = {};
    },
    
    editProduct(product) {
        this.editMode = true;
        this.productId = product.id;
        this.sku = product.sku || '';
        this.name = product.name;
        this.description = product.description || '';
        this.price = product.price;
        this.taxRate = product.tax_rate;
        this.imageUrl = product.image_url || '';
        this.customFields = product.custom_fields || {};
        this.openModal = true;
    }
}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">

    <!-- Header Block -->
    <div class="relative bg-slate-900 text-white rounded-2xl p-6 sm:p-8 shadow-mui-2 overflow-hidden mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border border-slate-800">
        <!-- Glows -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-[100px] -z-10"></div>
        
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Product Directory</h1>
            <p class="text-xs sm:text-sm text-indigo-250 opacity-90 mt-1 max-w-xl">Configure items, hourly rates, and standard tax brackets to quickly build invoices in your checkout Till.</p>
        </div>
        <div class="shrink-0">
            <button type="button" @click="resetForm(); openModal = true" class="shine-button w-full sm:w-auto inline-flex items-center justify-center gap-2 text-xs font-extrabold uppercase tracking-wider bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white px-5 py-3.5 rounded-xl shadow-mui-2 hover:shadow-mui-8 active:scale-95 transition-all cursor-pointer">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Add Product</span>
            </button>
        </div>
    </div>

    <!-- Product Directory Listing -->
    <div class="bg-white dark:bg-zinc-900 border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl shadow-sm overflow-hidden">
        @if($products->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="h-12 w-12 bg-slate-50 dark:bg-zinc-800/50 text-slate-400 dark:text-zinc-500 rounded-xl flex items-center justify-center mx-auto mb-3 border border-slate-200/50 dark:border-zinc-800/20">
                    <i class="fa-solid fa-box-open text-lg"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-zinc-200">No products configured</h3>
                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1 max-w-xs mx-auto">Configure billing packages, service rates, or items to automate invoice generation.</p>
                <button type="button" @click="resetForm(); openModal = true" class="shine-button inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white px-5 py-3.5 rounded-xl shadow-mui-2 hover:shadow-mui-8 active:scale-95 transition-all mt-5 cursor-pointer">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Configure First Product</span>
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-zinc-850/60 border-b border-slate-100 dark:border-zinc-800 text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider select-none">
                            <th class="p-4 pl-6">SKU</th>
                            <th class="p-4">Product Name</th>
                            <th class="p-4">Description</th>
                            <th class="p-4 text-right">Price</th>
                            <th class="p-4 text-right">Tax Rate</th>
                            <th class="p-4 pr-6 text-center w-28">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-slate-700 dark:text-zinc-300 divide-y divide-slate-100 dark:divide-zinc-800/65">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-850/30 transition-colors">
                                <td class="p-4 pl-6 font-mono font-bold text-slate-400 dark:text-zinc-500">{{ $product->sku ?: '—' }}</td>
                                <td class="p-4 font-bold text-slate-800 dark:text-zinc-100">
                                    <div class="flex items-center gap-2.5">
                                        @if(!empty($product->image_url))
                                            <img src="{{ $product->image_url }}" class="h-8 w-8 rounded-lg object-cover bg-slate-100 border border-slate-205 dark:border-zinc-800" />
                                        @else
                                            <div class="h-8 w-8 rounded-lg bg-slate-50 dark:bg-zinc-850 border border-slate-205 dark:border-zinc-800 flex items-center justify-center text-slate-400">
                                                <i class="fa-solid fa-box text-[10px]"></i>
                                            </div>
                                        @endif
                                        <span>{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-slate-550 dark:text-zinc-400 max-w-xs">
                                    <span class="block truncate" title="{{ $product->description }}">{{ $product->description ?: '—' }}</span>
                                    @if(!empty($product->custom_fields))
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($product->custom_fields as $key => $val)
                                                @if(!empty($val))
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400 border border-indigo-200/20 dark:border-indigo-900/10">
                                                        {{ $key }}: {{ $val }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4 text-right font-bold text-slate-900 dark:text-zinc-50 font-mono">${{ number_format($product->price, 2) }}</td>
                                <td class="p-4 text-right font-semibold text-slate-500 dark:text-zinc-400 font-mono">{{ number_format($product->tax_rate, 2) }}%</td>
                                <td class="p-4 pr-6 flex items-center justify-center gap-1.5 mt-1">
                                    <!-- Edit Trigger -->
                                    <button type="button" @click="editProduct({
                                        id: '{{ $product->id }}',
                                        sku: '{{ $product->sku }}',
                                        name: '{{ addslashes($product->name) }}',
                                        description: '{{ addslashes($product->description) }}',
                                        price: '{{ $product->price }}',
                                        tax_rate: '{{ $product->tax_rate }}',
                                        image_url: '{{ $product->image_url }}',
                                        custom_fields: {!! json_encode($product->custom_fields ?: new stdClass) !!}
                                    })" class="h-7 w-7 rounded-lg border border-slate-200 dark:border-zinc-800 text-slate-500 hover:text-amber-600 dark:text-zinc-400 dark:hover:text-amber-400 flex items-center justify-center bg-slate-50 dark:bg-zinc-850 hover:bg-white dark:hover:bg-zinc-800 cursor-pointer transition-all shadow-sm">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Remove this product?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-7 w-7 rounded-lg border border-slate-200 dark:border-zinc-800 text-slate-400 hover:text-rose-600 dark:text-zinc-500 dark:hover:text-rose-400 flex items-center justify-center bg-slate-50 dark:bg-zinc-850 hover:bg-white dark:hover:bg-zinc-800 cursor-pointer transition-all shadow-sm">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Product Configuration Modal (Add / Edit) -->
    <div x-show="openModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition
         x-cloak>
        
        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl w-full max-w-md shadow-mui-24 overflow-hidden relative"
             @click.away="if (!imgbbOpen) openModal = false">
            
            <div class="p-5 border-b border-slate-105 dark:border-zinc-850 flex items-center justify-between bg-slate-50/50 dark:bg-zinc-850/20">
                <h3 class="text-xs font-black text-slate-800 dark:text-zinc-50 uppercase tracking-widest" x-text="editMode ? 'Edit Product details' : 'Configure New Product'"></h3>
                <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 text-sm cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="editMode ? '/products/' + productId : '{{ route('products.store') }}'" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="productId" :value="productId">
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                @if ($errors->any())
                    <div class="p-3.5 bg-rose-50 dark:bg-rose-950/20 border border-rose-250/30 dark:border-rose-900/30 rounded-xl">
                        <span class="block text-[10px] font-black text-rose-700 dark:text-rose-455 uppercase tracking-wider mb-1">Validation Errors Found:</span>
                        <ul class="list-disc list-inside text-[10px] text-rose-600 dark:text-rose-400 space-y-0.5 leading-normal font-semibold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- SKU & Name -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-1">
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">SKU / Code</label>
                        <input type="text" name="sku" x-model="sku" placeholder="PROD-01" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white" />
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Product Name *</label>
                        <input type="text" name="name" x-model="name" required placeholder="Consultation Fee" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white" />
                    </div>
                </div>

                <!-- Price & Tax Rate -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Unit Price ($) *</label>
                        <input type="number" step="0.01" name="price" x-model="price" required placeholder="120.00" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white font-mono" />
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tax Rate (%) *</label>
                        <input type="number" step="0.01" name="tax_rate" x-model="taxRate" required placeholder="0.00" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white font-mono" />
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Description</label>
                    <textarea name="description" x-model="description" rows="3" placeholder="Additional product notes or client details..." class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white resize-none"></textarea>
                </div>

                <!-- Image URL -->
                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Product Image URL</label>
                    <div class="flex items-center gap-2">
                        <input type="url" name="image_url" x-model="imageUrl" placeholder="https://example.com/product.jpg" class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white" />
                        <button type="button" @click="openImgbbModal(url => imageUrl = url)" class="px-2.5 py-2.5 bg-slate-150 hover:bg-slate-200 dark:bg-zinc-800 dark:hover:bg-zinc-750 text-slate-700 dark:text-zinc-200 rounded-lg border border-slate-205 dark:border-zinc-700 text-xs font-bold shrink-0 cursor-pointer" title="Host Image on ImgBB Sandbox">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </button>
                    </div>
                </div>

                <!-- Dynamic Custom Fields (Managed in Account Settings) -->
                @if(auth()->check() && !empty(auth()->user()->settings['product_custom_fields']))
                    <div class="border-t border-slate-100 dark:border-zinc-800/60 pt-3 mt-1.5 space-y-3">
                        <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 select-none">Dynamic Product Fields</span>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(auth()->user()->settings['product_custom_fields'] as $field)
                                @php
                                    // Coerce legacy format strings to object definitions
                                    if (is_string($field)) {
                                        $field = ['name' => $field, 'type' => 'text', 'required' => false, 'tip' => ''];
                                    }
                                    $fieldName = $field['name'] ?? '';
                                    $fieldType = $field['type'] ?? 'text';
                                    $fieldRequired = !empty($field['required']) && $field['required'] !== 'false';
                                    $fieldTip = $field['tip'] ?? '';
                                    $fieldMin = $field['min'] ?? null;
                                    $fieldMax = $field['max'] ?? null;
                                @endphp
                                @if(!empty($fieldName))
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                                            {{ $fieldName }} {!! $fieldRequired ? '<span class="text-rose-500">*</span>' : '' !!}
                                        </label>
                                        <input type="{{ $fieldType }}" 
                                               name="custom_fields[{{ $fieldName }}]" 
                                               :value="editMode ? customFields['{{ $fieldName }}'] || '' : ''" 
                                               placeholder="{{ $fieldTip ?: 'Enter ' . strtolower($fieldName) . '...' }}"
                                               @if($fieldRequired) required @endif
                                               @if($fieldType === 'number')
                                                   @if(isset($fieldMin) && $fieldMin !== '') min="{{ $fieldMin }}" @endif
                                                   @if(isset($fieldMax) && $fieldMax !== '') max="{{ $fieldMax }}" @endif
                                               @else
                                                   @if(isset($fieldMin) && $fieldMin !== '') minlength="{{ $fieldMin }}" @endif
                                                   @if(isset($fieldMax) && $fieldMax !== '') maxlength="{{ $fieldMax }}" @endif
                                               @endif
                                               class="w-full bg-slate-50 dark:bg-zinc-950/40 border border-slate-200 dark:border-zinc-800 focus:border-indigo-500 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:text-white" />
                                               
                                        @if(!empty($fieldTip))
                                            <p class="text-[9px] text-slate-400 dark:text-zinc-550 mt-1 select-none font-medium">
                                                <i class="fa-solid fa-circle-info mr-0.5"></i> {{ $fieldTip }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-slate-105 dark:border-zinc-850 flex gap-2 justify-end">
                    <button type="button" @click="openModal = false" class="px-4 py-2 border border-slate-200 dark:border-zinc-800 text-slate-600 dark:text-zinc-300 text-xs font-bold rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all cursor-pointer" x-text="editMode ? 'Save Changes' : 'Create Product'"></button>
                </div>
            </form>
    </div>

    <!-- ImgBB Image Hosting Sandbox Modal (Applet) -->
    <div x-show="imgbbOpen" 
         class="fixed inset-0 z-[60] overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         @click.stop
         x-transition
         x-cloak>
        
        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-2xl w-full max-w-lg shadow-mui-24 overflow-hidden relative"
             @click.away="imgbbOpen = false">
            
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-105 dark:border-zinc-850 flex items-center justify-between bg-slate-50/50 dark:bg-zinc-850/20">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 flex items-center justify-center">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-slate-800 dark:text-zinc-50 uppercase tracking-widest">ImgBB Sandbox Applet</h3>
                        <p class="text-[9px] text-slate-400 dark:text-zinc-500 font-bold uppercase mt-0.5 tracking-wider">Free Image Hosting Console</p>
                    </div>
                </div>
                <button type="button" @click="imgbbOpen = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 flex flex-col gap-6">
                
                <!-- Tab Selector Navigation -->
                <div class="flex border-b border-slate-100 dark:border-zinc-800/80 pb-px">
                    <button type="button" 
                            @click="imgbbTab = 'upload'"
                            :class="imgbbTab === 'upload' ? 'border-indigo-600 text-indigo-650 dark:text-indigo-400' : 'border-transparent text-slate-400 hover:text-slate-600'"
                            class="flex-1 pb-3 text-xs font-black uppercase tracking-wider border-b-2 transition-all cursor-pointer">
                        Upload Image
                    </button>
                    <button type="button" 
                            @click="imgbbTab = 'presets'"
                            :class="imgbbTab === 'presets' ? 'border-indigo-600 text-indigo-650 dark:text-indigo-400' : 'border-transparent text-slate-400 hover:text-slate-600'"
                            class="flex-1 pb-3 text-xs font-black uppercase tracking-wider border-b-2 transition-all cursor-pointer">
                        Preset Gallery
                    </button>
                </div>

                <!-- TAB 1: UPLOAD SIMULATOR -->
                <div x-show="imgbbTab === 'upload'" class="flex flex-col gap-4">
                    <!-- Drop area -->
                    <div @dragover.prevent="imgbbDragOver = true"
                         @dragleave.prevent="imgbbDragOver = false"
                         @drop.prevent="handleImgbbDrop($event)"
                         :class="imgbbDragOver ? 'border-indigo-500 bg-indigo-50/10' : 'border-slate-200 dark:border-zinc-800 bg-slate-50/30 dark:bg-zinc-950/20'"
                         class="border-2 border-dashed rounded-xl p-8 text-center flex flex-col items-center justify-center gap-3 relative transition-all min-h-[160px]">
                        
                        <input type="file" id="imgbb-file-input" @change="handleImgbbFileSelect($event)" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" />
                        
                        <template x-if="!imgbbPreview">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fa-solid fa-images text-2xl text-slate-400 dark:text-zinc-655 animate-bounce"></i>
                                <span class="text-xs font-bold text-slate-755 dark:text-zinc-300">Drag and drop file here, or click to browse</span>
                                <span class="text-[9px] text-slate-400 dark:text-zinc-550 uppercase tracking-widest font-black">Supports PNG, JPG, GIF</span>
                            </div>
                        </template>

                        <template x-if="imgbbPreview">
                            <div class="flex flex-col items-center gap-3 w-full">
                                <img :src="imgbbPreview" class="h-24 w-24 object-cover rounded-lg border border-slate-205 dark:border-zinc-800 shadow-md bg-white" />
                                <span class="text-[10px] text-slate-400 dark:text-zinc-550 font-bold truncate max-w-xs">File loaded successfully</span>
                            </div>
                        </template>
                    </div>

                    <!-- Upload Action Button -->
                    <template x-if="imgbbPreview && !imgbbUploadedUrl">
                        <button type="button" 
                                @click="simulateImgbbUpload()"
                                :disabled="imgbbLoading"
                                class="shine-button w-full inline-flex items-center justify-center gap-2 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-xs font-extrabold uppercase tracking-wider shadow-sm hover:shadow-md transition-all cursor-pointer">
                            <template x-if="imgbbLoading">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-spinner animate-spin"></i> Hosting on ImgBB...
                                </span>
                            </template>
                            <template x-if="!imgbbLoading">
                                <span>Upload to ImgBB Sandbox</span>
                            </template>
                        </button>
                    </template>
                </div>

                <!-- TAB 2: READY-MADE PRESETS -->
                <div x-show="imgbbTab === 'presets'" class="flex flex-col gap-4">
                    <span class="text-[9px] font-black text-slate-400 dark:text-zinc-550 uppercase tracking-wider select-none">Select a pre-hosted product mock:</span>
                    <div class="grid grid-cols-3 gap-3">
                        <div @click="selectImgbbTemplate('https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&auto=format&fit=crop')"
                             class="flex flex-col items-center gap-1.5 p-2 bg-slate-50 dark:bg-zinc-955/20 border border-slate-200 dark:border-zinc-800 rounded-xl hover:border-indigo-500 cursor-pointer group transition-all select-none">
                            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=120&auto=format&fit=crop" class="h-14 w-full object-cover rounded-lg shadow-sm" />
                            <span class="text-[9px] font-bold text-slate-500 dark:text-zinc-400 group-hover:text-indigo-500 transition-colors">Consulting</span>
                        </div>
                        <div @click="selectImgbbTemplate('https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400&auto=format&fit=crop')"
                             class="flex flex-col items-center gap-1.5 p-2 bg-slate-50 dark:bg-zinc-955/20 border border-slate-200 dark:border-zinc-800 rounded-xl hover:border-indigo-500 cursor-pointer group transition-all select-none">
                            <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=120&auto=format&fit=crop" class="h-14 w-full object-cover rounded-lg shadow-sm" />
                            <span class="text-[9px] font-bold text-slate-500 dark:text-zinc-400 group-hover:text-indigo-500 transition-colors">Software</span>
                        </div>
                        <div @click="selectImgbbTemplate('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=400&auto=format&fit=crop')"
                             class="flex flex-col items-center gap-1.5 p-2 bg-slate-50 dark:bg-zinc-955/20 border border-slate-200 dark:border-zinc-800 rounded-xl hover:border-indigo-500 cursor-pointer group transition-all select-none">
                            <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=120&auto=format&fit=crop" class="h-14 w-full object-cover rounded-lg shadow-sm" />
                            <span class="text-[9px] font-bold text-slate-500 dark:text-zinc-400 group-hover:text-indigo-500 transition-colors">Hosting</span>
                        </div>
                    </div>
                </div>

                <!-- Result & Insert URL Actions -->
                <div x-show="imgbbUploadedUrl" 
                     class="border-t border-slate-100 dark:border-zinc-800/60 pt-5 flex flex-col gap-4"
                     x-transition
                     x-cloak>
                    
                    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 flex flex-col gap-2">
                        <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 text-xs font-extrabold select-none">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>CDN Hosting Successful!</span>
                        </div>
                        <div class="relative mt-1">
                            <input type="text" 
                                   readonly 
                                   :value="imgbbUploadedUrl" 
                                   class="w-full bg-slate-50 dark:bg-zinc-950 border border-slate-200 dark:border-zinc-800 rounded-lg pl-3 pr-10 py-2 text-[10px] font-mono text-slate-600 dark:text-zinc-400 focus:outline-none" />
                            <button type="button" 
                                    @click="navigator.clipboard.writeText(imgbbUploadedUrl); alert('URL copied to clipboard!')"
                                    class="absolute right-2 top-1.5 text-slate-400 hover:text-slate-655 cursor-pointer p-1"
                                    title="Copy URL">
                                <i class="fa-solid fa-copy text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-2 justify-end">
                        <button type="button" 
                                @click="imgbbUploadedUrl = ''" 
                                class="px-4 py-2 border border-slate-200 dark:border-zinc-800 text-slate-600 dark:text-zinc-300 text-xs font-bold rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all cursor-pointer">
                            Reset
                        </button>
                        <button type="button" 
                                @click="insertImgbbUrl()" 
                                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all cursor-pointer">
                            Insert & Apply URL
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
