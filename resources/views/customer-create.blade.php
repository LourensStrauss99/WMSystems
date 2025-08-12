{{-- filepath: resources/views/customer-create.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Customer</title>
    <link rel="stylesheet" href="{{ global_asset('style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mx-auto py-8">
        <a href="{{ route('customers.index') }}" class="text-blue-500 hover:underline mb-4 inline-block">&larr; Back to Customers</a>
        <div class="bg-white p-8 rounded shadow max-w-lg mx-auto">
            <h2 class="text-2xl font-semibold mb-4">Add Customer</h2>
            <form action="{{ route('client.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1">Business Name</label>
                    <input type="text" name="business_name" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Name</label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Surname</label>
                    <input type="text" name="surname" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Telephone</label>
                    <input type="tel" name="telephone" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Office Number</label>
                    <input type="tel" name="office_number" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Address</label>
                    <input type="text" name="address" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Email</label>
                    <input type="email" name="email" class="w-full border rounded px-3 py-2" required id="emailInput">
                    <span id="emailWarning" class="text-red-500 text-sm hidden">Please enter a valid email address.</span>
                </div>
                <button type="submit" class="bg-blue-400 text-white px-4 py-2 rounded hover:bg-blue-500">Save</button>
            </form>
        </div>
    </div>
    <script>
document.querySelectorAll('input[name="telephone"], input[name="office_number"]').forEach(function(input) {
    input.addEventListener('input', function() {
        // Allow only one leading +, then digits
        let value = this.value.replace(/[^0-9+]/g, '');
        // Ensure only one + at the start
        value = value.replace(/(?!^)\+/g, '');
        if (value.indexOf('+') > 0) {
            value = value.replace(/\+/g, '');
        }
        this.value = value;
    });
});

// Email validation
const emailInput = document.getElementById('emailInput');
const emailWarning = document.getElementById('emailWarning');
if (emailInput) {
    emailInput.addEventListener('input', function() {
        const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
        if (!valid && this.value.length > 0) {
            emailWarning.classList.remove('hidden');
        } else {
            emailWarning.classList.add('hidden');
        }
    });
}
</script>
</body>
</html>