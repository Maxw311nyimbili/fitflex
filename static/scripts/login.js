// document.getElementById("loginForm").addEventListener("submit", function(event) {
//     event.preventDefault(); // Prevent form submission

//     let isValid = true;

//     // Email validation: basic email pattern
//     const email = document.getElementById("email").value;
//     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
//     if (!emailRegex.test(email)) {
//         document.getElementById("emailError").textContent = "Please enter a valid email.";
//         isValid = false;
//     } else {
//         document.getElementById("emailError").textContent = ""; // Clear previous error
//     }

//     // Password validation: check if not empty
//     const password = document.getElementById("password").value;
    
//     if (password === "") {
//         document.getElementById("passwordError").textContent = "Password cannot be empty.";
//         isValid = false;
//     } else {
//         document.getElementById("passwordError").textContent = ""; // Clear previous error
//     }

//     // // Role validation: check if a role is selected
//     // const roleTrainee = document.getElementById("trainee").checked;
//     // const roleTrainer = document.getElementById("trainer").checked;
    
//     // if (!roleTrainee && !roleTrainer) {
//     //     alert("Please select a role.");
//     //     isValid = false;
//     // }

//     // Submit form if all fields are valid
//     if (isValid) {
//         document.getElementById("loginForm").submit();
//     }
// });