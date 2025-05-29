<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client</title>
    <link rel="stylesheet" href="/style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body>
     <header class="header"
      <div class="logo">Client</div>
      <nav class="tabs">
        <a href="/client" class="tab-button">1 - Client</a>
            <a href="/jobcard" class="tab-button">2 - Jobcard</a>
            <a href="/invoice" class="tab-button">3 - Invoices</a>
            <a href="/inventory" class="tab-button">4 - Inventory</a>
            <a href="/reports" class="tab-button">5 - Reports</a>
            <a href="/progress" class="tab-button">6 - Progress</a>
            <a href="/quotes" class="tab-button">7 - Quotes</a>
            <a href="/settings" class="tab-button">8 - Settings</a>

      </nav>
    </header>
   
    <div class="main-content">
        <div class="client-container">
            <livewire:client-form />
          
        </div>
    </div>
    
    @livewireScripts
</body>
</html>
