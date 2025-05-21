document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("registerForm");
  const errorMessage = document.getElementById("errorMessage");

  registerForm.addEventListener("submit", (event) => {
    event.preventDefault(); // Prevent default form submission

    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value.trim();

    // Validate that all fields are filled
    if (!username || !email || !password || !confirmPassword) {
      errorMessage.textContent = "All fields are required.";
      return;
    }

    // Validate that passwords match
    if (password !== confirmPassword) {
      errorMessage.textContent = "Passwords do not match.";
      return;
    }

    // Clear error message
    errorMessage.textContent = "";

    // Submit the form via fetch
    const formData = new FormData(registerForm);

    fetch("../src/php/register.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          alert(data.message);
          window.location.href = "Login.html";
        } else {
          errorMessage.textContent = data.message;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        errorMessage.textContent = "An error occurred. Please try again.";
      });
  });
});