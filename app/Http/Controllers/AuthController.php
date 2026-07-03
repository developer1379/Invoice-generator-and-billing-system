<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View
    {
        if (Auth::check()) {
            return redirect()->route('invoices.index');
        }

        return view('auth.login');
    }

    /**
     * Handle authentication attempts.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $guestInvoices = $request->session()->get('guest_invoices', []);
            $request->session()->regenerate();
            $request->session()->put('guest_invoices', $guestInvoices);

            $this->claimGuestInvoices($request);

            return redirect()->intended(route('invoices.index'))->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegister(): View
    {
        if (Auth::check()) {
            return redirect()->route('invoices.index');
        }

        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new \Illuminate\Auth\Events\Registered($user));

        Auth::login($user);

        $this->claimGuestInvoices($request);

        return redirect()->route('invoices.index')->with('success', 'Account created successfully! Please verify your email address.');
    }

    /**
     * Claim guest invoices for the newly authenticated user.
     */
    private function claimGuestInvoices(Request $request): void
    {
        $guestInvoiceIds = $request->session()->get('guest_invoices', []);
        if (! empty($guestInvoiceIds)) {
            \App\Models\Invoice::whereIn('id', $guestInvoiceIds)
                ->whereNull('user_id')
                ->update(['user_id' => Auth::id()]);

            $request->session()->forget('guest_invoices');
        }
    }

    /**
     * Terminate user session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully!');
    }

    /**
     * Redirect the user to the Google OAuth authorization page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        $query = http_build_query([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => csrf_token(),
        ]);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    /**
     * Handle the Google OAuth redirect callback.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $code = $request->query('code');

        if (empty($code)) {
            return redirect()->route('login')->withErrors(['email' => 'Google authorization code missing or expired.']);
        }

        // 1. Exchange auth code for access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
            'grant_type' => 'authorization_code',
        ]);

        if ($response->failed()) {
            return redirect()->route('login')->withErrors(['email' => 'Failed to obtain access token from Google: ' . $response->body()]);
        }

        $tokens = $response->json();
        $accessToken = $tokens['access_token'] ?? null;

        if (empty($accessToken)) {
            return redirect()->route('login')->withErrors(['email' => 'Access token not found in Google OAuth response.']);
        }

        // 2. Fetch user profile from Google UserInfo endpoint
        $userProfileResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if ($userProfileResponse->failed()) {
            return redirect()->route('login')->withErrors(['email' => 'Failed to retrieve profile info from Google.']);
        }

        $profile = $userProfileResponse->json();
        $email = $profile['email'] ?? null;
        $name = $profile['name'] ?? null;
        $sub = $profile['sub'] ?? null;

        if (empty($email)) {
            return redirect()->route('login')->withErrors(['email' => 'Google profile did not return an email address.']);
        }

        // 3. Find or create the user
        $user = User::where('google_id', $sub)
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            if (empty($user->google_id)) {
                $user->update(['google_id' => $sub]);
            }
            if (empty($user->email_verified_at)) {
                $user->markEmailAsVerified();
            }
        } else {
            $user = User::create([
                'name' => $name ?: 'Google User',
                'email' => $email,
                'google_id' => $sub,
                'password' => Hash::make(Str::random(24)),
            ]);
            $user->markEmailAsVerified();
        }

        // 4. Log the user in
        Auth::login($user, true);

        // 5. Preserved Guest invoice claiming logic!
        $guestInvoices = $request->session()->get('guest_invoices', []);
        $request->session()->regenerate();
        $request->session()->put('guest_invoices', $guestInvoices);
        $this->claimGuestInvoices($request);

        return redirect()->route('invoices.index')->with('success', 'Logged in via Google successfully!');
    }

    /**
     * Display the email verification notice.
     */
    public function showVerificationNotice(Request $request): View|RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->route('invoices.index')
            : view('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('invoices.index')->with('success', 'Email verified successfully!');
    }

    /**
     * Resend the email verification notification.
     */
    public function resendVerificationEmail(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('invoices.index');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent!');
    }
}
