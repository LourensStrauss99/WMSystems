


<x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
    <div class="container py-4">
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('settings') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> {{ __('Back to Settings') }}
                </a>
            </div>
        </div>
        @include('partials.settings-heading')
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form wire:submit="updateProfileInformation" class="row g-3">
                            <div class="col-12">
                                <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />
                            </div>
                            <div class="col-12">
                                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
                                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                                    <div class="alert alert-warning mt-2">
                                        {{ __('Your email address is unverified.') }}
                                        <flux:link class="text-sm cursor-pointer ms-2" wire:click.prevent="resendVerificationNotification">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </flux:link>
                                        @if (session('status') === 'verification-link-sent')
                                            <div class="mt-2 text-success">
                                                {{ __('A new verification link has been sent to your email address.') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <flux:input wire:model="telephone" :label="__('Telephone')" type="text" />
                            </div>
                            <div class="col-md-6">
                                <flux:input wire:model="address" :label="__('Address')" type="text" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Badge Photo') }}</label>
                                <input type="file" wire:model="photo" accept="image/*" class="form-control" />
                                @if(auth()->user()->photo)
                                    <img src="{{ asset('storage/' . auth()->user()->photo) }}"
                                         alt="Badge Photo"
                                         class="mt-2 rounded-circle border border-primary shadow"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                @endif
                                @error('photo') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-primary w-50">
                                    {{ __('Save') }}
                                </button>
                                <x-action-message class="ms-3" on="profile-updated">
                                    {{ __('Saved.') }}
                                </x-action-message>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mt-4">
                    <livewire:settings.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-settings.layout>

