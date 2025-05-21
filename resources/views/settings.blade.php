<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="container">
      <!-- Header -->
      <header class="header">
        <div class="logo">Settings</div>
        <!-- Tabs -->
        <nav class="tabs">
          <a href="/client" class="tab-button">1 - Client</a>
          <a href="/jobcard" class="tab-button">2 - Jobcard</a>
          <a href="/progress" class="tab-button">3 - Progress</a>
          <a href="/invoice" class="tab-button">4 - Invoices</a>
          <a href="/artisanprogress" class="tab-button">5 - Artisan progress</a>
          <a href="/inventory" class="tab-button">6 - Inventory</a>
          <a href="/reports" class="tab-button">7 - Reports</a>
          <a href="/quotes" class="tab-button">8 - Quotes</a>
          <a href="/settings" class="tab-button active">9 - Settings</a>
        </nav>
      </header>

      <!-- Main Content -->
    
      </main>
      <div class="footer">
        <button class="button active" id="save" onclick="saveInvoice()">
          Save
        </button>
        <button class="button" id="print" onclick="printInvoice()">Print</button>
        <button class="button" id="email" onclick="emailInvoice()">Email</button>
        <button class="button" id="delete" onclick="deleteInvoice()">
          Delete
        </button>
        <button class="button" id="refresh" onclick="refreshForm()">
          Refresh
        </button>
        <button class="button" id="exit" onclick="exitApp()">Exit</button>
      </div>

    <!-- Include the script.js file -->
    <script src="script.js"></script>
  </body>
</html>
