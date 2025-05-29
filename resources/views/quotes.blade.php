@extends('layouts.app')
@extends('layouts.nav')
@section(section: 'content')


<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="UTF-8">
    <title>Qoutes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="container">
      

      <!-- Main Content -->
     

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
@endsection