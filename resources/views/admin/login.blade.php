<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-100 font-sans">
    <!-- Navigation Bar -->
    <nav class="bg-blue-600 text-white p-4">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">Admin Login</h1>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-8 p-4">
      <h2 class="text-2xl font-semibold mb-4 text-center">Enter Admin Panel</h2>
      @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
      <form class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto" action="{{ route('admin.login.submit') }}" method="POST">
        @csrf
        <!-- Email -->
        <div class="mb-4">
          <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            class="w-full px-4 py-2 border rounded-lg"
            required
          />
        </div>
        <!-- Password -->
        <div class="mb-4">
          <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            class="w-full px-4 py-2 border rounded-lg"
            required
          />
        </div>
        <!-- Submit Button -->
        <button
          type="submit"
          class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 w-full"
        >
          Login
        </button>
      </form>
    </div>
  </body>
</html>
