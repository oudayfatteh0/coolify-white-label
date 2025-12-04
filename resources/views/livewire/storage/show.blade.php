<div>
    <x-slot:title>
        {{ data_get_str($storage, 'name')->limit(10) }} >Storages | {{ branding()->productName() }}
    </x-slot>
    <livewire:storage.form :storage="$storage" />
</div>
