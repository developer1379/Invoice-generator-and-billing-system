<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show user profile and business settings.
     */
    public function edit(): View
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile details and business defaults.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'business_name' => ['nullable', 'string', 'max:255'],
            'business_email' => ['nullable', 'email', 'max:255'],
            'business_phone' => ['nullable', 'string', 'max:50'],
            'business_address' => ['nullable', 'string'],
            'business_tax_id' => ['nullable', 'string', 'max:100'],
            'business_website' => ['nullable', 'string', 'max:255'],
            'business_logo' => ['nullable', 'string'],
            'business_signature' => ['nullable', 'string'],
            'business_currency' => ['nullable', 'string', 'max:10'],
            'business_tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'business_discount_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'business_notes' => ['nullable', 'string'],
            'business_terms' => ['nullable', 'string'],
            'product_custom_fields' => ['nullable', 'string'],
            'product_custom_fields_json' => ['nullable', 'string'],
            'imgbb_api_key' => ['nullable', 'string', 'max:100'],
        ]);

        // If password is empty, don't update it
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Process dynamic product custom fields definitions
        $settings = $user->settings ?? [];
        if ($request->has('product_custom_fields_json')) {
            $fields = json_decode($request->input('product_custom_fields_json'), true);
            $settings['product_custom_fields'] = array_values(array_filter($fields ?: [], function ($f) {
                return is_array($f) && !empty($f['name']);
            }));
        } elseif ($request->has('product_custom_fields')) {
            $fields = array_filter(array_map('trim', explode(',', $request->input('product_custom_fields'))));
            $settings['product_custom_fields'] = array_values($fields);
        }

        if ($request->has('imgbb_api_key')) {
            $settings['imgbb_api_key'] = $request->input('imgbb_api_key') ?: '';
        }
        
        $validated['settings'] = $settings;

        $user->update($validated);

        return redirect()->route('profile.edit')
            ->with('success', 'Profile and settings updated successfully!');
    }
}
