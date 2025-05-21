document.addEventListener("DOMContentLoaded", function () {
  let selectedJobcardId = null;

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
  async function loadJobcards() {
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
      console.log("Fetching jobcards from:", url);
      const result = await apiRequest(url);

      if (result.success && result.data.length > 0) {
        populateJobcardsTable(result.data);
      } else {
        console.warn("No jobcards found:", result.message || "Empty data");
        document.getElementById("assigned-jobcards-table-body").innerHTML =
          '<tr><td colspan="3">No jobcards found</td></tr>';
      }
    } catch (error) {
      console.error("Error loading jobcards:", error);
      alert("Failed to load jobcards: " + error.message);
    }
  }

  // Populate jobcards table
  function populateJobcardsTable(jobcards) {
    const tbody = document.getElementById("assigned-jobcards-table-body");
    tbody.innerHTML = "";
    jobcards.forEach((jobcard) => {
      const row = tbody.insertRow();
      row.innerHTML = `
        <td>${jobcard.jobcard_number}</td>
        <td>${jobcard.client_name} ${jobcard.client_surname}</td>
        <td>${jobcard.job_date}</td>
      `;
      row.dataset.jobcardId = jobcard.id;
      row.dataset.artisanName =
        jobcard.employee_name && jobcard.employee_surname
          ? `${jobcard.employee_name} ${jobcard.employee_surname}`
          : "Unassigned";

      // Add event listeners for row selection and double-click
      row.addEventListener("dblclick", () => {
        document.querySelectorAll("#assigned-jobcards-table-body tr").forEach((r) =>
          r.classList.remove("selected")
        );
        row.classList.add("selected");
        selectedJobcardId = jobcard.id;
        openJobcard();
        console.log("Double-clicked jobcard:", jobcard.id);
      });
    });
  }

  // Filter jobcards by client or artisan name
  window.filterJobcards = function () {
    const input = document.getElementById("search-jobcards").value.toLowerCase();
    const rows = document.querySelectorAll("#assigned-jobcards-table-body tr");
    rows.forEach((row) => {
      const clientName = row.cells[1].textContent.toLowerCase();
      const artisanName = row.dataset.artisanName.toLowerCase();
      row.style.display =
        clientName.includes(input) || artisanName.includes(input) ? "" : "none";
    });
  };

  // Open jobcard modal
  window.openJobcard = async function () {
    if (!selectedJobcardId) {
      alert("Please select a jobcard");
      return;
    }
    try {
      const jobcardData = await apiRequest(
        `../src/php/get_jobcard.php?jobcard_id=${selectedJobcardId}`
      );
      const sparesData = await apiRequest(
        `../src/php/get_spares.php?jobcard_id=${selectedJobcardId}`
      );

      if (!jobcardData.success) {
        throw new Error(jobcardData.message);
      }

      populateJobcardModal(jobcardData.data, sparesData.success ? sparesData.data : []);
      document.getElementById("jobcard-modal").style.display = "block";
    } catch (error) {
      console.error("Error opening jobcard:", error);
      alert("Failed to load jobcard: " + error.message);
    }
  };

  // Populate jobcard modal
  function populateJobcardModal(jobcardData, sparesData) {
    document.getElementById("view-jobcard-number").value =
      jobcardData.jobcard_number || "";
    document.getElementById("view-job-date").value = jobcardData.job_date || "";
    document.getElementById("view-client-name").value =
      `${jobcardData.client_name || ""} ${jobcardData.client_surname || ""}`;
    document.getElementById("view-client-telephone").value =
      jobcardData.client_telephone || "";
    document.getElementById("view-client-address").value =
      jobcardData.client_address || "";
    document.getElementById("view-client-email").value = jobcardData.client_email || "";
    document.getElementById("view-category").value = jobcardData.category || "";
    document.getElementById("view-artisan").value =
      jobcardData.employee_name && jobcardData.employee_surname
        ? `${jobcardData.employee_name} ${jobcardData.employee_surname}`
        : "Unassigned";
    document.getElementById("view-work-request").value =
      jobcardData.work_request || "";
    document.getElementById("view-work-done").value = jobcardData.work_done || "";
    document.getElementById("view-hours").value = jobcardData.hours || "";
    document.getElementById("view-special-request").value =
      jobcardData.special_request || "";

    // Populate spares table
    const sparesTbody = document.getElementById("view-spares-table-body");
    sparesTbody.innerHTML = "";
    if (sparesData.length > 0) {
      sparesData.forEach((spare) => {
        const row = sparesTbody.insertRow();
        row.innerHTML = `
          <td>${spare.spare_name}</td>
          <td>${spare.spare_part_number}</td>
          <td>${spare.quantity}</td>
        `;
      });
    } else {
      sparesTbody.innerHTML = '<tr><td colspan="3">No spares used</td></tr>';
    }
  }

  // Close jobcard modal
  window.closeJobcard = function () {
    document.getElementById("jobcard-modal").style.display = "none";
    selectedJobcardId = null;
    document.querySelectorAll("#assigned-jobcards-table-body tr").forEach((r) =>
      r.classList.remove("selected")
    );
  };

  // Initial load
  loadJobcards();
});