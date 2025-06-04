{{-- filepath: resources/views/livewire/settings/appearance.blade.php --}}
<section class="w-full">
    <div class="mb-4">
        <a href="{{ route('settings') }}"
           class="inline-flex items-center px-3 py-2 rounded border border-gray-300 bg-white text-black font-semibold shadow hover:bg-gray-100 transition"
           title="Back to Settings">
            <i class="bi bi-arrow-left" style="font-size: 1.3rem; color: #222;"></i>
            <span class="ml-2">Back to Settings</span>
        </a>
    </div>

    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <div class="flex flex-col items-start gap-6 mt-8">
            <label class="font-semibold mb-2">{{ __('Theme') }}</label>
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
                <flux:radio value="light" icon="sun" icon-class="w-8 h-8">{{ __('Light') }}</flux:radio>
                <flux:radio value="dark" icon="moon" icon-class="w-8 h-8">{{ __('Dark') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop" icon-class="w-8 h-8">{{ __('System') }}</flux:radio>
            </flux:radio.group>
        </div>
    </x-settings.layout>
</section>
