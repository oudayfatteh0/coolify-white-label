<?php

namespace App\Support;

class BrandingOptions
{
    public function __construct(
        public readonly ?string $productName = null,
        public readonly ?string $productShortName = null,
        public readonly ?string $tagline = null,
        public readonly ?string $primaryColor = null,
        public readonly ?string $accentColor = null,
        public readonly ?string $darkLogoPath = null,
        public readonly ?string $lightLogoPath = null,
        public readonly ?string $faviconPath = null,
        public readonly ?string $metaDescription = null,
        public readonly ?string $socialImageUrl = null,
        public readonly ?string $supportEmail = null,
        public readonly ?string $docsUrl = null,
        public readonly ?string $marketingSiteUrl = null,
        public readonly ?string $emailHeader = null,
        public readonly ?string $emailFooter = null,
        public readonly ?bool $useLogoInNavbar = null,
        public readonly ?string $backgroundColor = null,
        public readonly ?string $fontFamily = null,
        public readonly ?string $googleFontUrl = null,
    ) {}

    public static function defaults(): self
    {
        return new self(
            productName: 'Coolify',
            productShortName: 'Coolify',
            tagline: 'An open-source & self-hostable Heroku / Netlify / Vercel alternative',
            primaryColor: '#6b16ed',
            accentColor: '#fcd452',
            darkLogoPath: 'coolify-logo.svg',
            lightLogoPath: 'coolify-logo.svg',
            faviconPath: 'coolify-logo.svg',
            metaDescription: 'Coolify: An open-source & self-hostable Heroku / Netlify / Vercel alternative',
            socialImageUrl: 'https://cdn.coollabs.io/assets/coolify/og-image.png',
            supportEmail: null,
            docsUrl: 'https://coolify.io/docs',
            marketingSiteUrl: 'https://coolify.io',
            emailHeader: 'Hello,',
            emailFooter: "---\n\nThank you,\n{product_name}\n\n[Contact Support]({support_url})",
        );
    }

    public static function fromArray(?array $branding): self
    {
        if (empty($branding)) {
            return self::defaults();
        }

        return new self(
            productName: $branding['product_name'] ?? null,
            productShortName: $branding['product_short_name'] ?? null,
            tagline: $branding['tagline'] ?? null,
            primaryColor: $branding['primary_color'] ?? null,
            accentColor: $branding['accent_color'] ?? null,
            darkLogoPath: $branding['dark_logo_path'] ?? null,
            lightLogoPath: $branding['light_logo_path'] ?? null,
            faviconPath: $branding['favicon_path'] ?? null,
            metaDescription: $branding['meta_description'] ?? null,
            socialImageUrl: $branding['social_image_url'] ?? null,
            supportEmail: $branding['support_email'] ?? null,
            docsUrl: $branding['docs_url'] ?? null,
            marketingSiteUrl: $branding['marketing_site_url'] ?? null,
            emailHeader: $branding['email_header'] ?? null,
            emailFooter: $branding['email_footer'] ?? null,
            useLogoInNavbar: $branding['use_logo_in_navbar'] ?? null,
            backgroundColor: $branding['background_color'] ?? null,
            fontFamily: $branding['font_family'] ?? null,
            googleFontUrl: $branding['google_font_url'] ?? null,
        );
    }

    public function productName(): string
    {
        return $this->productName ?? self::defaults()->productName;
    }

    public function productShortName(): string
    {
        return $this->productShortName ?? $this->productName();
    }

    public function tagline(): ?string
    {
        return $this->tagline;
    }

    public function primaryColor(): string
    {
        return $this->primaryColor ?? self::defaults()->primaryColor;
    }

    public function accentColor(): string
    {
        return $this->accentColor ?? self::defaults()->accentColor;
    }

    public function darkLogoUrl(): string
    {
        if ($this->darkLogoPath) {
            return asset('storage/'.$this->darkLogoPath);
        }

        return asset(self::defaults()->darkLogoPath);
    }

    public function lightLogoUrl(): string
    {
        if ($this->lightLogoPath) {
            return asset('storage/'.$this->lightLogoPath);
        }

        return asset(self::defaults()->lightLogoPath);
    }

    public function faviconUrl(): string
    {
        if ($this->faviconPath) {
            return asset('storage/'.$this->faviconPath);
        }

        return asset(self::defaults()->faviconPath);
    }

    public function faviconType(): string
    {
        $path = $this->faviconPath ?? self::defaults()->faviconPath;
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'ico' => 'image/x-icon',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            default => 'image/svg+xml',
        };
    }

    public function metaDescription(): string
    {
        return $this->metaDescription ?? self::defaults()->metaDescription;
    }

    public function socialImageUrl(): string
    {
        return $this->socialImageUrl ?? self::defaults()->socialImageUrl;
    }

    public function supportEmail(): ?string
    {
        return $this->supportEmail;
    }

    public function docsUrl(): string
    {
        return $this->docsUrl ?? self::defaults()->docsUrl;
    }

    public function marketingSiteUrl(): string
    {
        return $this->marketingSiteUrl ?? self::defaults()->marketingSiteUrl;
    }

    public function emailSubject(string $subject): string
    {
        return $this->productName().': '.$subject;
    }

    public function emailHeader(): string
    {
        return $this->emailHeader ?? self::defaults()->emailHeader;
    }

    public function emailFooter(): string
    {
        $footer = $this->emailFooter ?? self::defaults()->emailFooter;

        // Replace placeholders (using {placeholder} format to avoid Blade conflicts)
        $footer = str_replace('{product_name}', $this->productName(), $footer);
        $supportUrl = $this->supportEmail() ? 'mailto:'.$this->supportEmail() : $this->docsUrl();
        $footer = str_replace('{support_url}', $supportUrl, $footer);
        $footer = str_replace('{docs_url}', $this->docsUrl(), $footer);
        $footer = str_replace('{marketing_url}', $this->marketingSiteUrl(), $footer);

        return $footer;
    }

    public function useLogoInNavbar(): bool
    {
        // Only use logo if it's enabled AND logos are uploaded
        if ($this->useLogoInNavbar === false) {
            return false;
        }

        // Default to true if logos exist, false otherwise
        $hasLogos = ! empty($this->darkLogoPath) || ! empty($this->lightLogoPath);

        return $this->useLogoInNavbar ?? ($hasLogos ? true : false);
    }

    public function backgroundColor(): ?string
    {
        if (! $this->backgroundColor) {
            return null;
        }

        // If it's already a hex color (starts with #), return as is
        if (str_starts_with($this->backgroundColor, '#')) {
            return $this->backgroundColor;
        }

        // Otherwise, it's a color identifier - return light mode color by default
        // Dark mode will be handled via CSS
        return $this->getThemeAdaptiveColor($this->backgroundColor, 'light');
    }

    public function getThemeAdaptiveColor(string $colorIdentifier, string $theme = 'light'): string
    {
        // Base colors (mid-tone colors that will be lightened/darkened)
        $baseColors = [
            'slate' => '#64748b',    // Slate-500
            'gray' => '#6b7280',     // Gray-500
            'zinc' => '#71717a',     // Zinc-500
            'neutral' => '#737373',  // Neutral-500
            'stone' => '#78716c',    // Stone-500
            'red' => '#ef4444',      // Red-500
            'orange' => '#f97316',   // Orange-500
            'amber' => '#f59e0b',    // Amber-500
            'green' => '#22c55e',    // Green-500
            'blue' => '#3b82f6',     // Blue-500
        ];

        // Check if it's a valid color identifier
        if (! isset($baseColors[$colorIdentifier])) {
            // Fallback to default if invalid
            $baseColor = $baseColors['slate'];
        } else {
            $baseColor = $baseColors[$colorIdentifier];
        }

        // Apply darkening for dark mode or lightening for light mode
        if ($theme === 'dark') {
            // Darken significantly (towards almost black but still maintain color hue)
            return $this->darkenColor($baseColor, 85);
        } else {
            // Lighten significantly (towards very light but not pure white)
            return $this->lightenColor($baseColor, 85);
        }
    }

    private function darkenColor(string $hex, int $percent): string
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Darken each component (move towards almost black but preserve color hue)
        // Target is very dark (#030303) but we'll preserve relative color ratios
        $targetDark = 3; // Almost black but not pure black

        // Calculate the ratio of each color component to preserve hue
        $total = $r + $g + $b;
        if ($total > 0) {
            $rRatio = $r / $total;
            $gRatio = $g / $total;
            $bRatio = $b / $total;
        } else {
            $rRatio = 0.33;
            $gRatio = 0.33;
            $bRatio = 0.34;
        }

        // Darken towards target but maintain color ratios
        // Use a darker base (around 8-12 total RGB) and distribute based on ratios
        $darkBase = 8 + ($percent / 100) * 4; // Range from 8 to 12

        $r = max($targetDark, min(15, floor($darkBase * $rRatio * 3)));
        $g = max($targetDark, min(15, floor($darkBase * $gRatio * 3)));
        $b = max($targetDark, min(15, floor($darkBase * $bRatio * 3)));

        // Convert back to hex
        return '#'.str_pad(dechex((int) $r), 2, '0', STR_PAD_LEFT).
                   str_pad(dechex((int) $g), 2, '0', STR_PAD_LEFT).
                   str_pad(dechex((int) $b), 2, '0', STR_PAD_LEFT);
    }

    private function lightenColor(string $hex, int $percent): string
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Lighten towards very light but not pure white (#fafafa)
        $targetR = 250;
        $targetG = 250;
        $targetB = 250;

        $r = min($targetR, $r + ($targetR - $r) * $percent / 100);
        $g = min($targetG, $g + ($targetG - $g) * $percent / 100);
        $b = min($targetB, $b + ($targetB - $b) * $percent / 100);

        // Convert back to hex
        return '#'.str_pad(dechex((int) $r), 2, '0', STR_PAD_LEFT).
                   str_pad(dechex((int) $g), 2, '0', STR_PAD_LEFT).
                   str_pad(dechex((int) $b), 2, '0', STR_PAD_LEFT);
    }

    public static function getAdaptiveColors(): array
    {
        // Return base colors with names for the UI
        return [
            'slate' => ['#64748b', 'Slate'],
            'gray' => ['#6b7280', 'Gray'],
            'zinc' => ['#71717a', 'Zinc'],
            'neutral' => ['#737373', 'Neutral'],
            'stone' => ['#78716c', 'Stone'],
            'red' => ['#ef4444', 'Red'],
            'orange' => ['#f97316', 'Orange'],
            'amber' => ['#f59e0b', 'Amber'],
            'green' => ['#22c55e', 'Green'],
            'blue' => ['#3b82f6', 'Blue'],
        ];
    }

    public function sidebarBackgroundColor(): ?string
    {
        if (! $this->backgroundColor) {
            return null;
        }

        $bgColor = $this->backgroundColor();

        // Lighten the background color by ~8% for sidebar
        return $this->lightenColor($bgColor, 8);
    }

    public function fontFamily(): ?string
    {
        return $this->fontFamily;
    }

    public function googleFontUrl(): ?string
    {
        return $this->googleFontUrl;
    }

    public function toArray(): array
    {
        return [
            'product_name' => $this->productName,
            'product_short_name' => $this->productShortName,
            'tagline' => $this->tagline,
            'primary_color' => $this->primaryColor,
            'accent_color' => $this->accentColor,
            'dark_logo_path' => $this->darkLogoPath,
            'light_logo_path' => $this->lightLogoPath,
            'favicon_path' => $this->faviconPath,
            'meta_description' => $this->metaDescription,
            'social_image_url' => $this->socialImageUrl,
            'support_email' => $this->supportEmail,
            'docs_url' => $this->docsUrl,
            'marketing_site_url' => $this->marketingSiteUrl,
            'email_header' => $this->emailHeader,
            'email_footer' => $this->emailFooter,
            'use_logo_in_navbar' => $this->useLogoInNavbar,
            'background_color' => $this->backgroundColor,
            'font_family' => $this->fontFamily,
            'google_font_url' => $this->googleFontUrl,
        ];
    }
}
