<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Artisan Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <header class="header"
      <div class="logo">ArtisanProgress</div>
      <nav class="tabs">
        <a href="/client" class="tab-button">1 - Client</a>
            <a href="/jobcard" class="tab-button">2 - Jobcard</a>
            <a href="/progress" class="tab-button">3 - Progress</a>
            <a href="/invoice" class="tab-button">4 - Invoices</a>
            <a href="/artisanprogress" class="tab-button active">5 - Artisan progress</a>
            <a href="/inventory" class="tab-button">6 - Inventory</a>
            <a href="/reports" class="tab-button">7 - Reports</a>
            <a href="/quotes" class="tab-button">8 - Quotes</a>
            <a href="/settings" class="tab-button">9 - Settings</a>
      </nav>
    </header>

    <div class="main-content">
      <div class="client-container">
        <div class="left-section">
          <div class="form-group">
            <h1>Artisan</h1>
            <div class="card-content">
              <input
                type="text"
                id="job-description"
                name="job-description"
                required
              />
            </div>

            <h1>Progress</h1>
            <div class="card-content">
              <input
                type="text"
                id="job-description"
                name="job-description"
                required
              />
            </div>

            <h1>Date</h1>
            <div class="card-content">
              <input
                type="date"
                id="job-description"
                name="job-description"
                required
              />
            </div>

            <h1>Hours</h1>
            <div class="card-content">
              <input
                type="text"
                id="job-description"
                name="job-description"
                required
              />
        <div>
              <div class="right-section">
                <div class="work-request">
                  <label for="work-request">Jobs Assigned</label>
                  <textarea
                    id="work-request"
                    rows="5"
                    autocomplete="off"
                  ></textarea>
                </div>
                <div class="special-request">
                  <label for="special-request">Outstanding Jobs</label>
                  <textarea
                    id="special-request"
                    rows="5"
                    autocomplete="off"
                  ></textarea>
       </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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

    <script src="script.js"></script>
  </body>
</html>
