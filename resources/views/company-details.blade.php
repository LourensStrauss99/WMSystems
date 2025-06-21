{{-- filepath: resources/views/company-details.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Details - Workflow Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">
    <!-- Enhanced Navigation -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-building text-2xl"></i>
                    <h1 class="text-xl font-bold">Company Details</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/master-settings" class="hover:bg-blue-700 px-3 py-2 rounded transition-colors">
                        <i class="fas fa-cog mr-2"></i>Master Settings
                    </a>
                    <a href="/dashboard" class="hover:bg-blue-700 px-3 py-2 rounded transition-colors">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8 p-4 max-w-6xl">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span class="font-semibold">Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Form -->
        <form method="POST" action="{{ route('company.details.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Business Settings Section -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b border-gray-200 pb-3">
                    <i class="fas fa-chart-line mr-3 text-blue-600"></i>Business Settings
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-tools mr-2 text-gray-600"></i>Labour Rate (per hour) *
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">R</span>
                            <input type="number" step="0.01" name="labour_rate" 
                                   value="{{ old('labour_rate', $companyDetails->labour_rate ?? '') }}"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                   placeholder="0.00" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-percent mr-2 text-gray-600"></i>VAT Percentage *
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" name="vat_percent" 
                                   value="{{ old('vat_percent', $companyDetails->vat_percent ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                   placeholder="15.00" required>
                            <span class="absolute right-3 top-2 text-gray-500">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Information Section -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b border-gray-200 pb-3">
                    <i class="fas fa-building mr-3 text-blue-600"></i>Company Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-tag mr-2 text-gray-600"></i>Company Name *
                        </label>
                        <input type="text" name="company_name" 
                               value="{{ old('company_name', $companyDetails->company_name ?? '') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Your Company Name" required>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-certificate mr-2 text-gray-600"></i>Company Registration Number
                        </label>
                        <input type="text" name="company_reg_number" 
                               value="{{ old('company_reg_number', $companyDetails->company_reg_number ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="2021/123456/07">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-receipt mr-2 text-gray-600"></i>VAT Registration Number
                        </label>
                        <input type="text" name="vat_reg_number" 
                               value="{{ old('vat_reg_number', $companyDetails->vat_reg_number ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="4123456789">
                    </div>
                </div>
            </div>

            <!-- Banking Details Section -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b border-gray-200 pb-3">
                    <i class="fas fa-university mr-3 text-blue-600"></i>Banking Details
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-landmark mr-2 text-gray-600"></i>Bank Name
                        </label>
                        <select name="bank_name" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Bank</option>
                            <option value="ABSA" {{ old('bank_name', $companyDetails->bank_name ?? '') == 'ABSA' ? 'selected' : '' }}>ABSA</option>
                            <option value="Standard Bank" {{ old('bank_name', $companyDetails->bank_name ?? '') == 'Standard Bank' ? 'selected' : '' }}>Standard Bank</option>
                            <option value="FNB" {{ old('bank_name', $companyDetails->bank_name ?? '') == 'FNB' ? 'selected' : '' }}>FNB</option>
                            <option value="Nedbank" {{ old('bank_name', $companyDetails->bank_name ?? '') == 'Nedbank' ? 'selected' : '' }}>Nedbank</option>
                            <option value="Capitec" {{ old('bank_name', $companyDetails->bank_name ?? '') == 'Capitec' ? 'selected' : '' }}>Capitec</option>
                            <option value="Other" {{ old('bank_name', $companyDetails->bank_name ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-user mr-2 text-gray-600"></i>Account Holder
                        </label>
                        <input type="text" name="account_holder" 
                               value="{{ old('account_holder', $companyDetails->account_holder ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Account holder name">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-credit-card mr-2 text-gray-600"></i>Account Number
                        </label>
                        <input type="text" name="account_number" 
                               value="{{ old('account_number', $companyDetails->account_number ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="1234567890">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-code-branch mr-2 text-gray-600"></i>Branch Code
                        </label>
                        <input type="text" name="branch_code" 
                               value="{{ old('branch_code', $companyDetails->branch_code ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="250655">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-globe mr-2 text-gray-600"></i>SWIFT/BIC Code
                        </label>
                        <input type="text" name="swift_code" 
                               value="{{ old('swift_code', $companyDetails->swift_code ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="ABSAZAJJ">
                    </div>
                </div>
            </div>

            <!-- Address Details Section -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b border-gray-200 pb-3">
                    <i class="fas fa-map-marker-alt mr-3 text-blue-600"></i>Address Details
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-home mr-2 text-gray-600"></i>Physical Address
                        </label>
                        <textarea name="address" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                  placeholder="Street address, building number, etc.">{{ old('address', $companyDetails->address ?? '') }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-city mr-2 text-gray-600"></i>City
                        </label>
                        <input type="text" name="city" 
                               value="{{ old('city', $companyDetails->city ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="City name">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-flag mr-2 text-gray-600"></i>Province/State
                        </label>
                        <select name="province" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Province</option>
                            <option value="Western Cape" {{ old('province', $companyDetails->province ?? '') == 'Western Cape' ? 'selected' : '' }}>Western Cape</option>
                            <option value="Eastern Cape" {{ old('province', $companyDetails->province ?? '') == 'Eastern Cape' ? 'selected' : '' }}>Eastern Cape</option>
                            <option value="Northern Cape" {{ old('province', $companyDetails->province ?? '') == 'Northern Cape' ? 'selected' : '' }}>Northern Cape</option>
                            <option value="Free State" {{ old('province', $companyDetails->province ?? '') == 'Free State' ? 'selected' : '' }}>Free State</option>
                            <option value="KwaZulu-Natal" {{ old('province', $companyDetails->province ?? '') == 'KwaZulu-Natal' ? 'selected' : '' }}>KwaZulu-Natal</option>
                            <option value="North West" {{ old('province', $companyDetails->province ?? '') == 'North West' ? 'selected' : '' }}>North West</option>
                            <option value="Gauteng" {{ old('province', $companyDetails->province ?? '') == 'Gauteng' ? 'selected' : '' }}>Gauteng</option>
                            <option value="Mpumalanga" {{ old('province', $companyDetails->province ?? '') == 'Mpumalanga' ? 'selected' : '' }}>Mpumalanga</option>
                            <option value="Limpopo" {{ old('province', $companyDetails->province ?? '') == 'Limpopo' ? 'selected' : '' }}>Limpopo</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-mail-bulk mr-2 text-gray-600"></i>Postal/ZIP Code
                        </label>
                        <input type="text" name="postal_code" 
                               value="{{ old('postal_code', $companyDetails->postal_code ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="7925">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-globe-africa mr-2 text-gray-600"></i>Country
                        </label>
                        <input type="text" name="country" 
                               value="{{ old('country', $companyDetails->country ?? 'South Africa') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="South Africa">
                    </div>
                </div>
            </div>

            <!-- Contact Details Section -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b border-gray-200 pb-3">
                    <i class="fas fa-address-book mr-3 text-blue-600"></i>Contact Details
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-phone mr-2 text-gray-600"></i>Telephone
                        </label>
                        <input type="text" name="company_telephone" 
                               value="{{ old('company_telephone', $companyDetails->company_telephone ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="+27 21 123 4567">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-envelope mr-2 text-gray-600"></i>Email
                        </label>
                        <input type="email" name="company_email" 
                               value="{{ old('company_email', $companyDetails->company_email ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="info@company.com">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-link mr-2 text-gray-600"></i>Website
                        </label>
                        <input type="url" name="company_website" 
                               value="{{ old('company_website', $companyDetails->company_website ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="https://www.company.com">
                    </div>
                </div>
            </div>

            <!-- Invoice Settings Section -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b border-gray-200 pb-3">
                    <i class="fas fa-file-invoice mr-3 text-blue-600"></i>Invoice Settings
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-handshake mr-2 text-gray-600"></i>Default Invoice Terms
                        </label>
                        <input type="text" name="invoice_terms" 
                               value="{{ old('invoice_terms', $companyDetails->invoice_terms ?? 'Payment due within 30 days') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               placeholder="Payment due within 30 days">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-sticky-note mr-2 text-gray-600"></i>Invoice Footer/Notes
                        </label>
                        <textarea name="invoice_footer" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                  placeholder="Thank you for your business! For any queries, please contact us.">{{ old('invoice_footer', $companyDetails->invoice_footer ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Company Logo Section -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b border-gray-200 pb-3">
                    <i class="fas fa-image mr-3 text-blue-600"></i>Company Logo
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            <i class="fas fa-upload mr-2 text-gray-600"></i>Upload Logo
                        </label>
                        <input type="file" name="company_logo" accept="image/*" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <small class="text-gray-600 mt-1 block">Recommended: PNG or JPG, max 2MB, square format works best</small>
                    </div>
                    
                    @if(!empty($companyDetails->company_logo ?? ''))
                        <div class="mt-4">
                            <p class="text-gray-700 font-semibold mb-2">Current Logo:</p>
                            <div class="bg-gray-50 p-4 rounded-lg border-2 border-dashed border-gray-300 inline-block">
                                <img src="{{ asset('storage/' . $companyDetails->company_logo) }}"
                                     alt="Company Logo"
                                     class="h-32 w-auto rounded shadow-md border">
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="flex justify-between items-center">
                    <a href="/master-settings" class="bg-gray-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-600 transition-colors font-semibold">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Master Settings
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg shadow-md hover:bg-blue-700 transition-colors font-semibold">
                        <i class="fas fa-save mr-2"></i>Save Company Details
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading state to form submission
            const form = document.querySelector('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            form.addEventListener('submit', function(e) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
                submitBtn.disabled = true;
            });
            
            // Auto-format phone numbers
            const phoneInput = document.querySelector('input[name="company_telephone"]');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.startsWith('27')) {
                        value = '+' + value;
                    } else if (value.startsWith('0')) {
                        value = '+27' + value.substring(1);
                    }
                    // Format as +27 XX XXX XXXX
                    if (value.length > 3) {
                        value = value.substring(0, 3) + ' ' + value.substring(3);
                    }
                    if (value.length > 6) {
                        value = value.substring(0, 6) + ' ' + value.substring(6);
                    }
                    if (value.length > 10) {
                        value = value.substring(0, 10) + ' ' + value.substring(10);
                    }
                    e.target.value = value;
                });
            }
            
            // Auto-format VAT number
            const vatInput = document.querySelector('input[name="vat_reg_number"]');
            if (vatInput) {
                vatInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 10) {
                        value = value.substring(0, 10);
                    }
                    e.target.value = value;
                });
            }
            
            // Preview logo before upload
            const logoInput = document.querySelector('input[name="company_logo"]');
            if (logoInput) {
                logoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Create preview if it doesn't exist
                            let preview = document.getElementById('logo-preview');
                            if (!preview) {
                                preview = document.createElement('div');
                                preview.id = 'logo-preview';
                                preview.className = 'mt-4';
                                logoInput.parentNode.appendChild(preview);
                            }
                            preview.innerHTML = `
                                <p class="text-gray-700 font-semibold mb-2">Preview:</p>
                                <div class="bg-gray-50 p-4 rounded-lg border-2 border-dashed border-gray-300 inline-block">
                                    <img src="${e.target.result}" alt="Logo Preview" class="h-32 w-auto rounded shadow-md border">
                                </div>
                            `;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</body>
</html>