{{-- filepath: resources/views/company-details.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Company Details</h1>
            <a href="/dashboard" class="text-white hover:underline">Dashboard</a>
        </div>
    </nav>
    <div class="container mx-auto mt-8 p-4">
        <h2 class="text-2xl font-semibold mb-4 mt-8">Invoice & Company Settings</h2>
        <form method="POST" action="{{ route('company.details.update') }}" class="bg-white p-6 rounded-lg shadow-md mb-8">
            @csrf
            @method('PUT')
            <!-- Master Settings: Invoice & Company Details -->
<h2 class="text-2xl font-semibold mb-4 mt-8">Invoice & Company Settings</h2>
<form method="POST" action="{{ route('master.settings.update') }}" class="bg-white p-6 rounded-lg shadow-md mb-8">
    @csrf
    @method('PUT')

    <!-- Labour Rate -->
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Labour Rate (per hour)</label>
        <input type="number" step="0.01" name="labour_rate" class="w-full px-4 py-2 border rounded-lg" required>
    </div>

    <!-- VAT Percentage -->
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">VAT %</label>
        <input type="number" step="0.01" name="vat_percent" class="w-full px-4 py-2 border rounded-lg" required>
    </div>

    <!-- Company Details -->
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Company Name</label>
        <input type="text" name="company_name" value="{{ old('company_name', $companyDetails->company_name ?? '') }}" class="w-full px-4 py-2 border rounded-lg" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Company Registration Number</label>
        <input type="text" name="company_reg_number" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">VAT Registration Number</label>
        <input type="text" name="vat_reg_number" class="w-full px-4 py-2 border rounded-lg">
    </div>

    <!-- Banking Details -->
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Bank Name</label>
        <input type="text" name="bank_name" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Account Holder</label>
        <input type="text" name="account_holder" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Account Number</label>
        <input type="text" name="account_number" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Branch Code</label>
        <input type="text" name="branch_code" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">SWIFT/BIC Code</label>
        <input type="text" name="swift_code" class="w-full px-4 py-2 border rounded-lg">
    </div>

    <!-- Address Details -->
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Physical Address</label>
        <input type="text" name="address" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">City</label>
        <input type="text" name="city" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Province/State</label>
        <input type="text" name="province" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Postal/ZIP Code</label>
        <input type="text" name="postal_code" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Country</label>
        <input type="text" name="country" class="w-full px-4 py-2 border rounded-lg">
    </div>

    <!-- Contact Details -->
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Telephone</label>
        <input type="text" name="company_telephone" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Email</label>
        <input type="email" name="company_email" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Website</label>
        <input type="text" name="company_website" class="w-full px-4 py-2 border rounded-lg">
    </div>

    <!-- Invoice Terms & Notes -->
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Default Invoice Terms</label>
        <input type="text" name="invoice_terms" class="w-full px-4 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Invoice Footer/Notes</label>
        <textarea name="invoice_footer" class="w-full px-4 py-2 border rounded-lg"></textarea>
    </div>

    <!-- Company Logo Upload -->
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Company Logo</label>
        <input type="file" name="company_logo" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
        @if(!empty($companyDetails->company_logo))
            <div class="mt-2">
                <img src="{{ asset('storage/' . $companyDetails->company_logo) }}"
                     alt="Company Logo"
                     class="h-24 rounded shadow border">
            </div>
        @endif
    </div>

    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700">Save Settings</button>
</form>

            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700">Save Settings</button>
        </form>
    </div>
</body>
</html>