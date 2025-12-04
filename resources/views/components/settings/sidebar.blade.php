<div class="flex flex-col items-start gap-2 min-w-fit">
    <a class="menu-item {{ $activeMenu === 'general' ? 'menu-item-active' : '' }}"
        href="{{ route('settings.index') }}">General</a>
    <a class="menu-item {{ $activeMenu === 'branding' ? 'menu-item-active' : '' }}"
        href="{{ route('settings.branding') }}">Branding</a>
    <a class="menu-item {{ $activeMenu === 'email_templates' ? 'menu-item-active' : '' }}"
        href="{{ route('settings.email-templates') }}">Email Templates</a>
    <a class="menu-item {{ $activeMenu === 'advanced' ? 'menu-item-active' : '' }}"
        href="{{ route('settings.advanced') }}">Advanced</a>
    {{-- Updates menu disabled for forked version --}}
    {{-- <a class="menu-item {{ $activeMenu === 'updates' ? 'menu-item-active' : '' }}"
        href="{{ route('settings.updates') }}">Updates</a> --}}
</div>
