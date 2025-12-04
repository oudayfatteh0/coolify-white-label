@extends('layouts.base')
@section('body')
    @parent
    @if (isSubscribed() || !isCloud())
        <livewire:layout-popups />
    @endif
    <!-- Global search component - included once to prevent keyboard shortcut duplication -->
    <livewire:global-search />
    @auth
        @php
            $branding = branding();
            // Calculate sidebar width dynamically
            // If using logo: use fixed width (14rem/224px) since logo has max-width constraint
            // If using text: calculate based on product name length (min 14rem, max 20rem/320px)
            if ($branding->useLogoInNavbar()) {
                // Logo mode: use standard width (logo max-width is 200px = 12.5rem, add padding = ~14rem)
                $sidebarWidth = 14;
            } else {
                // Text mode: calculate based on character count
                // Base: 14rem (224px) for padding and spacing, add width for text
                $productName = $branding->productShortName();
                $nameLength = mb_strlen($productName);
                // Estimate: each character is roughly 0.45rem wide at text-2xl font-bold
                // Add minimal padding for text container
                $textWidth = $nameLength * 0.45;
                $calculatedWidth = 14 + $textWidth + 0.75; // +0.75rem for minimal padding
                // Clamp between 14rem (224px) and 20rem (320px) to keep sidebar compact
                $sidebarWidth = max(14, min(20, $calculatedWidth));
            }
            // Round to nearest 0.25rem for cleaner CSS
            $sidebarWidth = round($sidebarWidth * 4) / 4;
            $sidebarWidthPx = round($sidebarWidth * 16); // Convert to pixels for reference
        @endphp
            <style>
            :root {
                --sidebar-width: {{ $sidebarWidth }}rem;
                --sidebar-width-px: {{ $sidebarWidthPx }}px;
            }
            /* Ensure main content padding matches sidebar width exactly */
            @media (min-width: 1024px) {
                main[class*="lg:pl-"] {
                    padding-left: var(--sidebar-width) !important;
                }
            }
        </style>
        <livewire:deployments-indicator />
        <div x-data="{
            open: false,
            init() {
                this.pageWidth = localStorage.getItem('pageWidth');
                if (!this.pageWidth) {
                    this.pageWidth = 'full';
                    localStorage.setItem('pageWidth', 'full');
                }
            }
        }" x-cloak class="mx-auto dark:text-inherit text-black"
            :class="pageWidth === 'full' ? '' : 'max-w-7xl'">
            <div class="relative z-50 lg:hidden" :class="open ? 'block' : 'hidden'" role="dialog" aria-modal="true">
<div class="fixed inset-0 bg-black/80" x-on:click="open = !open"></div>
                <div class="fixed inset-y-0 right-0 h-full flex">
                    <div class="relative flex flex-1 w-full" style="max-width: var(--sidebar-width);">
                        <div class="absolute top-0 flex justify-center w-16 pt-5 right-full">
                            <button type="button" class="-m-2.5 p-2.5" x-on:click="open = !open">
                                <span class="sr-only">Close sidebar</span>
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex flex-col pb-2 overflow-y-auto dark:bg-coolgray-100 gap-y-5 scrollbar sidebar-bg" style="min-width: var(--sidebar-width);">
                            <x-navbar />
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:flex-col" style="width: var(--sidebar-width);">
                <div class="flex flex-col overflow-y-auto grow gap-y-5 scrollbar">
                    <x-navbar />
                </div>
            </div>

            <div
                class="sticky top-0 z-40 flex items-center justify-between px-4 py-4 gap-x-6 sm:px-6 lg:hidden bg-white/95 dark:bg-base/95 backdrop-blur-sm border-b border-neutral-300/50 dark:border-coolgray-200/50">
                <div class="flex items-center gap-3 flex-shrink-0">
                    <a href="/" class="flex items-center hover:opacity-80 transition-opacity">
                        @php
                            $branding = branding();
                        @endphp
                        @if ($branding->useLogoInNavbar())
                            <img src="{{ $branding->darkLogoUrl() }}" alt="{{ $branding->productShortName() }}" 
                                class="h-6 w-auto max-w-[160px] object-contain dark:hidden" style="aspect-ratio: 4/1; height: 1.5rem; max-height: 1.5rem;" />
                            <img src="{{ $branding->lightLogoUrl() }}" alt="{{ $branding->productShortName() }}" 
                                class="hidden h-6 w-auto max-w-[160px] object-contain dark:block" style="aspect-ratio: 4/1; height: 1.5rem; max-height: 1.5rem;" />
                        @else
                            <span class="text-xl font-bold tracking-wide dark:text-white">{{ $branding->productShortName() }}</span>
                        @endif
                    </a>
                    <livewire:switch-team />
                </div>
                <button type="button" class="-m-2.5 p-2.5 dark:text-warning" x-on:click="open = !open">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <main class="lg:pl-[var(--sidebar-width)]">
                <div class="p-4 sm:px-6 lg:px-8 lg:py-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    @endauth
@endsection
