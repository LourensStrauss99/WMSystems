// Utility functions
function showError(element, message) {
  element.classList.remove("hidden");
  element.textContent = message;
}

function hideError(element) {
  element.classList.add("hidden");
  element.textContent = "";
}

async function apiRequest(url, method, data) {
  const options = {
    method,
    headers: { "Content-Type": "application/json" },
    body: method !== "GET" ? JSON.stringify(data) : undefined,
  };

  const response = await fetch(url, options);

  // Check if the response is JSON
  const contentType = response.headers.get("content-type");
  if (contentType && contentType.includes("application/json")) {
    return response.json();
  } else {
    const text = await response.text();
    console.error("Unexpected response:", text);
    throw new Error("Invalid JSON response");
  }
}

// Login functionality
document.getElementById("login-btn").addEventListener("click", async () => {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;
  const error = document.getElementById("login-error");

  if (!username || !password) {
    showError(error, "Please fill in all fields.");
    return;
  }

  try {
    const result = await apiRequest("src/php/login.php", "POST", { username, password });
    if (result.success) {
      document.getElementById("login-section").classList.add("hidden");
      document.getElementById("admin-panel").classList.remove("hidden");
      hideError(error);
    } else {
      showError(error, result.message || "Invalid credentials. Please try again.");
    }
  } catch (err) {
    showError(error, `Error: ${err.message}`);
  }
});

// Add User functionality
document.getElementById("add-user-btn").addEventListener("click", async () => {
  const name = document.getElementById("user-name").value;
  const username = document.getElementById("user-username").value;
  const email = document.getElementById("user-email").value;
  const password = document.getElementById("user-password").value;
  const adminLevel = document.getElementById("admin-level").value;
  const error = document.getElementById("user-error");

  if (!name || !username || !email || !password) {
    showError(error, "Please fill in all fields.");
    return;
  }

  try {
    const result = await apiRequest("src/php/add-user.php", "POST", {
      name,
      username,
      email,
      password,
      admin_level: adminLevel,
    });
    if (result.success) {
      alert("User added successfully!");
      ["user-name", "user-username", "user-email", "user-password"].forEach(
        (id) => (document.getElementById(id).value = "")
      );
      hideError(error);
    } else {
      showError(error, `Error: ${result.message}`);
    }
  } catch (err) {
    showError(error, `Error: ${err.message}`);
  }
});

// Add Stock functionality
document.getElementById("add-stock-btn").addEventListener("click", async () => {
  const name = document.getElementById("stock-name").value;
  const partNumber = document.getElementById("stock-part-number").value;
  const unitPrice = document.getElementById("stock-unit-price").value;
  const quantity = document.getElementById("stock-quantity").value;
  const errors = {
    name: document.getElementById("stock-name-error"),
    partNumber: document.getElementById("stock-part-number-error"),
    unitPrice: document.getElementById("stock-unit-price-error"),
    quantity: document.getElementById("stock-quantity-error"),
    general: document.getElementById("stock-error"),
  };

  // Reset error messages
  Object.values(errors).forEach(hideError);

  // Validate fields
  let hasError = false;
  if (!name) {
    showError(errors.name, "Item name is required.");
    hasError = true;
  }
  if (!partNumber) {
    showError(errors.partNumber, "Part number is required.");
    hasError = true;
  }
  if (!unitPrice || parseFloat(unitPrice) <= 0) {
    showError(errors.unitPrice, "Unit price must be a positive number.");
    hasError = true;
  }
  if (!quantity || parseInt(quantity) < 0) {
    showError(errors.quantity, "Quantity must be non-negative.");
    hasError = true;
  }

  if (hasError) {
    showError(errors.general, "Please correct the errors above.");
    return;
  }

  try {
    const result = await apiRequest("src/php/add-stock.php", "POST", {
      name,
      part_number: partNumber,
      unit_price: parseFloat(unitPrice),
      quantity: parseInt(quantity),
    });
    if (result.success) {
      alert("Stock added successfully!");
      ["stock-name", "stock-part-number", "stock-unit-price", "stock-quantity"].forEach(
        (id) => (document.getElementById(id).value = "")
      );
      hideError(errors.general);
      updateStockReport();
    } else {
      showError(errors.general, `Error: ${result.message}`);
    }
  } catch (err) {
    showError(errors.general, `Error: ${err.message}`);
  }
});

// Update Stock Report
async function updateStockReport() {
  try {
    const stock = await apiRequest("src/php/get-stock.php", "GET");
    const tbody = document.getElementById("stock-table-body");
    tbody.innerHTML = "";
    stock.forEach((item) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td class="p-2">${item.name}</td>
        <td class="p-2">${item.part_number}</td>
        <td class="p-2">${item.unit_price}</td>
        <td class="p-2">${item.quantity || 0}</td>
      `;
      tbody.appendChild(row);
    });
  } catch (err) {
    console.error("Error fetching stock:", err);
  }
}

// Add Employee functionality
document.getElementById("add-employee-btn").addEventListener("click", async () => {
  const name = document.getElementById("employee-name").value;
  const surname = document.getElementById("employee-surname").value;
  const email = document.getElementById("employee-email").value;
  const phone = document.getElementById("employee-phone").value;
  const role = document.getElementById("employee-role").value;
  const adminLevel =
    role === "admin" ? document.getElementById("employee-admin").value : null;
  const error = document.getElementById("employee-error");

  if (!name || !surname || !email || !phone || !role) {
    showError(error, "Please fill in all fields.");
    return;
  }

  try {
    const result = await apiRequest("src/php/add-employee.php", "POST", {
      name,
      surname,
      email,
      phone,
      role,
      admin_level: adminLevel,
    });
    if (result.success) {
      alert("Employee added successfully!");
      ["employee-name", "employee-surname", "employee-email", "employee-phone"].forEach(
        (id) => (document.getElementById(id).value = "")
      );
      document.getElementById("employee-role").value = "employee";
      document.getElementById("employee-admin-level").classList.add("hidden");
      hideError(error);
    } else {
      showError(error, `Error: ${result.message}`);
    }
  } catch (err) {
    showError(error, `Error: ${err.message}`);
  }
});

// Show/Hide admin level based on role
document.getElementById("employee-role").addEventListener("change", (e) => {
  const adminLevelDiv = document.getElementById("employee-admin-level");
  if (e.target.value === "admin") {
    adminLevelDiv.classList.remove("hidden");
  } else {
    adminLevelDiv.classList.add("hidden");
  }
});

// Initial stock report load
updateStockReport();