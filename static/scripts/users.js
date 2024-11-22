//VIEW FUNCTION
function viewUser(userId) {
    console.log(`${userId}`);
  fetch(`../actions/view_user.php?id=${userId}`)
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              document.getElementById('modalUserDetails').innerHTML = `
                  <p><strong>ID:</strong> ${data.user.user_id}</p>
                  <p><strong>Name:</strong> ${data.user.name}</p>
                  <p><strong>Email:</strong> ${data.user.email}</p>
              `;
              document.getElementById('userModal').style.display = 'block';
          } else {
              alert('Error: ' + data.message);
          }
      })
      .catch(error => console.error('Error:', error));
}

function closeModal() {
  document.getElementById('userModal').style.display = 'none';
}



// EDIT FUNCTION
function editUser(userId) {
    // Fetch user data using GET request
    fetch(`../actions/edit_user_GET.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate the modal form fields with user data
                document.getElementById('editUserId').value = data.user.user_id;
                document.getElementById('editUsername').value = data.user.name;
                document.getElementById('editEmail').value = data.user.email;
                
                // Show the modal
                document.getElementById('editUserModal').style.display = 'block';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching the user data.');
        });
}


function updateUser() {
    const userId = document.getElementById('editUserId').value;
    const email = document.getElementById('editEmail').value;
    const name = document.getElementById('editUsername').value;

    // Send updated data using POST request
    fetch('../actions/edit_user_POST.php', {
        method: 'POST',  // POST method to send data
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',  // Form-urlencoded content type
        },
        body: new URLSearchParams({
            'id': userId,
            'email': email,
            'name': name
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);  // Show success message
            closeModal();  // Close the modal
            location.reload();
        } else {
            alert('Error: ' + data.message);  // Show error message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the user.');
    });
}
function closeEditModal(){
    document.getElementById("editUserModal").style.display = 'none';
}




// DELETE FUNCTION
function deleteUser(userId) {
    if (confirm("Are you sure you want to delete this user?")) {
        fetch('../actions/delete_user.php', {
            method: 'DELETE',
            body: new URLSearchParams({ id: userId }) // Send the user ID in the body
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User deleted successfully');
                location.reload(); // Refresh the table
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
  }
  
// Function to open the Add User Modal
function openUserModal() {
    document.getElementById('addUserModal').style.display = 'block';
}

// Function to close the Add User Modal
function closeUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
}

// Function to handle adding a user
function addUser(event) {
    event.preventDefault(); // Prevent form submission

    const firstName = document.getElementById('newFirstName').value;
    const lastName = document.getElementById('newLastName').value;
    const email = document.getElementById('newEmail').value;
    const password = document.getElementById('newPassword').value;  // Default password
    const role = document.getElementById('newRole').value;

    // Validate inputs
    if (!firstName || !lastName || !email || !role) {
        alert('Please fill in all fields');
        return;
    }

    // Send the data to the server via POST request
    fetch('../actions/add_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'firstName': firstName,
            'lastName': lastName,
            'email': email,
            'password': password,
            'role': role
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User added successfully');
            closeUserModal();  // Close the modal after success
            location.reload();  // Optionally refresh the page to show the new user in the table
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the user.');
    });
}

  


// FORM SUBMISSION
// function submitEditForm(event) {
//   event.preventDefault();
//   const formData = new FormData(document.getElementById('editUserForm'));

//   fetch('../actions/edit_user.php', {
//       method: 'POST',
//       body: formData
//   })
//   .then(response => response.json())
//   .then(data => {
//       if (data.success) {
//           alert(data.message);
//           closeEditModal();
//           location.reload(); // Reload to see updated data
//       } else {
//           alert('Error: ' + data.message);
//       }
//   })
//   .catch(error => console.error('Error:', error));
// }
