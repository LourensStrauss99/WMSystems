// Initialize arrays
let customerTransactions = [];
let assignedJobcards = JSON.parse(localStorage.getItem("assignedJobcards")) || [];
let selectedJobcard = null; // Track the selected job card

document.addEventListener("DOMContentLoaded", () => {
  initializeCustomerTransactions();
  displayAssignedJobcards();

  if (window.location.pathname.includes("Client.html")) {
    initializeClientPage();
  }

  if (window.location.pathname.includes("Jobcard.html")) {
    populateJobcardFields();
  }
});

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

// Initialize customer transactions
function initializeCustomerTransactions() {
  const customerTableBody = document.getElementById("customer-table-body");
  if (customerTableBody) {
    const rows = customerTableBody.getElementsByTagName("tr");
    for (let row of rows) {
      const cells = row.getElementsByTagName("input");
      customerTransactions.push({
        invoiceId: cells[0].value,
        date: cells[1].value,
        clientName: cells[2].value,
        amount: parseFloat(cells[3].value.replace("$", "")),
        tax: parseFloat(cells[4].value.replace("$", "")),
      });
    }
  }
}

// Initialize Client.html page
function initializeClientPage() {
  try {
    const jobcardNumberField = document.getElementById("jobcard-number");
    if (jobcardNumberField && !jobcardNumberField.value) {
      const jobcardNumber = generateJobcardNumber();
      jobcardNumberField.value = jobcardNumber;
      saveFormData(); // Save the generated jobcard number
    }
  } catch (error) {
    console.error("Error generating jobcard number on page load:", error);
    alert("Failed to generate job card number on page load.");
  }
}

// Generate job card number in DDMMYYNNNN format
function generateJobcardNumber() {
  try {
    const invoiceDateInput = document.getElementById("invoice-date");
    const date = invoiceDateInput && invoiceDateInput.value ? new Date(invoiceDateInput.value) : new Date();

    if (isNaN(date.getTime())) {
      throw new Error("Invalid date in invoice-date field");
    }

    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = String(date.getFullYear()).slice(-2);
    const datePrefix = `${day}${month}${year}`;

    const jobCounts = JSON.parse(localStorage.getItem("jobCounts")) || {};
    const todayKey = datePrefix;
    let jobNumber = (jobCounts[todayKey] || 0) + 1;

    jobCounts[todayKey] = jobNumber;
    localStorage.setItem("jobCounts", JSON.stringify(jobCounts));

    const formattedJobNumber = String(jobNumber).padStart(4, "0");
    return `${datePrefix}${formattedJobNumber}`;
  } catch (error) {
    console.error("Error in generateJobcardNumber:", error);
    throw error;
  }
}

// Save form data to localStorage
function saveFormData() {
  const formData = {
    jobcardNumber: document.getElementById("jobcard-number")?.value || "",
    invoiceDate: document.getElementById("invoice-date")?.value || "",
    customer: $("#customer")?.val() || document.getElementById("customer")?.value || "",
    telephone: document.getElementById("Telephone")?.value || "",
    address: document.getElementById("Address")?.value || "",
    category: document.getElementById("Category")?.value || "",
    workRequest: document.getElementById("work-request")?.value || "",
    specialRequest: document.getElementById("special-request")?.value || "",
    instructions: document.getElementById("Instructions")?.value || "",
    workDone: document.getElementById("work-done")?.value || "",
    assignedArtisan: $("#artisan")?.val() || document.getElementById("artisan")?.value || "",
    spares: document.getElementById("spares")?.value || "",
    quantities: document.getElementById("quantities")?.value || "",
    duration: document.getElementById("duration")?.value || "",
  };
  localStorage.setItem("clientFormData", JSON.stringify(formData));
  console.log("Saved data:", formData);
  return formData;
}

// Populate fields in Jobcard.html
function populateJobcardFields() {
  const savedData = localStorage.getItem("clientFormData");
  if (!savedData) {
    console.log("No data found in localStorage for Jobcard.html");
    alert("No data found in localStorage for Jobcard.html");
    return;
  }

  const formData = JSON.parse(savedData);
  console.log("Loaded data in Jobcard.html:", formData);

  setTimeout(() => {
    populateField("jobcard-number", formData.jobcardNumber);
    populateField("invoice-date", formData.invoiceDate);
    populateField("customer", formData.customer);
    populateField("Telephone", formData.telephone);
    populateField("Address", formData.address);
    populateField("Category", formData.category);
    populateField("work-request", formData.workRequest);
    populateField("Instructions", formData.specialRequest);
    populateField("work-done", formData.workDone);
    populateField("spares", formData.spares);
    populateField("quantities", formData.quantities);
    populateField("duration", formData.duration);
  }, 500);
}

// Helper function to populate a field
function populateField(fieldId, value) {
  const field = document.getElementById(fieldId);
  if (field) {
    field.value = value || "";
    console.log(`Set ${fieldId}:`, field.value);
  }
}

// Display assigned job cards
function displayAssignedJobcards() {
  const tableBody = document.getElementById("assigned-jobcards-table-body");
  if (tableBody) {
    tableBody.innerHTML = "";
    assignedJobcards.forEach((jobcard, index) => {
      const newRow = document.createElement("tr");
      newRow.innerHTML = `
        <td>${jobcard.jobcardNumber}</td>
        <td>${jobcard.customer}</td>
        <td>${jobcard.artisan}</td>
      `;
      newRow.addEventListener("click", () => {
        const rows = tableBody.getElementsByTagName("tr");
        for (let row of rows) {
          row.classList.remove("selected");
        }
        newRow.classList.add("selected");
        selectedJobcard = jobcard;
      });
      tableBody.appendChild(newRow);
    });
  }
}