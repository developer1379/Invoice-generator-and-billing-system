<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(): View
    {
        $products = Product::where('user_id', Auth::id())->latest()->get();

        return view('products.index', compact('products'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request);

        $data['user_id'] = Auth::id();

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $data = $this->validateProduct($request);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Helper to dynamically validate custom product fields using current settings schema definition.
     */
    private function validateProduct(Request $request): array
    {
        $customFieldsSettings = Auth::user()->settings['product_custom_fields'] ?? [];
        $customRules = [];

        foreach ($customFieldsSettings as $field) {
            if (!is_array($field) || empty($field['name'])) {
                continue;
            }

            $rules = [];
            $rules[] = (!empty($field['required']) && $field['required'] !== 'false') ? 'required' : 'nullable';

            $type = $field['type'] ?? 'text';
            if ($type === 'number') {
                $rules[] = 'numeric';
                if (isset($field['min']) && $field['min'] !== '') {
                    $rules[] = 'min:' . $field['min'];
                }
                if (isset($field['max']) && $field['max'] !== '') {
                    $rules[] = 'max:' . $field['max'];
                }
            } elseif ($type === 'url') {
                $rules[] = 'url';
            } elseif ($type === 'email') {
                $rules[] = 'email';
            } else {
                $rules[] = 'string';
                if (isset($field['min']) && $field['min'] !== '') {
                    $rules[] = 'min:' . $field['min'];
                }
                if (isset($field['max']) && $field['max'] !== '') {
                    $rules[] = 'max:' . $field['max'];
                }
            }
            $customRules['custom_fields.' . $field['name']] = $rules;
        }

        return $request->validate(array_merge([
            'sku' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'custom_fields' => ['nullable', 'array'],
        ], $customRules));
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }

    /**
     * Upload an image to ImgBB securely from the backend to hide active API keys.
     */
    public function uploadImgbb(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:10240'], // Max 10MB
        ]);

        $user = Auth::user();
        $apiKey = !empty($user->settings['imgbb_api_key']) ? $user->settings['imgbb_api_key'] : env('IMGBB_API_KEY');

        if (empty($apiKey)) {
            return response()->json([
                'error' => [
                    'message' => 'ImgBB API key is not configured in settings or environment.'
                ]
            ], 422);
        }

        $file = $request->file('image');

        try {
            $response = Http::asMultipart()
                ->attach('image', file_get_contents($file), $file->getClientOriginalName() ?: 'image.webp')
                ->post('https://api.imgbb.com/1/upload?key=' . $apiKey);

            if ($response->failed()) {
                return response()->json([
                    'error' => [
                        'message' => 'ImgBB upload rejected: ' . $response->body()
                    ]
                ], 400);
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'message' => 'Backend Proxy Upload error: ' . $e->getMessage()
                ]
            ], 500);
        }
    }
}
