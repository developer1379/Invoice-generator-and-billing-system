@extends('invoices.layout')

@section('title', 'Terms of Service — Invoicify')

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
            <i class="fa-solid fa-file-contract"></i> Agreement & Rules
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-zinc-50 tracking-tight">
            Terms of Service
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
                <a href="#acceptance" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    1. Acceptance of Terms
                </a>
                <a href="#use-license" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    2. Use License
                </a>
                <a href="#user-accounts" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    3. User Accounts
                </a>
                <a href="#math-accuracy" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    4. Invoicing Math & Output
                </a>
                <a href="#disclaimer" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    5. Warranties & Disclaimer
                </a>
                <a href="#termination" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    6. Termination
                </a>
                <a href="#governing-law" class="sidebar-link block pl-3 py-2 border-l-2 border-slate-100 dark:border-zinc-800 text-xs font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600 dark:hover:border-indigo-400 transition-all">
                    7. Governing Law
                </a>
            </div>
        </nav>

        <!-- Main Content Body (9 cols) -->
        <div class="lg:col-span-9 space-y-6">
            
            <!-- Section 1: Acceptance of Terms -->
            <section id="acceptance" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">1. Acceptance of Terms</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        By accessing or using the Invoicify application, website, and services, you agree to be bound by these Terms of Service. If you do not agree to all of the terms and conditions outlined here, you are not authorized to use the service.
                    </p>
                    <p>
                        We reserve the right to modify or replace these terms at any time at our sole discretion. We will indicate changes by updating the "Last Updated" date at the top of this document. Continued use after changes constitutes agreement.
                    </p>
                </div>
            </section>

            <!-- Section 2: Use License -->
            <section id="use-license" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-blue-50 dark:bg-blue-955/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-book text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">2. Use License</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-4 leading-relaxed">
                    <p>
                        Permission is granted to access, create drafts, log entries, and export invoices using the Invoicify application for personal or commercial use. Under this license, you may not:
                    </p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Attempt to decompile, reverse engineer, or extract code from Invoicify backend systems.</li>
                        <li>Use any automated tools, scrapers, or bots to query the POS Till or product databases.</li>
                        <li>Upload malware, malicious signature structures, or fraudulent company logos.</li>
                        <li>Remove any copyright, trade names, or branding indications from our core stylesheet templates.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 3: User Accounts -->
            <section id="user-accounts" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-emerald-50 dark:bg-emerald-955/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-user-shield text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">3. User Accounts</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        To access certain features of the application, such as saving default profiles, clients, product catalogs, and viewing analytics charts, you must create a registered account.
                    </p>
                    <p>
                        You are responsible for safeguarding your login credentials (and/or Google OAuth tokens) and for all activities that occur under your account. You agree to immediately notify us of any unauthorized use or security compromise.
                    </p>
                </div>
            </section>

            <!-- Section 4: Invoicing Math & Output -->
            <section id="math-accuracy" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-purple-50 dark:bg-purple-955/40 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-calculator text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">4. Invoicing Math & Output</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        Invoicify provides compound calculators for quantities, unit rates, tax percentages, discount ranges, and shipping inputs. While we use highly audited Alpine.js logic to perform math computations, users are solely responsible for verifying the accuracy of all totals before distributing invoices or bills to clients.
                    </p>
                    <p>
                        Invoicify shall not be held liable for any loss, legal disputes, or accounting errors resulting from incorrect variables, tax classification mistakes, or layout overflow issues.
                    </p>
                </div>
            </section>

            <!-- Section 5: Warranties & Disclaimer -->
            <section id="disclaimer" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-amber-50 dark:bg-amber-955/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">5. Warranties & Disclaimer</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        Invoicify is provided "as is" and "as available" without any warranties of any kind, whether express or implied.
                    </p>
                    <p>
                        We do not warrant that the application will be uninterrupted, error-free, secure, or free from server outages. You use this client-side builder at your own risk. To the fullest extent permitted by law, Invoicify disclaims all liability for any direct, indirect, incidental, or consequential damages.
                    </p>
                </div>
            </section>

            <!-- Section 6: Termination -->
            <section id="termination" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-rose-50 dark:bg-rose-955/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-ban text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">6. Termination</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        We may terminate or suspend access to our service immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.
                    </p>
                    <p>
                        All provisions of the Terms which by their nature should survive termination shall survive termination, including ownership provisions, warranty disclaimers, indemnity, and limitations of liability.
                    </p>
                </div>
            </section>

            <!-- Section 7: Governing Law -->
            <section id="governing-law" class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-slate-200/50 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-9 w-9 rounded-xl bg-slate-50 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-gavel text-sm"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-zinc-100">7. Governing Law</h2>
                </div>
                <div class="text-xs text-slate-600 dark:text-zinc-400 space-y-3 leading-relaxed">
                    <p>
                        These Terms shall be governed and construed in accordance with the laws of Tech City, USA, without regard to its conflict of law provisions.
                    </p>
                    <p>
                        Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights.
                    </p>
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
