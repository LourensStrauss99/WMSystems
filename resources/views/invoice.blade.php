<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Invoices</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
      /* Modal for invoice view */
      .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        overflow: auto;
      }
      .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 800px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      }
      .modal-content h2 {
        margin-top: 0;
        text-align: center;
      }
      .modal-content .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
      }
      .modal-content .close:hover,
      .modal-content .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
      }
      .modal-content .form-group {
        margin-bottom: 15px;
      }
      .modal-content label {
        display: block;
        font-weight: bold;
      }
      .modal-content input,
      .modal-content textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
      }
      .modal-content textarea {
        resize: vertical;
        min-height: 100px;
      }
      .modal-content table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
      }
      .modal-content th,
      .modal-content td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
      }
      .modal-content th {
        background-color: #f2f2f2;
      }
      .modal-content .invoice-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
      }
      .modal-content .invoice-total {
        margin-top: 20px;
        text-align: right;
      }
      .modal-content .invoice-total div {
        margin: 5px 0;
      }
      /* Highlight selected row */
      #invoices-table-body tr.selected {
        background-color: #d1e7ff;
        font-weight: bold;
      }
      #invoices-table-body tr:hover {
        background-color: #f0f0f0;
        cursor: pointer;
      }
      /* Scrollable table */
      .invoices {
        max-height: 400px;
        overflow-y: auto;
      }
      .invoices table {
        width: 100%;
      }
      /* Date range search */
      .date-search {
        margin-top: 10px;
        display: flex;
        gap: 10px;
        align-items: center;
      }
      .date-search label {
        font-weight: bold;
      }
      .date-search input[type="date"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
      }
      @media print {
        .modal {
          background-color: transparent;
        }
        .modal-content {
          margin: 0;
          width: 100%;
          box-shadow: none;
          border: none;
        }
        .no-print {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <!-- Header -->
      <header class="header">
        <div class="logo">Invoices</div>
        <!-- Tabs -->
        <nav class="tabs">
          <a href="/client" class="tab-button">1 - Client</a>
          <a href="/jobcard" class="tab-button">2 - Jobcard</a>
          <a href="/progress" class="tab-button">3 - Progress</a>
          <a href="/invoice" class="tab-button active">4 - Invoices</a>
          <a href="/artisanprogress" class="tab-button">5 - Artisan progress</a>
          <a href="/inventory" class="tab-button">6 - Inventory</a>
          <a href="/reports" class="tab-button">7 - Reports</a>
          <a href="/quotes" class="tab-button">8 - Quotes</a>
          <a href="/settings" class="tab-button">9 - Settings</a>
        </nav>
      </header>

      <!-- Main Content -->
      <div class="left-section">
        <div class="form-group">
          <div class="main-content">
            <!-- Invoices Table View -->
            <div id="invoices-table-view">
              <!-- Search Bar -->
              <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input
                  type="text"
                  id="search-invoices"
                  placeholder="Search by client name..."
                  onkeyup="filterInvoices()"
                />
              </div>
              <!-- Date Range Search -->
              <div class="date-search">
                <label for="start-date">Start Date:</label>
                <input type="date" id="start-date" onchange="loadInvoices()" />
                <label for="end-date">End Date:</label>
                <input type="date" id="end-date" onchange="loadInvoices()" />
              </div>

              <!-- Invoices Table -->
              <div class="container-large">
                <div class="invoices">
                  <h3><i><u><b>Invoices</b></u></i></h3>
                  <table>
                    <thead>
                      <tr>
                        <th>Jobcard Number</th>
                        <th>Client Name</th>
                        <th>Job Date</th>
                      </tr>
                    </thead>
                    <tbody id="invoices-table-body">
                      <!-- Invoices populated by JS -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Invoice View -->
    <div id="invoice-modal" class="modal">
      <div class="modal-content">
        <span class="close no-print" onclick="closeInvoice()">×</span>
        <h2>Invoice</h2>
        <div class="invoice-header">
          <div>
            <strong>WCC</strong><br />
            123 Business St, City<br />
            Phone: 555-1234<br />
            Email: info@wcc.com
          </div>
          <div>
            <strong>Invoice Details</strong><br />
            Jobcard Number: <span id="view-jobcard-number"></span><br />
            Job Date: <span id="view-job-date"></span>
          </div>
        </div>
        <div class="form-group">
          <label>Client Details</label>
          <div>Name: <span id="view-client-name"></span></div>
          <div>Address: <span id="view-client-address"></span></div>
          <div>Telephone: <span id="view-client-telephone"></span></div>
          <div>Email: <span id="view-client-email"></span></div>
        </div>
        <div class="form-group">
          <label>Work Details</label>
          <div>Hours Worked: <span id="view-hours"></span></div>
          <div>Hourly Rate: <span id="view-hourly-rate">$50.00</span></div>
          <div>Work Done: <span id="view-work-done"></span></div>
        </div>
        <div class="form-group">
          <label>Spares Used</label>
          <table id="view-spares-table">
            <thead>
              <tr>
                <th>Spare Name</th>
                <th>Part Number</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody id="view-spares-table-body"></tbody>
          </table>
        </div>
        <div class="invoice-total">
          <div>Subtotal: <span id="view-subtotal">$0.00</span></div>
          <div>VAT (15%): <span id="view-vat">$0.00</span></div>
          <div><strong>Total: <span id="view-total">$0.00</span></strong></div>
        </div>
      </div>
    </div>

    <script src="../src/js/invoice.js"></script>
  </body>
</html>