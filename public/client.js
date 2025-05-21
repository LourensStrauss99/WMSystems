$(document).ready(function () {
  // Utility functions
  async function apiRequest(url, method, data = null) {
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

  function resetForm() {
    document.getElementById("clientForm").reset();
    document.getElementById("jobcard-number").value = generateJobcardNumber();
  }

  function generateJobcardNumber() {
    try {
      const date = new Date();
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      let sequence = parseInt(localStorage.getItem("jobcardSequence") || "0") + 1;
      localStorage.setItem("jobcardSequence", sequence);
      sequence = String(sequence).padStart(3, "0");
      const jobcardNumber = `JC-${year}${month}${day}-${sequence}`;
      console.log("Generated jobcard number:", jobcardNumber);
      return jobcardNumber;
    } catch (error) {
      console.error("Error generating jobcard number:", error);
      return `JC-ERROR-${Date.now()}`;
    }
  }

  // Load clients into the dropdown
  async function loadClients() {
    try {
      console.log("Fetching clients from get_clients.php...");
      const result = await apiRequest("../src/php/get_clients.php", "GET");

      if (result.success) {
        const customerSelect = document.getElementById("customer");
        customerSelect.innerHTML = '<option value="">Select a customer</option>';
        result.data.forEach((client) => {
          const option = document.createElement("option");
          option.value = client.id;
          option.text = `${client.name} ${client.surname}`;
          option.dataset.client = JSON.stringify(client);
          customerSelect.appendChild(option);
        });
        console.log("Clients loaded successfully:", result.data);
      } else {
        console.error("Failed to load clients:", result.message);
        alert("Failed to load clients: " + result.message);
      }
    } catch (error) {
      console.error("Error loading clients:", error);
      alert("Failed to load clients: " + error.message);
    }
  }

  // Handle client selection
  function handleClientSelection() {
    $("#customer").on("select2:select", function (e) {
      const data = e.params.data;
      if (data.id) {
        const client = JSON.parse(data.element.dataset.client);
        console.log("Selected client data:", client);
        document.getElementById("name").value = client.name || "";
        document.getElementById("surname").value = client.surname || "";
        document.getElementById("Address").value = client.address || "";
        document.getElementById("Telephone").value = client.telephone_number || "";
        document.getElementById("email").value = client.email_address || "";
      }
    });

    $("#customer").on("select2:unselect", resetForm);
  }

  // Handle form submission
  async function handleFormSubmission() {
    document.getElementById("clientForm").addEventListener("submit", async function (event) {
      event.preventDefault();

      const formData = {
        client_id: document.getElementById("customer").value,
        name: document.getElementById("name").value.trim(),
        surname: document.getElementById("surname").value.trim(),
        address: document.getElementById("Address").value.trim(),
        telephone_number: document.getElementById("Telephone").value.trim(),
        email_address: document.getElementById("email").value.trim(),
        job_date: document.getElementById("invoice-date").value,
        category: document.getElementById("category").value,
        work_request: document.getElementById("work-request").value.trim(),
        special_request: document.getElementById("special-request").value.trim(),
        jobcard_number: document.getElementById("jobcard-number").value,
      };

      console.log("Submitting form data:", formData);

      try {
        console.log("Sending request to process_client.php...");
        const result = await apiRequest("../src/php/process_client.php", "POST", formData);

        if (result.success) {
          localStorage.setItem(
            "jobcardSequence",
            parseInt(localStorage.getItem("jobcardSequence") || "0") + 1
          );
          window.location.href = `/views/jobcard.html?jobcard_id=${result.jobcard_id}`;
        } else {
          console.error("Error from server:", result.message);
          alert("Error: " + result.message);
        }
      } catch (error) {
        console.error("Fetch error:", error);
        alert("Error: Failed to send data to server - " + error.message);
      }
    });
  }

  // Initialize Select2
  $("#customer").select2({
    placeholder: "Select a customer",
    allowClear: true,
  });

  // Initialize the page
  function initialize() {
    document.getElementById("jobcard-number").value = generateJobcardNumber();
    loadClients();
    handleClientSelection();
    handleFormSubmission();
  }

  initialize();
});