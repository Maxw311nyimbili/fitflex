document.getElementById("forgot-password-form").addEventListener("submit", function(event) {
    const emailField = document.getElementById("email");
    const emailError = document.getElementById("email-error");

    // Check if email is valid
    if (!emailField.value || !validateEmail(emailField.value)) {
        emailError.style.display = "block";
        emailField.focus();
        event.preventDefault();
    } else {
        emailError.style.display = "none";
    }
});

function validateEmail(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
}
