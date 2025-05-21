document.getElementById("addUserForm").addEventListener("submit", function (event) {
        const username = document.getElementById("username").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        if (!username || !email || !password) {
          event.preventDefault();
          alert("All fields are required!");
        }
      });

document.addEventListener("DOMContentLoaded", () => {
  const adminLevelDropdown = document.getElementById("adminLevel");
  const level3Option = document.getElementById("level3Option");

  // Simulate the logged-in user's admin level (replace with actual backend logic)
  const loggedInAdminLevel = parseInt(sessionStorage.getItem("loggedInAdminLevel")) || 1; // Example: Level 1 user

  // Restrict Level 3 option for non-Super Users
  if (loggedInAdminLevel < 3) {
    level3Option.style.display = "none";
  }

  const form = document.getElementById("addUserForm");
  const responseMessage = document.getElementById("responseMessage");

  form.addEventListener("submit", (event) => {
    event.preventDefault(); // Prevent default form submission

    const formData = new FormData(form);

    fetch("../src/php/admin-panel.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
      })
      .then((data) => {
        responseMessage.textContent = data; // Display the response message
        responseMessage.classList.remove("text-red-500");
        responseMessage.classList.add("text-green-500");

        // Clear the form fields
        form.reset();
      })
      .catch((error) => {
        responseMessage.textContent = "An error occurred. Please try again.";
        responseMessage.classList.remove("text-green-500");
        responseMessage.classList.add("text-red-500");
        console.error("Error:", error);
      });
  });
});

function checkAvailability(field, value) {
  if (!value.trim()) return; // Skip empty values

  fetch(`../src/php/check-availability.php?field=${field}&value=${encodeURIComponent(value)}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      const errorElement = document.getElementById(`${field}Error`);
      if (data.exists) {
        errorElement.textContent = `${field.charAt(0).toUpperCase() + field.slice(1)} already exists.`;
      } else {
        errorElement.textContent = "";
      }
    })
    .catch((error) => {
      console.error("Error checking availability:", error);
    });
}
