document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");

  if (loginForm) {
    loginForm.addEventListener("submit", (event) => {
      event.preventDefault();
      console.log("Login form submitted!");
      // Add your login logic here
    });
  } else {
    console.error("Login form not found!");
  }
});

// Login form
document
  .getElementById("loginForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("loginUsername").value;
    const password = document.getElementById("loginPassword").value;

    try {
      const response = await fetch("../src/php/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      });
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      const result = await response.json();
      alert(result.message);
      if (result.success) {
        window.location.href = "dashboard.html";
      }
    } catch (error) {
      console.error("Fetch error:", error);
      alert("Error: Failed to process request. Check console for details.");
    }
  });

// Registration form
document
  .getElementById("registrationForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("regUsername").value;
    const email = document.getElementById("regEmail").value;
    const password = document.getElementById("regPassword").value;
    const confirmPassword = document.getElementById("regConfirmPassword").value;

    if (password.length < 8) {
      alert("Password must be at least 8 characters");
      return;
    }

    try {
      const response = await fetch("../src/php/register.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          username,
          email,
          password,
          confirmPassword,
        }),
      });
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      const result = await response.json();
      alert(result.message);
      if (result.success) {
        document.getElementById("registrationForm").reset();
      }
    } catch (error) {
      console.error("Fetch error:", error);
      alert("Error: Failed to process request. Check console for details.");
    }
  });

// Forgot password link
document.getElementById('forgotPasswordLink').addEventListener('click', (e) => {
    e.preventDefault();
    window.location.href = 'forgot-password.php';
});

// Back to login link
document
  .getElementById("backToLoginLink")
  .addEventListener("click", function (e) {
    e.preventDefault();
    document.getElementById("loginForm").classList.remove("hidden");
    document.getElementById("registrationForm").classList.remove("hidden");
    document.getElementById("forgotPasswordForm").classList.add("hidden");
  });

// Forgot password form
document
  .getElementById("forgotPasswordForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault();
    const email = document.getElementById("forgotEmail").value;

    try {
      const response = await fetch("http://localhost:8000/src/php/forgot-password.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email }),
      });
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      const result = await response.json();
      alert(result.message);
      if (result.success) {
        document.getElementById("forgotPasswordForm").reset();
        document.getElementById("loginForm").classList.remove("hidden");
        document.getElementById("registrationForm").classList.remove("hidden");
        document.getElementById("forgotPasswordForm").classList.add("hidden");
      }
    } catch (error) {
      console.error("Fetch error:", error);
      alert("Error: Failed to process request. Check console for details.");
    }
  });

