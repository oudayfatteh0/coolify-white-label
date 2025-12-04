<?php

namespace App\Livewire\Settings;

use App\Models\InstanceSettings;
use App\Models\Team;
use App\Notifications\TransactionalEmails\Test;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Branding extends Component
{
    use WithFileUploads;

    public InstanceSettings $settings;

    #[Validate('nullable|string|max:255')]
    public ?string $product_name = null;

    #[Validate('nullable|string|max:100')]
    public ?string $product_short_name = null;

    #[Validate('nullable|string|max:500')]
    public ?string $tagline = null;

    #[Validate('nullable|string|regex:/^#[0-9A-Fa-f]{6}$/')]
    public ?string $primary_color = null;

    #[Validate('nullable|string|regex:/^#[0-9A-Fa-f]{6}$/')]
    public ?string $accent_color = null;

    #[Validate('nullable|string|max:500')]
    public ?string $meta_description = null;

    #[Validate('nullable|url|max:500')]
    public ?string $social_image_url = null;

    #[Validate('nullable|email|max:255')]
    public ?string $support_email = null;

    #[Validate('nullable|url|max:500')]
    public ?string $docs_url = null;

    #[Validate('nullable|url|max:500')]
    public ?string $marketing_site_url = null;

    #[Validate('nullable|string|max:2000')]
    public ?string $email_header = null;

    #[Validate('nullable|string|max:5000')]
    public ?string $email_footer = null;

    #[Validate('nullable|boolean')]
    public ?bool $use_logo_in_navbar = null;

    #[Validate('nullable|string|max:255')]
    public ?string $font_family = null;

    #[Validate('nullable|url|max:500')]
    public ?string $google_font_url = null;

    public function updatedFontFamily($value)
    {
        // Auto-generate Google Font URL for common fonts
        if ($value) {
            $fontMap = [
                'Roboto, sans-serif' => 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
                'Poppins, sans-serif' => 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
                'Nunito, sans-serif' => 'https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap',
                'Open Sans, sans-serif' => 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap',
                'Lato, sans-serif' => 'https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap',
                'Montserrat, sans-serif' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap',
                'Raleway, sans-serif' => 'https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap',
                'Source Sans Pro, sans-serif' => 'https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap',
                'Ubuntu, sans-serif' => 'https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap',
                'Playfair Display, serif' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap',
            ];

            if (isset($fontMap[$value])) {
                $this->google_font_url = $fontMap[$value];
            } elseif (empty($this->google_font_url)) {
                // Clear Google Font URL if font is not in map and no custom URL is set
                $this->google_font_url = null;
            }
        } else {
            $this->google_font_url = null;
        }
    }

    #[Validate('nullable|string')]
    public ?string $background_color = null;

    public function updatedBackgroundColor($value)
    {
        // Validate that it's either empty or a valid color identifier
        if ($value && ! in_array($value, ['slate', 'gray', 'zinc', 'neutral', 'stone', 'red', 'orange', 'amber', 'green', 'blue'], true)) {
            $this->addError('background_color', 'The selected background color is invalid.');

            return;
        }
    }

    public $dark_logo = null;

    public $light_logo = null;

    public $favicon = null;

    public ?string $dark_logo_preview = null;

    public ?string $light_logo_preview = null;

    public ?string $favicon_preview = null;

    #[Validate('nullable|email')]
    public ?string $test_email_address = null;

    public Team $team;

    public function mount()
    {
        if (! isInstanceAdmin()) {
            return redirect()->route('dashboard');
        }
        $this->settings = instanceSettings();
        $this->team = auth()->user()->currentTeam();
        $this->test_email_address = auth()->user()->email;
        $branding = $this->settings->branding ?? [];

        $this->product_name = $branding['product_name'] ?? null;
        $this->product_short_name = $branding['product_short_name'] ?? null;
        $this->tagline = $branding['tagline'] ?? null;
        $this->primary_color = $branding['primary_color'] ?? null;
        $this->accent_color = $branding['accent_color'] ?? null;
        $this->meta_description = $branding['meta_description'] ?? null;
        $this->social_image_url = $branding['social_image_url'] ?? null;
        $this->support_email = $branding['support_email'] ?? null;
        $this->docs_url = $branding['docs_url'] ?? null;
        $this->marketing_site_url = $branding['marketing_site_url'] ?? null;
        $this->email_header = $branding['email_header'] ?? null;
        $this->email_footer = $branding['email_footer'] ?? null;
        $this->use_logo_in_navbar = $branding['use_logo_in_navbar'] ?? null;
        $this->font_family = $branding['font_family'] ?? null;
        $this->google_font_url = $branding['google_font_url'] ?? null;
        $this->background_color = $branding['background_color'] ?? null;

        // Set preview URLs for existing logos
        if (! empty($branding['dark_logo_path'])) {
            $this->dark_logo_preview = asset('storage/'.$branding['dark_logo_path']);
        }
        if (! empty($branding['light_logo_path'])) {
            $this->light_logo_preview = asset('storage/'.$branding['light_logo_path']);
        }
        if (! empty($branding['favicon_path'])) {
            $this->favicon_preview = asset('storage/'.$branding['favicon_path']);
        }
    }

    public function updatedDarkLogo()
    {
        $this->validateOnly('dark_logo', [
            'dark_logo' => 'nullable|image|max:2048|mimes:png,jpg,jpeg,svg,webp',
        ]);
    }

    public function updatedLightLogo()
    {
        $this->validateOnly('light_logo', [
            'light_logo' => 'nullable|image|max:2048|mimes:png,jpg,jpeg,svg,webp',
        ]);
    }

    public function updatedFavicon()
    {
        $this->validateOnly('favicon', [
            'favicon' => 'nullable|image|max:1024|mimes:png,jpg,jpeg,svg,ico',
        ]);
    }

    public function removeDarkLogo()
    {
        $branding = $this->settings->branding ?? [];
        if (! empty($branding['dark_logo_path'])) {
            Storage::disk('public')->delete($branding['dark_logo_path']);
            unset($branding['dark_logo_path']);
            $this->settings->branding = $branding;
            $this->settings->save();
            $this->dark_logo_preview = null;
            $this->dispatch('success', 'Dark logo removed!');
        }
    }

    public function removeLightLogo()
    {
        $branding = $this->settings->branding ?? [];
        if (! empty($branding['light_logo_path'])) {
            Storage::disk('public')->delete($branding['light_logo_path']);
            unset($branding['light_logo_path']);
            $this->settings->branding = $branding;
            $this->settings->save();
            $this->light_logo_preview = null;
            $this->dispatch('success', 'Light logo removed!');
        }
    }

    public function removeFavicon()
    {
        $branding = $this->settings->branding ?? [];
        if (! empty($branding['favicon_path'])) {
            Storage::disk('public')->delete($branding['favicon_path']);
            unset($branding['favicon_path']);
            $this->settings->branding = $branding;
            $this->settings->save();
            $this->favicon_preview = null;
            $this->dispatch('success', 'Favicon removed!');
        }
    }

    public function submit()
    {
        try {
            $this->validate();

            $branding = $this->settings->branding ?? [];

            // Handle file uploads
            if ($this->dark_logo) {
                // Delete old logo if exists
                if (! empty($branding['dark_logo_path'])) {
                    Storage::disk('public')->delete($branding['dark_logo_path']);
                }
                $path = $this->dark_logo->store('branding', 'public');
                $branding['dark_logo_path'] = $path;
            }

            if ($this->light_logo) {
                // Delete old logo if exists
                if (! empty($branding['light_logo_path'])) {
                    Storage::disk('public')->delete($branding['light_logo_path']);
                }
                $path = $this->light_logo->store('branding', 'public');
                $branding['light_logo_path'] = $path;
            }

            if ($this->favicon) {
                // Delete old favicon if exists
                if (! empty($branding['favicon_path'])) {
                    Storage::disk('public')->delete($branding['favicon_path']);
                }
                $path = $this->favicon->store('branding', 'public');
                $branding['favicon_path'] = $path;
            }

            // Update branding data
            $branding['product_name'] = $this->product_name ?: null;
            $branding['product_short_name'] = $this->product_short_name ?: null;
            $branding['tagline'] = $this->tagline ?: null;
            $branding['primary_color'] = $this->primary_color ?: null;
            $branding['accent_color'] = $this->accent_color ?: null;
            $branding['meta_description'] = $this->meta_description ?: null;
            $branding['social_image_url'] = $this->social_image_url ?: null;
            $branding['support_email'] = $this->support_email ?: null;
            $branding['docs_url'] = $this->docs_url ?: null;
            $branding['marketing_site_url'] = $this->marketing_site_url ?: null;
            $branding['email_header'] = $this->email_header ?: null;
            $branding['email_footer'] = $this->email_footer ?: null;
            $branding['use_logo_in_navbar'] = $this->use_logo_in_navbar;
            $branding['font_family'] = $this->font_family ?: null;
            $branding['google_font_url'] = $this->google_font_url ?: null;
            $branding['background_color'] = $this->background_color ?: null;

            // Remove null values to keep JSON clean
            $branding = array_filter($branding, fn ($value) => $value !== null && $value !== '');

            $this->settings->branding = $branding;
            $this->settings->save();

            // Clear branding cache
            \Cache::forget('instance_settings_branding');

            // Update preview URLs
            if ($this->dark_logo) {
                $this->dark_logo_preview = asset('storage/'.$branding['dark_logo_path']);
            }
            if ($this->light_logo) {
                $this->light_logo_preview = asset('storage/'.$branding['light_logo_path']);
            }
            if ($this->favicon) {
                $this->favicon_preview = asset('storage/'.$branding['favicon_path']);
            }

            // Reset file uploads
            $this->dark_logo = null;
            $this->light_logo = null;
            $this->favicon = null;

            $this->dispatch('success', 'Branding settings updated successfully!');

            // Reload page to apply all changes (fonts, colors, etc.)
            return redirect()->route('settings.branding');
        } catch (\Exception $e) {
            return handleError($e, $this);
        }
    }

    public function sendTestEmail()
    {
        try {
            $this->validate([
                'test_email_address' => 'required|email',
            ], [
                'test_email_address.required' => 'Test email address is required.',
                'test_email_address.email' => 'Please enter a valid email address.',
            ]);

            if (! is_transactional_emails_enabled()) {
                $this->dispatch('error', 'Transactional emails are not enabled. Please configure SMTP or Resend in Settings > Transactional Email.');

                return;
            }

            $executed = RateLimiter::attempt(
                'test-email-branding:'.$this->team->id,
                $perMinute = 0,
                function () {
                    $this->team?->notifyNow(new Test($this->test_email_address));
                    $this->dispatch('success', 'Test email sent! Check your inbox to see your branding.');
                },
                $decaySeconds = 10,
            );

            if (! $executed) {
                throw new \Exception('Too many test emails sent! Please wait a moment and try again.');
            }
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function render()
    {
        return view('livewire.settings.branding');
    }
}
