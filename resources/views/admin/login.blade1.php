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
      <form id="adminLoginForm" class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto" action="Admin-Panel.html" method="POST">
        <!-- Username -->
        <div class="mb-4">
          <label for="username" class="block text-gray-700 font-bold mb-2">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
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
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"
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
