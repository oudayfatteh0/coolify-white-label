<div>
    <x-slot:title>
        Branding Presets | {{ branding()->productName() }}
    </x-slot>
    <x-settings.navbar />
    <div class="flex flex-col h-full gap-8 sm:flex-row">
        <x-settings.sidebar activeMenu="branding" />
        <div class="flex flex-col w-full">
            <div class="flex items-center gap-2 mb-4">
                <h2>Branding Presets</h2>
            </div>
            <div class="pb-4">
                Save your current branding as a preset, or apply pre-built themes. Presets can be exported and imported for easy backup and sharing.
            </div>

            <div class="flex flex-col gap-6">
                <!-- Save Current as Preset -->
                <div class="flex flex-col gap-4 p-4 border rounded dark:border-coolgray-200 bg-neutral-50 dark:bg-coolgray-100">
                    <h3 class="text-lg font-semibold dark:text-white">Save Current Branding as Preset</h3>
                    <form wire:submit.prevent="saveCurrentAsPreset" class="flex flex-col gap-4">
                        <x-forms.input wire:model="preset_name" id="preset_name" label="Preset Name" 
                            placeholder="My Custom Theme" required />
                        <x-forms.textarea wire:model="preset_description" id="preset_description" 
                            label="Description (Optional)" placeholder="A description of this preset..." rows="2" />
                        <x-forms.button type="submit" canGate="update" :canResource="$settings">
                            Save Current Branding as Preset
                        </x-forms.button>
                    </form>
                </div>

                <!-- System Presets -->
                @if (count($systemPresets) > 0)
                    <div class="flex flex-col gap-4">
                        <h3 class="text-lg font-semibold dark:text-white">Pre-built Themes</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($systemPresets as $preset)
                                <div class="p-4 border rounded dark:border-coolgray-200 bg-white dark:bg-coolgray-200">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h4 class="font-semibold dark:text-white">{{ $preset['name'] }}</h4>
                                            @if ($preset['description'])
                                                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                                    {{ $preset['description'] }}
                                                </p>
                                            @endif
                                        </div>
                                        <span class="text-xs px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded">
                                            System
                                        </span>
                                    </div>
                                    <div class="flex gap-2 mt-4">
                                        <x-forms.button wire:click="applyPreset({{ $preset['id'] }})" 
                                            class="flex-1" canGate="update" :canResource="$settings">
                                            Apply
                                        </x-forms.button>
                                        <x-forms.button wire:click="exportPreset({{ $preset['id'] }})" 
                                            class="bg-neutral-200 dark:bg-coolgray-300 hover:bg-neutral-300 dark:hover:bg-coolgray-400">
                                            Export
                                        </x-forms.button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- User Presets -->
                @if (count($userPresets) > 0)
                    <div class="flex flex-col gap-4">
                        <h3 class="text-lg font-semibold dark:text-white">Your Presets</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($userPresets as $preset)
                                <div class="p-4 border rounded dark:border-coolgray-200 bg-white dark:bg-coolgray-200">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h4 class="font-semibold dark:text-white">{{ $preset['name'] }}</h4>
                                            @if ($preset['description'])
                                                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                                    {{ $preset['description'] }}
                                                </p>
                                            @endif
                                            <p class="text-xs text-neutral-500 dark:text-neutral-500 mt-1">
                                                Created {{ \Carbon\Carbon::parse($preset['created_at'])->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 mt-4">
                                        <x-forms.button wire:click="applyPreset({{ $preset['id'] }})" 
                                            class="flex-1" canGate="update" :canResource="$settings">
                                            Apply
                                        </x-forms.button>
                                        <x-forms.button wire:click="exportPreset({{ $preset['id'] }})" 
                                            class="bg-neutral-200 dark:bg-coolgray-300 hover:bg-neutral-300 dark:hover:bg-coolgray-400">
                                            Export
                                        </x-forms.button>
                                        <x-forms.button wire:click="deletePreset({{ $preset['id'] }})" 
                                            wire:confirm="Are you sure you want to delete this preset?"
                                            class="bg-error hover:bg-error">
                                            Delete
                                        </x-forms.button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif (count($systemPresets) > 0)
                    <div class="p-4 border rounded dark:border-coolgray-200 bg-neutral-50 dark:bg-coolgray-100">
                        <p class="text-sm dark:text-neutral-400">
                            You haven't created any custom presets yet. Save your current branding above to create one.
                        </p>
                    </div>
                @endif

                <!-- Import Preset -->
                <div class="flex flex-col gap-4 p-4 border rounded dark:border-coolgray-200 bg-neutral-50 dark:bg-coolgray-100">
                    <h3 class="text-lg font-semibold dark:text-white">Import Preset</h3>
                    <form wire:submit.prevent="importPreset" class="flex flex-col gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium dark:text-white">Preset File (JSON)</label>
                            <input type="file" wire:model="preset_file" accept=".json" class="input">
                            @error('preset_file')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                Upload a preset JSON file exported from another instance.
                            </p>
                        </div>
                        <x-forms.button type="submit" canGate="update" :canResource="$settings">
                            Import Preset
                        </x-forms.button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
