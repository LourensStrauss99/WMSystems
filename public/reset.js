// Function to handle the reset password process
async function handleResetPassword(token) {
    try {
        console.log('Sending request to reset_password.php with token:', token);
        const response = await fetch(`../src/php/reset_password.php?token=${token}`);
        const data = await response.json();
        console.log('Response from server:', data);

        if (data.success) {
            document.getElementById('resetToken').value = token;
            console.log('Token is valid. Ready to reset password.');
        } else {
            alert(data.message);
            window.location.href = '../views/forgot-password.html';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}
async function handleResetPassword(token) {
    try {
        console.log('Sending request to reset_password.php with token:', token);
        const response = await fetch(`../src/php/reset_password.php?token=${token}`);
        
        // Log response status and headers
        console.log('Response status:', response.status);
        console.log('Response headers:', [...response.headers.entries()]);

        // Log the raw response text
        const text = await response.text();
        console.log('Raw response:', text);

        // Attempt to parse JSON
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error:', e);
            throw new Error('Invalid JSON response');
        }
        console.log('Response from server:', data);

        if (data.success) {
            document.getElementById('resetToken').value = token;
            console.log('Token is valid. Ready to reset password.');
        } else {
            alert(data.message);
            window.location.href = '../views/forgot-password.html';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}

// Function to handle the password reset form submission
async function submitResetPasswordForm(event) {
    event.preventDefault();
    const token = document.getElementById('resetToken').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    console.log('Submitting form with:', { token, newPassword, confirmPassword });

    try {
        const response = await fetch('../src/php/reset_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ token, newPassword, confirmPassword }),
        });

        // Log the raw response for debugging
        const text = await response.text();
        console.log('Raw response:', text);

        // Parse the JSON response
        const data = JSON.parse(text);
        console.log('Response from server:', data);

        if (data.success) {
            alert(data.message);
            window.location.href = '../views/login.html'; // Redirect to login page
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
}

// Extract the token from the URL query string
const urlParams = new URLSearchParams(window.location.search);
const token = urlParams.get('token');

// If a token is present, validate it
if (token) {
    handleResetPassword(token);
} else {
    alert('No token provided. Redirecting to forgot password page.');
    window.location.href = '../views/forgot-password.html';
}

// Add event listener to the reset password form
document.getElementById('resetForm').addEventListener('submit', submitResetPasswordForm);

