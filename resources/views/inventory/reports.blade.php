@extends('layouts.app')
@extends('layouts.nav')
@section(section: 'content')


<!DOCTYPE html>
<html lang="en">
 <!-- In login.html -->
<head>
    <meta charset="UTF-8">
    
    <link rel="stylesheet" href="style.css">


    <title>Reports</title>
  
      <!-- Main Content -->
   

        <div class="footer">
         
          </button>
          <button class="button" id="print" onclick="printInvoice()">Print</button>
          <button class="button" id="email" onclick="emailInvoice()">Email</button>
         
          </button>
          <button class="button" id="refresh" onclick="refreshForm()">
            Refresh
          </button>
         
        </div>

    <!-- Include the script.js file 
    <script src="script.js"></script>-->
  </body>
</html>
@endsection