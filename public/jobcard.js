$(document).ready(function () {
  let selectedSpares = [];

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

  // Populate dropdowns
  function populateDropdown(selectElement, data, placeholder) {
    selectElement.innerHTML = `<option value="">${placeholder}</option>`;
    data.forEach((item) => {
      const option = document.createElement("option");
      option.value = item.id;
      option.text = `${item.name} ${item.surname || ""}`.trim();
      selectElement.appendChild(option);
    });
    $(selectElement).trigger("change");
  }

  // Populate hours dropdown with 15-minute intervals
  function populateHoursDropdown() {
    const hoursDropdown = document.getElementById("hours");
    if (hoursDropdown) {
      hoursDropdown.innerHTML = ""; // Clear existing options
      for (let i = 0; i <= 24; i++) {
        for (let j = 0; j < 60; j += 15) {
          const hours = String(i).padStart(2, "0");
          const minutes = String(j).padStart(2, "0");
          const value = `${hours}:${minutes}`;
          const option = document.createElement("option");
          option.value = value;
          option.textContent = `${hours}h ${minutes}m`;
          hoursDropdown.appendChild(option);
        }
      }
    }
  }

  // Load artisans
  async function loadArtisans() {
    try {
      const result = await apiRequest("../src/php/get_artisan.php");
      if (result.success && result.data.length > 0) {
        populateDropdown(
          document.getElementById("assigned-employee"),
          result.data,
          "Select an artisan"
        );
      } else {
        console.warn("No artisans found:", result.message || "Empty data");
      }
    } catch (error) {
      console.error("Error loading artisans:", error);
    }
  }

  // Load stock for spares
  async function loadStock() {
    try {
      const result = await apiRequest("../src/php/get_stock.php");
      if (result.success && result.data.length > 0) {
        populateDropdown(
          document.getElementById("spares"),
          result.data.map((stock) => ({
            id: stock.id,
            name: `${stock.name} (${stock.part_number}) - $${parseFloat(
              stock.unit_price
            ).toFixed(2)}`,
          })),
          "Select spares"
        );
      } else {
        console.warn("No stock found:", result.message || "Empty data");
      }
    } catch (error) {
      console.error("Error loading stock:", error);
    }
  }

  // Load spares
  async function loadSpares(jobcardId) {
    try {
      const result = await apiRequest(
        `../src/php/get_spares.php?jobcard_id=${encodeURIComponent(jobcardId)}`
      );
      if (result.success) {
        selectedSpares = result.data.map((spare) => ({
          spare_id: spare.stock_id,
          quantity: spare.quantity,
          name: `${spare.spare_name} (${spare.spare_part_number}) - $${parseFloat(
            spare.unit_price
          ).toFixed(2)}`,
        }));
        updateSparesTable();
      } else {
        console.warn("No spares found:", result.message);
        selectedSpares = [];
        updateSparesTable();
      }
    } catch (error) {
      console.error("Error loading spares:", error);
    }
  }

  // Update spares table
  function updateSparesTable() {
    const sparesTable = document.getElementById("spares-table-body");
    sparesTable.innerHTML = "";
    selectedSpares.forEach((spare, index) => {
      const row = sparesTable.insertRow();
      row.insertCell(0).textContent = spare.name.split(" (")[0];
      row.insertCell(1).textContent = spare.name.match(/\((.*?)\)/)[1].replace(") - $", "");
      row.insertCell(2).innerHTML = `
        ${spare.quantity}
        <button onclick="removeSpare(${index})" style="color: red; border: none; background: none; cursor: pointer;">Remove</button>
      `;
    });
  }

  // Add spare
  window.addSpare = function () {
    const spareIds = $("#spares").val();
    const quantity = document.getElementById("quantity").value;
    if (spareIds.length && quantity) {
      spareIds.forEach((spareId) => {
        const spareOption = document.querySelector(`#spares option[value="${spareId}"]`);
        const spareText = spareOption.text;
        selectedSpares.push({
          spare_id: spareId,
          quantity: parseInt(quantity),
          name: spareText,
        });
      });
      updateSparesTable();
      $("#spares").val(null).trigger("change");
      document.getElementById("quantity").value = "";
    } else {
      alert("Please select at least one spare and a quantity");
    }
  };

  // Remove spare
  window.removeSpare = function (index) {
    selectedSpares.splice(index, 1);
    updateSparesTable();
  };

  // Load job card details
  async function loadJobcardDetails(jobcardId) {
    try {
      const result = await apiRequest(
        `../src/php/get_jobcard.php?jobcard_id=${jobcardId}`
      );
      if (result.success) {
        const data = result.data;
        document.getElementById("jobcard-number").value = data.jobcard_number || "";
        document.getElementById("job-date").value = data.job_date || "";
        document.getElementById("client-name").value = data.client_name || "";
        document.getElementById("client-surname").value = data.client_surname || "";
        document.getElementById("client-address").value = data.client_address || "";
        document.getElementById("client-telephone").value = data.client_telephone || "";
        document.getElementById("client-email").value = data.client_email || "";
        document.getElementById("category").value = data.category || "";
        document.getElementById("work-request").value = data.work_request || "";
        document.getElementById("work-done").value = data.work_done || "";
        document.getElementById("hours").value = data.hours || "";
        document.getElementById("special-request").value = data.special_request || "";

        if (data.client_id) {
          const customerOption = new Option(
            `${data.client_name} ${data.client_surname}`,
            data.client_id,
            true,
            true
          );
          $("#customer").append(customerOption).trigger("change");
        }
        if (data.assigned_employee_id) {
          const artisanOption = new Option(
            `${data.employee_name} ${data.employee_surname}`,
            data.assigned_employee_id,
            true,
            true
          );
          $("#assigned-employee").append(artisanOption).trigger("change");
        }
      }
    } catch (error) {
      console.error("Error loading job card:", error);
    }
  }

  // Handle form submission
  $("#jobcardForm").on("submit", async function (event) {
    event.preventDefault();
    const formData = {
      jobcard_id: jobcardId || null,
      client_id: $("#customer").val(),
      assigned_employee_id: $("#assigned-employee").val() || null,
      job_date: $("#job-date").val(),
      category: $("#category").val(),
      work_request: $("#work-request").val(),
      work_done: $("#work-done").val(),
      hours: $("#hours").val(), // Use the selected value from the dropdown
      special_request: $("#special-request").val(),
      jobcard_number: $("#jobcard-number").val(),
    };
    const spares = selectedSpares.map((spare) => ({
      stock_id: parseInt(spare.spare_id),
      quantity: spare.quantity,
    }));
    try {
      const jobcardResult = await apiRequest("../src/php/save_jobcard.php", "POST", formData);
      if (!jobcardResult.success) {
        throw new Error(jobcardResult.message);
      }
      const savedJobcardId = jobcardResult.jobcard_id;

      if (spares.length > 0) {
        const stockResult = await apiRequest("../src/php/update_stock.php", "POST", {
          jobcard_id: savedJobcardId,
          spares,
        });
        if (!stockResult.success) {
          throw new Error(stockResult.message);
        }
      }

      alert("Job card saved successfully");
      window.location.href = "/views/client.html";
    } catch (error) {
      console.error("Submission error:", error);
      alert("Error: Failed to save job card: " + error.message);
    }
  });

  // Initialize page
  const urlParams = new URLSearchParams(window.location.search);
  const jobcardId = urlParams.get("jobcard_id");
  if (jobcardId) {
    loadJobcardDetails(jobcardId);
    loadSpares(jobcardId);
  }
  loadArtisans();
  loadStock();
  populateHoursDropdown(); // Populate the hours dropdown
});