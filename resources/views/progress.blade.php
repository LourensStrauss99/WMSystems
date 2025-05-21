<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
      /* Modal for job card view */
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
      /* Highlight selected row */
      #assigned-jobcards-table-body tr.selected {
        background-color: #d1e7ff;
        font-weight: bold;
      }
      #assigned-jobcards-table-body tr:hover {
        background-color: #f0f0f0;
        cursor: pointer;
      }
      /* Scrollable table */
      .assigned-jobcards {
        max-height: 400px;
        overflow-y: auto;
      }
      .assigned-jobcards table {
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
        <div class="logo">Progress</div>
        <!-- Tabs -->
        <nav class="tabs">
          <a
          <a href="/client" class="tab-button">1 - Client</a>
          <a href="/jobcard" class="tab-button">2 - Jobcard</a>
          <a href="/progress" class="tab-button active">3 - Progress</a>
          <a href="/invoice" class="tab-button">4 - Invoices</a>
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
            <!-- Jobcards Table View -->
            <div id="jobcards-table-view">
              <!-- Search Bar -->
              <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input
                  type="text"
                  id="search-jobcards"
                  placeholder="Search by /client or artisan name..."
                  onkeyup="filterJobcards()"
                />
              </div>
              <!-- Date Range Search -->
              <div class="date-search">
                <label for="start-date">Start Date:</label>
                <input type="date" id="start-date" onchange="loadJobcards()" />
                <label for="end-date">End Date:</label>
                <input type="date" id="end-date" onchange="loadJobcards()" />
              </div>

              <!-- Assigned Jobcards Table -->
              <div class="container-large">
                <div class="assigned-jobcards">
                  <h3><i><u><b>Assigned Jobcards</b></u></i></h3>
                  <table>
                    <thead>
                      <tr>
                        <th>Jobcard Number</th>
                        <th>Client Name</th>
                        <th>Job Date</th>
                      </tr>
                    </thead>
                    <tbody id="assigned-jobcards-table-body">
                      <!-- Jobcards populated by JS -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Jobcard View -->
    <div id="jobcard-modal" class="modal">
      <div class="modal-content">
        <span class="close no-print" onclick="closeJobcard()">×</span>
        <h2>Jobcard Details</h2>
        <div class="form-group">
          <label>Jobcard Number</label>
          <input type="text" id="view-jobcard-number" readonly />
        </div>
        <div class="form-group">
          <label>Job Date</label>
          <input type="date" id="view-job-date" readonly />
        </div>
        <div class="form-group">
          <label>Client Name</label>
          <input type="text" id="view-/client-name" readonly />
        </div>
        <div class="form-group">
          <label>Telephone</label>
          <input type="text" id="view-/client-telephone" readonly />
        </div>
        <div class="form-group">
          <label>Address</label>
          <input type="text" id="view-/client-address" readonly />
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="text" id="view-/client-email" readonly />
        </div>
        <div class="form-group">
          <label>Category</label>
          <input type="text" id="view-category" readonly />
        </div>
        <div class="form-group">
          <label>Assigned Artisan</label>
          <input type="text" id="view-artisan" readonly />
        </div>
        <div class="form-group">
          <label>Work Request</label>
          <textarea id="view-work-request" readonly></textarea>
        </div>
        <div class="form-group">
          <label>Work Done</label>
          <textarea id="view-work-done" readonly></textarea>
        </div>
        <div class="form-group">
          <label>Hours</label>
          <input type="text" id="view-hours" readonly />
        </div>
        <div class="form-group">
          <label>Special Request</label>
          <textarea id="view-special-request" readonly></textarea>
        </div>
        <div class="form-group">
          <label>Spares Used</label>
          <table id="view-spares-table">
            <thead>
              <tr>
                <th>Spare Name</th>
                <th>Part Number</th>
                <th>Quantity</th>
              </tr>
            </thead>
            <tbody id="view-spares-table-body"></tbody>
          </table>
        </div>
      </div>
    </div>

    <script src="../src/js/progress.js"></script>
  </body>
</html>