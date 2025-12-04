<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandingPreset extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_system_preset',
        'branding_data',
    ];

    protected $casts = [
        'is_system_preset' => 'boolean',
        'branding_data' => 'array',
    ];

    /**
     * Get all system presets (pre-built themes)
     */
    public static function getSystemPresets(): array
    {
        return [
            [
                'name' => 'Coolify Default',
                'description' => 'The original Coolify branding',
                'is_system_preset' => true,
                'branding_data' => [
                    'product_name' => 'Coolify',
                    'product_short_name' => 'Coolify',
                    'tagline' => 'An open-source & self-hostable Heroku / Netlify / Vercel alternative',
                    'primary_color' => '#6b16ed',
                    'accent_color' => '#fcd452',
                    'meta_description' => 'Coolify: An open-source & self-hostable Heroku / Netlify / Vercel alternative',
                    'social_image_url' => 'https://cdn.coollabs.io/assets/coolify/og-image.png',
                    'support_email' => 'hi@coolify.io',
                    'docs_url' => 'https://coolify.io/docs',
                    'marketing_site_url' => 'https://coolify.io',
                    'email_header' => 'Hello,',
                    'email_footer' => "---\n\nThank you,\n{product_name}\n\n[Contact Support]({support_url})",
                    'use_logo_in_navbar' => true,
                    'background_color' => null,
                    'font_family' => null,
                    'google_font_url' => null,
                ],
            ],
            [
                'name' => 'Corporate Blue',
                'description' => 'Professional corporate theme with blue accents and clean typography',
                'is_system_preset' => true,
                'branding_data' => [
                    'product_name' => 'Your Platform',
                    'product_short_name' => 'Platform',
                    'tagline' => 'Enterprise-grade deployment platform',
                    'primary_color' => '#2563eb',
                    'accent_color' => '#3b82f6',
                    'meta_description' => 'Enterprise-grade deployment and hosting platform',
                    'support_email' => null,
                    'docs_url' => null,
                    'marketing_site_url' => null,
                    'email_header' => 'Hello,',
                    'email_footer' => "---\n\nBest regards,\n{product_name}",
                    'use_logo_in_navbar' => false,
                    'background_color' => '#f8fafc',
                    'font_family' => 'Roboto, sans-serif',
                    'google_font_url' => 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
                ],
            ],
            [
                'name' => 'Modern Green',
                'description' => 'Modern theme with green accents and contemporary font',
                'is_system_preset' => true,
                'branding_data' => [
                    'product_name' => 'Your Service',
                    'product_short_name' => 'Service',
                    'tagline' => 'Modern deployment made simple',
                    'primary_color' => '#10b981',
                    'accent_color' => '#34d399',
                    'meta_description' => 'Modern deployment and hosting platform',
                    'support_email' => null,
                    'docs_url' => null,
                    'marketing_site_url' => null,
                    'email_header' => 'Hello,',
                    'email_footer' => "---\n\nThank you,\n{product_name}",
                    'use_logo_in_navbar' => false,
                    'background_color' => '#f0fdf4',
                    'font_family' => 'Poppins, sans-serif',
                    'google_font_url' => 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
                ],
            ],
            [
                'name' => 'Minimal Dark',
                'description' => 'Minimal dark theme with subtle colors and elegant typography',
                'is_system_preset' => true,
                'branding_data' => [
                    'product_name' => 'Your App',
                    'product_short_name' => 'App',
                    'tagline' => 'Simple. Powerful. Reliable.',
                    'primary_color' => '#6366f1',
                    'accent_color' => '#8b5cf6',
                    'meta_description' => 'Simple and powerful deployment platform',
                    'support_email' => null,
                    'docs_url' => null,
                    'marketing_site_url' => null,
                    'email_header' => 'Hello,',
                    'email_footer' => "---\n\n{product_name}",
                    'use_logo_in_navbar' => false,
                    'background_color' => '#0f172a',
                    'font_family' => 'Inter, sans-serif',
                    'google_font_url' => null,
                ],
            ],
            [
                'name' => 'Warm Orange',
                'description' => 'Warm and friendly theme with orange accents and welcoming font',
                'is_system_preset' => true,
                'branding_data' => [
                    'product_name' => 'Your Platform',
                    'product_short_name' => 'Platform',
                    'tagline' => 'Deploy with confidence',
                    'primary_color' => '#f97316',
                    'accent_color' => '#fb923c',
                    'meta_description' => 'Deploy your applications with confidence',
                    'support_email' => null,
                    'docs_url' => null,
                    'marketing_site_url' => null,
                    'email_header' => 'Hello,',
                    'email_footer' => "---\n\nThanks,\n{product_name}",
                    'use_logo_in_navbar' => false,
                    'background_color' => '#fff7ed',
                    'font_family' => 'Nunito, sans-serif',
                    'google_font_url' => 'https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap',
                ],
            ],
        ];
    }

    /**
     * Seed system presets if they don't exist
     */
    public static function seedSystemPresets(): void
    {
        $systemPresets = self::getSystemPresets();

        foreach ($systemPresets as $preset) {
            self::firstOrCreate(
                ['name' => $preset['name'], 'is_system_preset' => true],
                [
                    'description' => $preset['description'],
                    'branding_data' => $preset['branding_data'],
                ]
            );
        }
    }
}
