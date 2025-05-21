document.addEventListener("DOMContentLoaded", function () {
  let selectedStockId = null;

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

  // Load stock
  async function loadStock() {
    try {
      const url = "../src/php/get_stock.php";
      console.log("Fetching stock from:", url);
      const result = await apiRequest(url);

      if (result.success && result.data.length > 0) {
        populateStockTable(result.data);
      } else {
        console.warn("No stock found:", result.message || "Empty data");
        document.getElementById("stock-table-body").innerHTML =
          '<tr><td colspan="4">No stock found</td></tr>';
      }
    } catch (error) {
      console.error("Error loading stock:", error);
      alert("Failed to load stock: " + error.message);
    }
  }

  // Populate stock table
  function populateStockTable(stockItems) {
    const tbody = document.getElementById("stock-table-body");
    tbody.innerHTML = "";
    stockItems.forEach((item) => {
      const row = tbody.insertRow();
      row.innerHTML = `
        <td>${item.name}</td>
        <td>${item.part_number}</td>
        <td>$${parseFloat(item.unit_price).toFixed(2)}</td>
        <td>${item.quantity}</td>
      `;
      row.dataset.stockId = item.id;

      // Add event listeners for row selection and double-click
      row.addEventListener("click", () => selectStockRow(row, item));
      row.addEventListener("dblclick", () => openStock(item));
    });
  }

  // Select stock row
  function selectStockRow(row, item) {
    document.querySelectorAll("#stock-table-body tr").forEach((r) =>
      r.classList.remove("selected")
    );
    row.classList.add("selected");
    selectedStockId = item.id;
    console.log("Selected stock:", item.id);
  }

  // Filter stock by name
  window.filterStock = function () {
    const input = document.getElementById("search-stock").value.toLowerCase();
    const rows = document.querySelectorAll("#stock-table-body tr");
    rows.forEach((row) => {
      const name = row.cells[0].textContent.toLowerCase();
      row.style.display = name.includes(input) ? "" : "none";
    });
  };

  // Open stock modal
  window.openStock = function (item) {
    document.getElementById("view-spare-name").textContent = item.name || "";
    document.getElementById("view-part-number").textContent = item.part_number || "";
    document.getElementById("view-unit-price").textContent = item.unit_price
      ? `$${parseFloat(item.unit_price).toFixed(2)}`
      : "$0.00";
    document.getElementById("view-quantity").textContent = item.quantity || "0";
    document.getElementById("stock-modal").style.display = "block";
  };

  // Close stock modal
  window.closeStock = function () {
    document.getElementById("stock-modal").style.display = "none";
    selectedStockId = null;
    document.querySelectorAll("#stock-table-body tr").forEach((r) =>
      r.classList.remove("selected")
    );
  };

  // Initial load
  loadStock();
});