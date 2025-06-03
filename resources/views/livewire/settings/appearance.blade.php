<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <!--<flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun" icon-class="w-8 h-8">{{ __('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon" icon-class="w-8 h-8">{{ __('Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop" icon-class="w-8 h-8">{{ __('System') }}</flux:radio>
        </flux:radio.group>-->
    </x-settings.layout>

    <!-- Example for settings.appearance Livewire component -->
    <!--<div class="flex justify-center gap-4 mt-4">
        <img src="..." class="w-8 h-8" />
        <img src="..." class="w-8 h-8" />
        <img src="..." class="w-8 h-8" />
    </div>-->
</section>
