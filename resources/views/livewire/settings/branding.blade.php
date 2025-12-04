<div>
    <x-slot:title>
        Branding | {{ branding()->productName() }}
    </x-slot>
    <x-settings.navbar />
    <div x-data="{ activeTab: window.location.hash ? window.location.hash.substring(1) : 'branding' }"
        class="flex flex-col h-full gap-8 sm:flex-row">
        <x-settings.sidebar activeMenu="branding" />
        <form wire:submit='submit' class="flex flex-col w-full">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <h2>Branding</h2>
                    <x-forms.button canGate="update" :canResource="$settings" type="submit">
                        Save
                    </x-forms.button>
                    @if (is_transactional_emails_enabled() && isInstanceAdmin())
                        <x-modal-input buttonTitle="Test Email" title="Send Test Email">
                            <form wire:submit.prevent="sendTestEmail" class="flex flex-col w-full gap-2">
                                <x-forms.input wire:model="test_email_address" placeholder="test@example.com" id="test_email_address"
                                    label="Recipient Email" required />
                                <p class="text-sm dark:text-neutral-400">
                                    This will send a test email using your current branding settings (header, footer, product name).
                                </p>
                                <x-forms.button type="submit" @click="modalOpen=false">
                                    Send Test Email
                                </x-forms.button>
                            </form>
                        </x-modal-input>
                    @endif
                </div>
                <a href="{{ route('settings.branding-presets') }}" 
                    class="px-4 py-2 text-sm bg-neutral-200 dark:bg-coolgray-200 hover:bg-neutral-300 dark:hover:bg-coolgray-300 rounded-md transition-colors">
                    Presets
                </a>
            </div>
            <div class="pb-4">Customize the branding and appearance of your {{ branding()->productName() }} instance.</div>

            <div class="flex flex-col gap-6">
                <!-- Product Information -->
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-semibold dark:text-white">Product Information</h3>
                    <div class="flex gap-2 md:flex-row flex-col w-full">
                        <x-forms.input canGate="update" :canResource="$settings" id="product_name" label="Product Name"
                            placeholder="Coolify"
                            helper="The main product name displayed throughout the interface." />
                        <x-forms.input canGate="update" :canResource="$settings" id="product_short_name"
                            label="Short Name" placeholder="Coolify"
                            helper="A shorter version of the product name for compact displays." />
                    </div>
                    <x-forms.textarea canGate="update" :canResource="$settings" id="tagline" label="Tagline"
                        placeholder="An open-source & self-hostable Heroku / Netlify / Vercel alternative"
                        helper="A brief description or tagline for your product." />
                </div>

                <!-- Colors -->
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-semibold dark:text-white">Colors</h3>
                    <div class="flex gap-2 md:flex-row flex-col w-full">
                        <div class="w-full">
                            <x-forms.input canGate="update" :canResource="$settings" id="primary_color" type="color"
                                label="Primary Color"
                                helper="Main brand color used for primary actions and highlights. Format: #RRGGBB" />
                        </div>
                        <div class="w-full">
                            <x-forms.input canGate="update" :canResource="$settings" id="accent_color" type="color"
                                label="Accent Color"
                                helper="Secondary brand color used for accents and warnings. Format: #RRGGBB" />
                        </div>
                    </div>
                    <div class="w-full" x-data="{
                        selectedColor: @entangle('background_color').live,
                        isDark: false,
                        baseColors: @js(\App\Support\BrandingOptions::getAdaptiveColors()),
                        init() {
                            this.updateTheme();
                            // Watch for theme changes
                            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => this.updateTheme());
                            // Watch for dark class changes
                            const observer = new MutationObserver(() => this.updateTheme());
                            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                        },
                        updateTheme() {
                            const theme = localStorage.getItem('theme') || 'dark';
                            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                            this.isDark = theme === 'dark' || (theme === 'system' && prefersDark) || document.documentElement.classList.contains('dark');
                        },
                        darkenColor(hex, percent) {
                            hex = hex.replace('#', '');
                            const r = parseInt(hex.substr(0, 2), 16);
                            const g = parseInt(hex.substr(2, 2), 16);
                            const b = parseInt(hex.substr(4, 2), 16);
                            
                            // Calculate color ratios to preserve hue
                            const total = r + g + b;
                            let rRatio, gRatio, bRatio;
                            if (total > 0) {
                                rRatio = r / total;
                                gRatio = g / total;
                                bRatio = b / total;
                            } else {
                                rRatio = 0.33;
                                gRatio = 0.33;
                                bRatio = 0.34;
                            }
                            
                            // Darken towards almost black but maintain color ratios
                            const targetDark = 3; // Almost black
                            const darkBase = 8 + (percent / 100) * 4; // Range from 8 to 12
                            
                            const newR = Math.max(targetDark, Math.min(15, Math.floor(darkBase * rRatio * 3)));
                            const newG = Math.max(targetDark, Math.min(15, Math.floor(darkBase * gRatio * 3)));
                            const newB = Math.max(targetDark, Math.min(15, Math.floor(darkBase * bRatio * 3)));
                            
                            return '#' + [newR, newG, newB].map(x => {
                                const hex = x.toString(16);
                                return hex.length === 1 ? '0' + hex : hex;
                            }).join('');
                        },
                        lightenColor(hex, percent) {
                            hex = hex.replace('#', '');
                            const r = parseInt(hex.substr(0, 2), 16);
                            const g = parseInt(hex.substr(2, 2), 16);
                            const b = parseInt(hex.substr(4, 2), 16);
                            const targetR = 250, targetG = 250, targetB = 250;
                            const newR = Math.min(targetR, Math.floor(r + (targetR - r) * percent / 100));
                            const newG = Math.min(targetG, Math.floor(g + (targetG - g) * percent / 100));
                            const newB = Math.min(targetB, Math.floor(b + (targetB - b) * percent / 100));
                            return '#' + [newR, newG, newB].map(x => {
                                const hex = x.toString(16);
                                return hex.length === 1 ? '0' + hex : hex;
                            }).join('');
                        },
                        getColorHex(identifier) {
                            if (!identifier || !this.baseColors[identifier]) return null;
                            const baseColor = this.baseColors[identifier][0];
                            return this.isDark ? this.darkenColor(baseColor, 85) : this.lightenColor(baseColor, 85);
                        },
                        getColorName(identifier) {
                            if (!identifier || !this.baseColors[identifier]) return '';
                            return this.baseColors[identifier][1];
                        }
                    }">
                        <x-forms.select canGate="update" :canResource="$settings" id="background_color" label="Background Color"
                            helper="Choose a predefined color. In dark mode it will be darkened, in light mode it will be lightened automatically.">
                            <option value="">Default (System Theme)</option>
                            @foreach (\App\Support\BrandingOptions::getAdaptiveColors() as $key => $colorData)
                                <option value="{{ $key }}" {{ $background_color === $key ? 'selected' : '' }}>
                                    {{ $colorData[1] }}
                                </option>
                            @endforeach
                        </x-forms.select>
                        @if ($background_color)
                            <div class="mt-2 flex items-center gap-2 p-2 border rounded dark:border-coolgray-200 bg-neutral-50 dark:bg-coolgray-100">
                                <div class="w-8 h-8 rounded border-2 border-neutral-300 dark:border-coolgray-400 shadow-sm" 
                                     :style="'background-color: ' + getColorHex(selectedColor)"></div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                        <span x-text="getColorName(selectedColor)"></span> 
                                        (<span x-text="isDark ? 'Darkened' : 'Lightened'"></span>)
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400" x-text="getColorHex(selectedColor)"></span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Typography -->
                <div class="flex flex-col gap-4" 
                     x-data="{
                        selectedFont: @entangle('font_family').live,
                        fontUrl: @entangle('google_font_url').live,
                        fontMap: {
                            'Roboto, sans-serif': 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
                            'Poppins, sans-serif': 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
                            'Nunito, sans-serif': 'https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap',
                            'Open Sans, sans-serif': 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap',
                            'Lato, sans-serif': 'https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap',
                            'Montserrat, sans-serif': 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap',
                            'Raleway, sans-serif': 'https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap',
                            'Source Sans Pro, sans-serif': 'https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap',
                            'Ubuntu, sans-serif': 'https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap',
                            'Playfair Display, serif': 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap'
                        },
                        getPreviewFont() {
                            return this.selectedFont || 'Inter, sans-serif';
                        },
                        getFontUrl() {
                            if (this.selectedFont && this.fontMap[this.selectedFont]) {
                                return this.fontMap[this.selectedFont];
                            }
                            return this.fontUrl || null;
                        },
                        loadFont(url) {
                            if (!url) return;
                            // Check if font link already exists
                            const existingLink = document.querySelector(`link[data-font-preview='${url}']`);
                            if (existingLink) return;
                            
                            // Create and append font link
                            const link = document.createElement('link');
                            link.rel = 'stylesheet';
                            link.href = url;
                            link.setAttribute('data-font-preview', url);
                            document.head.appendChild(link);
                        }
                     }"
                     x-init="
                        // Load font when component initializes
                        if (getFontUrl()) {
                            loadFont(getFontUrl());
                        }
                     "
                     x-effect="
                        // Watch for changes and load font
                        const url = getFontUrl();
                        if (url) {
                            loadFont(url);
                        }
                     ">
                    <h3 class="text-lg font-semibold dark:text-white">Typography</h3>
                    <div class="p-4 border rounded dark:border-coolgray-200 bg-neutral-50 dark:bg-coolgray-100">
                        <p class="text-sm dark:text-neutral-400 mb-2">
                            Select a Google Font to customize the typography. The font will be applied throughout the application.
                        </p>
                    </div>
                    <div class="flex gap-2 md:flex-row flex-col w-full">
                        <div class="w-full">
                            <x-forms.select canGate="update" :canResource="$settings" id="font_family" label="Font Family" wire:model.live="font_family">
                                <option value="">Default (Inter)</option>
                                <option value="Roboto, sans-serif">Roboto</option>
                                <option value="Poppins, sans-serif">Poppins</option>
                                <option value="Nunito, sans-serif">Nunito</option>
                                <option value="Open Sans, sans-serif">Open Sans</option>
                                <option value="Lato, sans-serif">Lato</option>
                                <option value="Montserrat, sans-serif">Montserrat</option>
                                <option value="Raleway, sans-serif">Raleway</option>
                                <option value="Source Sans Pro, sans-serif">Source Sans Pro</option>
                                <option value="Ubuntu, sans-serif">Ubuntu</option>
                                <option value="Playfair Display, serif">Playfair Display (Serif)</option>
                            </x-forms.select>
                        </div>
                    </div>
                    <!-- Font Preview -->
                    <div class="p-6 border rounded dark:border-coolgray-200 bg-white dark:bg-coolgray-100" 
                         :style="'font-family: ' + getPreviewFont()">
                        <h4 class="text-lg font-semibold mb-2 dark:text-white">Font Preview</h4>
                        <p class="text-base mb-2 dark:text-neutral-300">
                            The quick brown fox jumps over the lazy dog
                        </p>
                        <p class="text-sm dark:text-neutral-400">
                            ABCDEFGHIJKLMNOPQRSTUVWXYZ<br>
                            abcdefghijklmnopqrstuvwxyz<br>
                            0123456789 !@#$%^&*()
                        </p>
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" id="google_font_url" type="url"
                        label="Custom Google Font URL (Optional)"
                        placeholder="https://fonts.googleapis.com/css2?family=YourFont:wght@300;400;500;700&display=swap"
                        helper="For custom fonts not listed above, paste the Google Fonts CSS URL here. Make sure to also set the Font Family above." />
                </div>

                <!-- Logos -->
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-semibold dark:text-white">Logos & Icons</h3>
                    <div class="flex flex-col gap-4">
                        <!-- Dark Logo -->
                        <div class="flex flex-col gap-2">
                            <label class="flex gap-1 items-center mb-1 text-sm font-medium">
                                Dark Logo
                                <x-helper helper="Logo displayed on dark backgrounds. Recommended: SVG or PNG with transparent background. Max 2MB." />
                            </label>
                            @if ($dark_logo_preview)
                                <div class="flex items-center gap-4 p-4 border rounded dark:border-coolgray-200">
                                    <img src="{{ $dark_logo_preview }}" alt="Dark logo preview" class="h-10 object-contain">
                                    <x-forms.button type="button" wire:click="removeDarkLogo" class="bg-error hover:bg-error">
                                        Remove
                                    </x-forms.button>
                                </div>
                            @endif
                            <input type="file" wire:model="dark_logo"
                                accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                class="input">
                            @error('dark_logo')
                                <label class="label">
                                    <span class="text-red-500 label-text-alt">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Light Logo -->
                        <div class="flex flex-col gap-2">
                            <label class="flex gap-1 items-center mb-1 text-sm font-medium">
                                Light Logo
                                <x-helper helper="Logo displayed on light backgrounds. Recommended: SVG or PNG with transparent background. Max 2MB." />
                            </label>
                            @if ($light_logo_preview)
                                <div class="flex items-center gap-4 p-4 border rounded dark:border-coolgray-200 bg-white">
                                    <img src="{{ $light_logo_preview }}" alt="Light logo preview" class="h-10 object-contain">
                                    <x-forms.button type="button" wire:click="removeLightLogo" class="bg-error hover:bg-error">
                                        Remove
                                    </x-forms.button>
                                </div>
                            @endif
                            <input type="file" wire:model="light_logo"
                                accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                class="input">
                            @error('light_logo')
                                <label class="label">
                                    <span class="text-red-500 label-text-alt">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Logo Display Option -->
                        @php
                            $hasLogos = $dark_logo_preview || $light_logo_preview || !empty(branding()->darkLogoPath) || !empty(branding()->lightLogoPath);
                        @endphp
                        @if ($hasLogos)
                            <div class="flex flex-col gap-2 p-4 border rounded dark:border-coolgray-200 bg-neutral-50 dark:bg-coolgray-100">
                                <x-forms.checkbox canGate="update" :canResource="$settings" id="use_logo_in_navbar" 
                                    label="Use Logo in Navigation" 
                                    helper="Display logo instead of product name in login page and sidebar navigation. Only available when logos are uploaded." />
                            </div>
                        @endif

                        <!-- Favicon -->
                        <div class="flex flex-col gap-2">
                            <label class="flex gap-1 items-center mb-1 text-sm font-medium">
                                Favicon
                                <x-helper helper="Browser tab icon. Recommended: ICO, PNG, or SVG. Max 1MB." />
                            </label>
                            @if ($favicon_preview)
                                <div class="flex items-center gap-4 p-4 border rounded dark:border-coolgray-200">
                                    <img src="{{ $favicon_preview }}" alt="Favicon preview" class="h-6 w-6 object-contain">
                                    <x-forms.button type="button" wire:click="removeFavicon" class="bg-error hover:bg-error">
                                        Remove
                                    </x-forms.button>
                                </div>
                            @endif
                            <input type="file" wire:model="favicon"
                                accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/x-icon,image/vnd.microsoft.icon"
                                class="input">
                            @error('favicon')
                                <label class="label">
                                    <span class="text-red-500 label-text-alt">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Meta Information -->
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-semibold dark:text-white">Meta Information</h3>
                    <x-forms.textarea canGate="update" :canResource="$settings" id="meta_description"
                        label="Meta Description"
                        placeholder="Coolify: An open-source & self-hostable Heroku / Netlify / Vercel alternative"
                        helper="Description used in search engines and social media previews." />
                    <x-forms.input canGate="update" :canResource="$settings" id="social_image_url" type="url"
                        label="Social Media Image URL"
                        placeholder="https://cdn.example.com/og-image.png"
                        helper="URL to the image shown when sharing links on social media." />
                </div>

                <!-- Contact & Links -->
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-semibold dark:text-white">Contact & Links</h3>
                    <div class="flex gap-2 md:flex-row flex-col w-full">
                        <x-forms.input canGate="update" :canResource="$settings" id="support_email" type="email"
                            label="Support Email"
                            placeholder="[email protected]"
                            helper="Email address for support inquiries." />
                        <x-forms.input canGate="update" :canResource="$settings" id="docs_url" type="url"
                            label="Documentation URL"
                            placeholder="https://coolify.io/docs"
                            helper="URL to your documentation site." />
                    </div>
                    <x-forms.input canGate="update" :canResource="$settings" id="marketing_site_url" type="url"
                        label="Marketing Site URL"
                        placeholder="https://coolify.io"
                        helper="URL to your main marketing website." />
                </div>

                <!-- Email Templates -->
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold dark:text-white">Email Templates</h3>
                        <a href="{{ route('settings.email-templates') }}" 
                            class="text-sm text-coollabs hover:underline">
                            Advanced Email Customization →
                        </a>
                    </div>
                    <div class="p-4 border rounded dark:border-coolgray-200 bg-neutral-50 dark:bg-coolgray-100">
                        <p class="text-sm dark:text-neutral-400 mb-2">
                            Customize the header and footer for all transactional emails. Supports Markdown formatting.
                        </p>
                        <p class="text-xs dark:text-neutral-500 mb-2">
                            <strong>Available placeholders:</strong><br>
                            <code>{product_name}</code> - Product name<br>
                            <code>{support_url}</code> - Support email or docs URL<br>
                            <code>{docs_url}</code> - Documentation URL<br>
                            <code>{marketing_url}</code> - Marketing site URL
                        </p>
                        <p class="text-xs dark:text-neutral-500 mt-2 pt-2 border-t dark:border-coolgray-300">
                            For advanced email template customization (invitation emails, deployment notifications, etc.), 
                            visit the <a href="{{ route('settings.email-templates') }}" class="text-coollabs underline">Email Templates</a> page.
                        </p>
                    </div>
                    <x-forms.textarea canGate="update" :canResource="$settings" id="email_header" label="Email Header"
                        placeholder="Hello,"
                        helper="Text displayed at the beginning of all emails. Supports Markdown." rows="3" />
                    <x-forms.textarea canGate="update" :canResource="$settings" id="email_footer" label="Email Footer"
                        placeholder="---&#10;&#10;Thank you,&#10;{product_name}&#10;&#10;[Contact Support]({support_url})"
                        helper="Text displayed at the end of all emails. Supports Markdown and placeholders." rows="6" />
                </div>
            </div>
        </form>
    </div>
</div>

