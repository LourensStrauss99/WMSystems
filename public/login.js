document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");

  if (loginForm) {
    loginForm.addEventListener("submit", async (event) => {
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
  } else {
    console.error("Login form not found!");
  }
});