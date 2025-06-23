{{-- filepath: resources/views/livewire/client-form.blade.php --}}
<div class="bg-white rounded-lg shadow-lg p-6 client-form-component">
    {{-- Success Message --}}
    @if ($successMessage)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ $successMessage }}</span>
            </div>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="font-semibold">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Header -->
    <div class="mb-6 pb-4 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-plus-circle mr-3 text-blue-600"></i>
            Create New Jobcard
        </h2>
        <!--<p class="text-gray-600 mt-2">Create a new jobcard for an existing client</p>-->
    </div>

    <form wire:submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column - Job Info & Client Selection -->
            <div class="space-y-6">
                <!-- Job Information Section -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 form-section">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                        Job Information
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="jobcard_number" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hashtag mr-1 text-gray-500"></i>Jobcard Number
                            </label>
                            <input type="text" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-100 form-input" 
                                   id="jobcard_number" 
                                   wire:model="jobcard_number" 
                                   readonly>
                        </div>
                        
                        <div>
                            <label for="job_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1 text-gray-500"></i>Job Date
                            </label>
                            <input type="date" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors form-input" 
                                   id="job_date" 
                                   wire:model="job_date">
                        </div>
                    </div>
                </div>

                <!-- Client Selection Section -->
                <div class="bg-blue-50 p-6 rounded-lg border border-blue-200 form-section">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-users mr-2 text-blue-600"></i>
                        Client Selection
                    </h3>
                    
                    <div class="mb-6">
                        <label for="clientId" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-check mr-1 text-gray-500"></i>Select Client <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors form-input" 
                                id="clientId" 
                                wire:model.live="clientId"
                                required>
                            <option value="">-- Select a Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">
                                    {{ $client->name }} {{ $client->surname }}
                                    @if($client->email) - {{ $client->email }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Selected Client Info (if client selected) -->
                    @if($clientId)
                        @php
                            $selectedClient = $clients->find($clientId);
                        @endphp
                        @if($selectedClient)
                            <div class="bg-white p-6 rounded-lg border-2 border-green-300 shadow-sm">
                                <h4 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                                    <i class="fas fa-user-check mr-2 text-green-600"></i>
                                    Selected Client Details
                                </h4>
                                
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="flex items-center p-3 bg-green-50 rounded-lg">
                                        <i class="fas fa-user mr-3 text-green-600 text-lg"></i>
                                        <div>
                                            <span class="block text-sm font-medium text-gray-700">Full Name</span>
                                            <span class="text-lg font-semibold text-gray-900">{{ $selectedClient->name }} {{ $selectedClient->surname }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($selectedClient->email)
                                        <div class="flex items-center p-3 bg-green-50 rounded-lg">
                                            <i class="fas fa-envelope mr-3 text-green-600 text-lg"></i>
                                            <div>
                                                <span class="block text-sm font-medium text-gray-700">Email Address</span>
                                                <span class="text-base text-gray-900">{{ $selectedClient->email }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($selectedClient->telephone)
                                        <div class="flex items-center p-3 bg-green-50 rounded-lg">
                                            <i class="fas fa-phone mr-3 text-green-600 text-lg"></i>
                                            <div>
                                                <span class="block text-sm font-medium text-gray-700">Phone Number</span>
                                                <span class="text-base text-gray-900">{{ $selectedClient->telephone }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($selectedClient->address)
                                        <div class="flex items-start p-3 bg-green-50 rounded-lg">
                                            <i class="fas fa-map-marker-alt mr-3 text-green-600 text-lg mt-1"></i>
                                            <div>
                                                <span class="block text-sm font-medium text-gray-700">Address</span>
                                                <span class="text-base text-gray-900">{{ $selectedClient->address }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Right Column - Work Request -->
            <div class="space-y-6">
                <!-- Work Request Section -->
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200 h-full form-section">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tools mr-2 text-orange-600"></i>
                        Work Details
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="work_request" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clipboard-list mr-1 text-gray-500"></i>
                                Work Request <span class="text-red-500">*</span>
                            </label>
                            <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none form-textarea" 
                                      id="work_request" 
                                      wire:model="work_request" 
                                      rows="16"
                                      placeholder="Describe the work that needs to be done..."
                                      required></textarea>
                        </div>
                        
                        <div>
                            <label for="special_request" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-star mr-1 text-gray-500"></i>Special Instructions
                            </label>
                            <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none form-textarea" 
                                      id="special_request" 
                                      wire:model="special_request" 
                                      rows="16"
                                      placeholder="Any special instructions or requirements..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Fields marked with <span class="text-red-500">*</span> are required
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('jobcard.index') }}" 
                   class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-medium shadow-sm flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 font-semibold shadow-lg flex items-center form-submit-btn"
                        {{ !$clientId ? 'disabled' : '' }}>
                    <i class="fas fa-save mr-2"></i>
                    Create Jobcard
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
/* Enhanced focus states */
.client-form-component .form-input:focus, 
.client-form-component .form-textarea:focus {
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3) !important;
}

/* Button hover animation */
.client-form-component .form-submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4) !important;
}

/* Disabled button state */
.client-form-component .form-submit-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
}

/* Form section animations */
.client-form-component .form-section {
    transition: all 0.3s ease;
}

.client-form-component .form-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Input animations */
.client-form-component .form-input, 
.client-form-component .form-textarea {
    transition: all 0.3s ease;
}

.client-form-component .form-input:hover, 
.client-form-component .form-textarea:hover {
    border-color: #60a5fa;
}

/* Success message animation */
.client-form-component .bg-green-100 {
    animation: slideIn 0.5s ease-out;
}

.client-form-component .bg-red-100 {
    animation: slideIn 0.5s ease-out;
}

/* Client details section animation */
.client-form-component .border-green-300 {
    animation: slideInClient 0.6s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInClient {
    from {
        opacity: 0;
        transform: translateY(-15px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Section headers */
.client-form-component h3 {
    font-weight: 600;
    color: #374151;
}

.client-form-component h4 {
    font-weight: 600;
    color: #065f46;
}

/* Placeholder styling */
.client-form-component ::placeholder {
    color: #9ca3af;
    opacity: 1;
}

/* Cancel button styling */
.client-form-component .bg-gray-500:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
}

/* Required field styling */
.client-form-component .text-red-500 {
    font-weight: 600;
}

/* Enhanced client details styling */
.client-form-component .border-green-300 {
    box-shadow: 0 4px 20px rgba(34, 197, 94, 0.1);
}

.client-form-component .bg-green-50 {
    transition: all 0.2s ease;
}

.client-form-component .bg-green-50:hover {
    background-color: #dcfce7;
    transform: translateX(4px);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .client-form-component .grid-cols-1.lg\\:grid-cols-2 {
        gap: 1.5rem;
    }
    
    .client-form-component .flex.space-x-3 {
        flex-direction: column;
        space-x: 0;
        gap: 0.75rem;
    }
    
    .client-form-component .justify-between {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .client-form-component .grid.gap-4 .flex {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .client-form-component .grid.gap-4 .flex i {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush