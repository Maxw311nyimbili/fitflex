// Get elements
// const trainerRadio = document.getElementById("trainer");
const traineeRadio = document.getElementById("trainee");
const trainerModal = document.getElementById("trainerModal");
const traineeModal = document.getElementById("traineeModal");
const trainerDoneButton = document.getElementById("trainerDone");
const closeButtons = document.querySelectorAll(".close");
const signUpForm = document.getElementById("registerForm"); 

// Show Trainer modal when selected
// trainerRadio.addEventListener("change", function () {
//     if (this.checked) {
//         trainerModal.style.display = "block"; // Show modal
//     }
// });

// Show Trainee modal when selected
traineeRadio.addEventListener("change", function () {
    if (this.checked) {
        traineeModal.style.display = "block"; // Show modal
    }
});

// Close modals when the close button is clicked
closeButtons.forEach((button) => {
    button.addEventListener("click", function () {
        trainerModal.style.display = "none"; // Hide trainer modal
        traineeModal.style.display = "none"; // Hide trainee modal
    });
});

// Capture and validate Trainer details
// trainerDoneButton.addEventListener("click", function () {
//     const gymNameValue = document.getElementById('gymName').value;
//     const gymContactValue = document.getElementById('gymContact').value;
//     const gymLocationValue = document.getElementById('gymLocation').value;
//     const servicesOfferedValue = document.getElementById('servicesOffered').value;

//     if (gymNameValue && gymContactValue && gymLocationValue && servicesOfferedValue) {
//         document.getElementById('gymNameHidden').value = gymNameValue;
//         document.getElementById('gymContactHidden').value = gymContactValue;
//         document.getElementById('gymLocationHidden').value = gymLocationValue;
//         document.getElementById('servicesOfferedHidden').value = servicesOfferedValue;

//         trainerModal.style.display = 'none'; // Close modal
//     } else {
//         alert("Please fill out all fields in the Trainer form.");
//     }
// });

// Capture and validate Trainee details
document.getElementById('traineeDone').addEventListener("click", function () {
    const preferredGymValue = document.getElementById('gymPreferred').value;

    if (preferredGymValue) {
        const hiddenGymPreferredInput = document.createElement('input');
        hiddenGymPreferredInput.setAttribute('type', 'hidden');
        hiddenGymPreferredInput.setAttribute('name', 'gymPreferred');
        hiddenGymPreferredInput.setAttribute('value', preferredGymValue);
        
        signUpForm.appendChild(hiddenGymPreferredInput); // Append to main form

        traineeModal.style.display = 'none'; // Close modal
    } else {
        alert("Please select a preferred gym.");
    }
});