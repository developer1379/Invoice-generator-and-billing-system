@extends('invoices.layout')

@section('title', 'Privacy Policy — Invoicify')

@push('styles')
<style>
    html {
        scroll-behavior: smooth;
        scroll-padding-top: 5rem;
    }
    .sidebar-link.active {
        color: #6366f1 !important;
        border-color: #6366f1 !important;
        background-color: rgba(99, 102, 241, 0.05);
    }
    .dark .sidebar-link.active {
        color: #818cf8 !important;
        border-color: #818cf8 !important;
        background-color: rgba(129, 140, 248, 0.1);
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative select-none">
    
    <!-- Header Block -->
    <div class="mb-10 text-center md:text-left">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 mb-3">
            <i class="fa-solid fa-shield-halved"></i> Security & Trust
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">
            Privacy Policy
        </h1>
        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-2">
            Last Updated: July 6, 2026 • Effective Immediately
        </p>
    </div>

    <!-- Main Container -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Sidebar Navigation (3 cols) -->
        <nav class="lg:col-span-3 sticky top-20 hidden lg:block bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-5 shadow-sm">
            <h3 class="text-xs font-black text-slate-400 dark:text-zinc-500 uppercase tracking-widest mb-4">On This Page</h3>
            <div class="space-y-1">
                <a href="#introduction" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    1. Introduction
                </a>
                <a href="#data-collected" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    2. Information We Collect
                </a>
                <a href="#how-we-use" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    3. How We Use Information
                </a>
                <a href="#data-storage" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    4. Storage & Security
                </a>
                <a href="#cookies" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    5. Cookies & Tracking
                </a>
                <a href="#your-rights" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    6. Your Rights & Choice
                </a>
                <a href="#contact" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    7. Contact Us
                </a>
            </div>
        </nav>

        <!-- Main Content Body (9 cols) -->
        <div class="lg:col-span-9 space-y-6">
            
            <!-- Section 1: Introduction -->
            <section id="introduction" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-circle-info text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">1. Introduction</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        Welcome to Invoicify. We respect your privacy and are committed to protecting the personal data that you share with us. This Privacy Policy describes how we collect, use, store, and safeguard your details when you use our invoicing maker, point of sale (POS) till interface, and dashboard services.
                    </p>
                    <p>
                        By accessing or using our application, you agree to the collection and use of information in accordance with this policy. If you do not agree with any terms of this policy, please do not use the service.
                    </p>
                </div>
            </section>

            <!-- Section 2: Information We Collect -->
            <section id="data-collected" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-blue-50 dark:bg-blue-955/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-database text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">2. Information We Collect</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-4 leading-relaxed">
                    <p>We collect information you directly provide to us and details collected automatically during your visits:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>
                            <strong class="text-slate-700 dark:text-zinc-200">Account Credentials:</strong> When you register an account, we collect your name, email address, password, and Google profile details if you sign up using Google OAuth.
                        </li>
                        <li>
                            <strong class="text-slate-700 dark:text-zinc-200">Invoicing & Business Details:</strong> Any text fields, logo images, client data, and signature drawings you upload or input to draft invoices, till logs, or products.
                        </li>
                        <li>
                            <strong class="text-slate-700 dark:text-zinc-200">Usage Data:</strong> Information about how you interact with our pages, including clicked links, access times, and diagnostic reports.
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Section 3: How We Use Information -->
            <section id="how-we-use" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-emerald-50 dark:bg-emerald-955/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-chart-line text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">3. How We Use Information</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-4 leading-relaxed">
                    <p>We use the collected information for various purposes to deliver a premium user experience:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>To provide, maintain, and support our interactive invoicing builder, POS Till, and dashboard.</li>
                        <li>To personalize your experience, including saving your business default details, logo images, and signature graphics.</li>
                        <li>To process registration, authenticate sessions, and deliver email verification notices.</li>
                        <li>To monitor, analyze, and optimize application performance and prevent math or calculations latency.</li>
                        <li>To communicate important changes to our terms, software updates, or account operations.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 4: Storage & Security -->
            <section id="data-storage" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-purple-50 dark:bg-purple-955/40 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">4. Storage & Security</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        The security of your data is a top priority for us. Your billing logs, invoices, and profile variables are stored securely within our databases. Access is locked down and restricted to your authenticated user identifier.
                    </p>
                    <p>
                        We use industry-standard encryption, SSL protocols, and secure OAuth flows. However, please remember that no method of transmission over the Internet, or method of electronic storage, is 100% secure. While we strive to use commercially acceptable means to protect your personal data, we cannot guarantee its absolute security.
                    </p>
                </div>
            </section>

            <!-- Section 5: Cookies & Tracking -->
            <section id="cookies" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-amber-50 dark:bg-amber-955/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-cookie-bite text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">5. Cookies & Tracking</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        Invoicify uses cookies and similar session tracking mechanisms to remember your light/dark mode preference, manage your authenticated session state, and avoid CSRF (Cross-Site Request Forgery) security threats.
                    </p>
                    <p>
                        You can configure your browser to refuse all cookies or to alert you when a cookie is being sent. However, if you choose not to accept cookies, certain interactive areas of our site (such as remaining logged in) may not function correctly.
                    </p>
                </div>
            </section>

            <!-- Section 6: Your Rights & Choice -->
            <section id="your-rights" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-rose-50 dark:bg-rose-955/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-user-gear text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">6. Your Rights & Choice</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        We believe you should have control over your data. Depending on your location, you may have the right to request access to, correction of, or erasure of your personal data collected by our application.
                    </p>
                    <p>
                        You can update your personal profile, email settings, and billing parameters directly through the <a href="{{ route('profile.edit') }}" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Settings Dashboard</a>. If you wish to delete your account and all associated invoice logs permanently, please contact our support team.
                    </p>
                </div>
            </section>

            <!-- Section 7: Contact Us -->
            <section id="contact" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-slate-50 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-envelope text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">7. Contact Us</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        If you have any questions or suggestions regarding our Privacy Policy, please do not hesitate to contact us at:
                    </p>
                    <div class="p-4 bg-slate-50 dark:bg-zinc-850/60 border border-slate-100 dark:border-zinc-800 rounded-xl inline-flex flex-col gap-1.5 font-bold">
                        <span class="text-slate-800 dark:text-zinc-200"><i class="fa-solid fa-envelope text-indigo-500 mr-2"></i> privacy@invoicify.test</span>
                        <span class="text-slate-800 dark:text-zinc-200"><i class="fa-solid fa-location-dot text-indigo-500 mr-2"></i> Invoicify HQ, Tech City, USA</span>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.sidebar-link');

        function activateLink() {
            let index = sections.length;

            while(--index && window.scrollY + 120 < sections[index].offsetTop) {}

            navLinks.forEach((link) => link.classList.remove('active'));
            if (navLinks[index]) {
                navLinks[index].classList.add('active');
            }
        }

        activateLink();
        window.addEventListener('scroll', activateLink);
    });
</script>
@endsection
