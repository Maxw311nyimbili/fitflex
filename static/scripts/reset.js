document.getElementById("reset-form").addEventListener("submit", function(event) {
    const newPassword = document.getElementById("new-password");
    const confirmPassword = document.getElementById("confirm-password");
    const passwordError = document.getElementById("password-error");
    const confirmError = document.getElementById("confirm-error");
    let isValid = true;

    // Validate new password length
    if (newPassword.value.length < 8) {
        passwordError.style.display = "block";
        newPassword.focus();
        isValid = false;
    } else {
        passwordError.style.display = "none";
    }

    // Validate passwords match
    if (newPassword.value !== confirmPassword.value) {
        confirmError.style.display = "block";
        confirmPassword.focus();
        isValid = false;
    } else {
        confirmError.style.display = "none";
    }

    if (isValid) {
        showSuccessMessage();
        event.preventDefault(); // prevent form submission to demonstrate success message
    } else {
        event.preventDefault(); // prevent submission if invalid
    }
});

function showSuccessMessage() {
    const successMessage = document.getElementById("success-message");
    successMessage.style.display = "block";

    // Redirect to login page after 3 seconds
    setTimeout(function() {
        window.location.href = "login.html";
    }, 3000);
}
