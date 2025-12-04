<?php

namespace App\Livewire\Settings;

use App\Models\BrandingPreset;
use App\Models\InstanceSettings;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class BrandingPresets extends Component
{
    use WithFileUploads;

    public InstanceSettings $settings;

    #[Validate('required|string|max:255')]
    public ?string $preset_name = null;

    #[Validate('nullable|string|max:500')]
    public ?string $preset_description = null;

    public $preset_file = null;

    public $presets = [];

    public $systemPresets = [];

    public $userPresets = [];

    public function mount()
    {
        if (! isInstanceAdmin()) {
            return redirect()->route('dashboard');
        }
        $this->settings = instanceSettings();
        $this->loadPresets();
    }

    public function loadPresets()
    {
        // Seed system presets if they don't exist
        BrandingPreset::seedSystemPresets();

        $this->systemPresets = BrandingPreset::where('is_system_preset', true)
            ->orderBy('name')
            ->get()
            ->toArray();

        $this->userPresets = BrandingPreset::where('is_system_preset', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $this->presets = array_merge($this->systemPresets, $this->userPresets);
    }

    public function saveCurrentAsPreset()
    {
        try {
            $this->validate([
                'preset_name' => 'required|string|max:255',
                'preset_description' => 'nullable|string|max:500',
            ]);

            $branding = $this->settings->branding ?? [];

            // Remove background_color, font_family, and google_font_url from custom presets
            // These are only available in system presets
            unset($branding['background_color'], $branding['font_family'], $branding['google_font_url']);

            BrandingPreset::create([
                'name' => $this->preset_name,
                'description' => $this->preset_description,
                'is_system_preset' => false,
                'branding_data' => $branding,
            ]);

            $this->preset_name = null;
            $this->preset_description = null;
            $this->loadPresets();
            $this->dispatch('success', 'Preset saved successfully!');
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function applyPreset(int $presetId)
    {
        try {
            $preset = BrandingPreset::findOrFail($presetId);
            $this->settings->branding = $preset->branding_data;
            $this->settings->save();

            // Clear branding cache
            \Cache::forget('instance_settings_branding');

            $this->dispatch('success', 'Preset applied successfully! Redirecting to branding page...');

            // Redirect to branding page after a short delay
            return redirect()->route('settings.branding')->with('success', 'Preset applied successfully!');
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function deletePreset(int $presetId)
    {
        try {
            $preset = BrandingPreset::findOrFail($presetId);

            // Don't allow deleting system presets
            if ($preset->is_system_preset) {
                throw new \Exception('Cannot delete system presets.');
            }

            $preset->delete();
            $this->loadPresets();
            $this->dispatch('success', 'Preset deleted successfully!');
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function exportPreset(int $presetId)
    {
        try {
            $preset = BrandingPreset::findOrFail($presetId);
            $exportData = [
                'name' => $preset->name,
                'description' => $preset->description,
                'branding_data' => $preset->branding_data,
            ];

            $filename = str_replace(' ', '-', strtolower($preset->name)).'-preset.json';

            return response()->streamDownload(function () use ($exportData) {
                echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }, $filename, [
                'Content-Type' => 'application/json',
            ]);
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function importPreset()
    {
        try {
            $this->validate([
                'preset_file' => 'required|file|mimes:json|max:1024',
            ]);

            $fileContent = file_get_contents($this->preset_file->getRealPath());
            $importData = json_decode($fileContent, true);

            if (! isset($importData['branding_data'])) {
                throw new \Exception('Invalid preset file format.');
            }

            BrandingPreset::create([
                'name' => $importData['name'] ?? 'Imported Preset',
                'description' => $importData['description'] ?? null,
                'is_system_preset' => false,
                'branding_data' => $importData['branding_data'],
            ]);

            $this->loadPresets();
            $this->dispatch('success', 'Preset imported successfully!');
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function render()
    {
        return view('livewire.settings.branding-presets');
    }
}
