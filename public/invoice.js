document.addEventListener("DOMContentLoaded", function () {
  let selectedJobcardId = null;
  const HOURLY_RATE = 50.0; // Configurable hourly rate

  // Firebase configuration
  const firebaseConfig = {
    apiKey: "AIzaSyB7S2G-bfOCeILZlA-2DBFKBJakAXr3WpY",
    authDomain: "wmsystems-2af66.firebaseapp.com",
    projectId: "wmsystems-2af66",
    storageBucket: "wmsystems-2af66.firebasestorage.app",
    messagingSenderId: "560740833259",
    appId: "1:560740833259:web:22a3cb1365482e1daa2bae",
    measurementId: "G-1GWTS43TF3",
    vapidKey: "BD-wo4IqQMZX8NVuloW_c-gWLVKPrpyljtVeRTmkk07fZgEvcXwXglqNkyS7u4ulvxxEvFLM74HAlf4Otlgxm3Q",
  };

  // Utility function for API requests
  async function apiRequest(url, method = "GET", data = null) {
    const options = {
      method,
      headers: { "Content-Type": "application/json" },
    };
    if (data) options.body = JSON.stringify(data);

    const response = await fetch(url, options);
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    return response.json();
  }

  // Load job cards
  async function loadInvoices() {
    try {
      const startDate = document.getElementById("start-date").value;
      const endDate = document.getElementById("end-date").value;
      let url = "../src/php/get_jobcards.php";
      if (startDate || endDate) {
        const params = new URLSearchParams();
        if (startDate) params.append("start_date", startDate);
        if (endDate) params.append("end_date", endDate);
        url += `?${params.toString()}`;
      }
      console.log("Fetching job cards from:", url);
      const result = await apiRequest(url);

      if (result.success && result.data.length > 0) {
        populateInvoicesTable(result.data);
      } else {
        console.warn("No job cards found:", result.message || "Empty data");
        document.getElementById("invoices-table-body").innerHTML =
          '<tr><td colspan="3">No job cards found</td></tr>';
      }
    } catch (error) {
      console.error("Error loading job cards:", error);
      alert("Failed to load job cards: " + error.message);
    }
  }

  // Populate job cards table
  function populateInvoicesTable(jobcards) {
    const tbody = document.getElementById("invoices-table-body");
    tbody.innerHTML = "";
    jobcards.forEach((jobcard) => {
      const row = tbody.insertRow();
      row.innerHTML = `
        <td>${jobcard.jobcard_number}</td>
        <td>${jobcard.client_name} ${jobcard.client_surname}</td>
        <td>${jobcard.job_date}</td>
      `;
      row.dataset.jobcardId = jobcard.id;

      // Add event listeners for row selection and double-click
      row.addEventListener("dblclick", () => {
        document.querySelectorAll("#invoices-table-body tr").forEach((r) =>
          r.classList.remove("selected")
        );
        row.classList.add("selected");
        selectedJobcardId = jobcard.id;
        console.log("Double-clicked job card:", jobcard.id);
        openInvoice();
      });
    });
  }

  // Filter job cards by client name
  window.filterInvoices = function () {
    const input = document.getElementById("search-invoices").value.toLowerCase();
    const rows = document.querySelectorAll("#invoices-table-body tr");
    rows.forEach((row) => {
      const clientName = row.cells[1].textContent.toLowerCase();
      row.style.display = clientName.includes(input) ? "" : "none";
    });
  };

  // Open invoice modal
  window.openInvoice = async function () {
    if (!selectedJobcardId) {
      alert("Please select a job card");
      return;
    }
    try {
      const invoiceNumber = await fetchOrCreateInvoice(selectedJobcardId);
      const jobcardData = await fetchJobcardDetails(selectedJobcardId);
      const sparesData = await fetchSpares(selectedJobcardId);

      populateInvoiceModal(invoiceNumber, jobcardData, sparesData);
      document.getElementById("invoice-modal").style.display = "block";
    } catch (error) {
      console.error("Error opening invoice:", error);
      alert("Failed to load invoice: " + error.message);
    }
  };

  // Fetch or create invoice
  async function fetchOrCreateInvoice(jobcardId) {
    const invoiceResponse = await apiRequest(
      `../src/php/get_invoices.php?jobcard_id=${jobcardId}`
    );

    if (!invoiceResponse.success || invoiceResponse.data.length === 0) {
      const saveResponse = await apiRequest("../src/php/save_invoice.php", "POST", {
        jobcard_id: jobcardId,
      });
      if (!saveResponse.success) {
        throw new Error(saveResponse.message);
      }
      return saveResponse.invoice_number;
    }
    return invoiceResponse.data[0].jobcard_number;
  }

  // Fetch job card details
  async function fetchJobcardDetails(jobcardId) {
    const jobcardResponse = await apiRequest(
      `../src/php/get_jobcard.php?jobcard_id=${jobcardId}`
    );
    if (!jobcardResponse.success) {
      throw new Error(jobcardResponse.message);
    }
    return jobcardResponse.data;
  }

  // Fetch spares
  async function fetchSpares(jobcardId) {
    const sparesResponse = await apiRequest(
      `../src/php/get_spares.php?jobcard_id=${jobcardId}`
    );
    if (!sparesResponse.success) {
      return [];
    }
    return sparesResponse.data;
  }

  // Populate invoice modal
  function populateInvoiceModal(invoiceNumber, jobcardData, sparesData) {
    const data = jobcardData;
    document.getElementById("view-jobcard-number").textContent =
      invoiceNumber || data.jobcard_number;
    document.getElementById("view-job-date").textContent = data.job_date || "";
    document.getElementById("view-client-name").textContent =
      `${data.client_name || ""} ${data.client_surname || ""}`;
    document.getElementById("view-client-telephone").textContent =
      data.client_telephone || "";
    document.getElementById("view-client-address").textContent =
      data.client_address || "";
    document.getElementById("view-client-email").textContent = data.client_email || "";
    document.getElementById("view-hours").textContent = data.hours || "0";
    document.getElementById("view-work-done").textContent = data.work_done || "";
    document.getElementById("view-hourly-rate").textContent = `$${HOURLY_RATE.toFixed(
      2
    )}`;

    // Populate spares table
    const sparesTbody = document.getElementById("view-spares-table-body");
    sparesTbody.innerHTML = "";
    let sparesTotal = 0;
    if (sparesData.length > 0) {
      sparesData.forEach((spare) => {
        const total = spare.unit_price * spare.quantity;
        sparesTotal += total;
        const row = sparesTbody.insertRow();
        row.innerHTML = `
          <td>${spare.spare_name}</td>
          <td>${spare.spare_part_number}</td>
          <td>${spare.quantity}</td>
          <td>$${spare.unit_price.toFixed(2)}</td>
          <td>$${total.toFixed(2)}</td>
        `;
      });
    } else {
      sparesTbody.innerHTML = '<tr><td colspan="5">No spares used</td></tr>';
    }

    // Calculate totals
    const hours = parseFloat(data.hours) || 0;
    const hoursTotal = hours * HOURLY_RATE;
    const subtotal = hoursTotal + sparesTotal;
    const vat = subtotal * 0.15;
    const total = subtotal + vat;

    document.getElementById("view-subtotal").textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById("view-vat").textContent = `$${vat.toFixed(2)}`;
    document.getElementById("view-total").textContent = `$${total.toFixed(2)}`;
  }

  // Close invoice modal
  window.closeInvoice = function () {
    document.getElementById("invoice-modal").style.display = "none";
    selectedJobcardId = null;
    document.querySelectorAll("#invoices-table-body tr").forEach((r) =>
      r.classList.remove("selected")
    );
  };

  // Initial load
  loadInvoices();
});