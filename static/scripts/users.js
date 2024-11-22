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
                document.getElementById('newRole-1').value = data.user.role;
        

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
    const role = document.getElementById('newRole-1').value;

    console.log('Updating User:', { userId, email, name, role }); // Debugging

    // Send updated data using POST request
    fetch('../actions/edit_user_POST.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'id': userId,
            'email': email,
            'name': name,
            'role': role
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message); // Show success message
            closeEditModal();   // Close the modal
            location.reload();  // Reload to reflect changes
        } else {
            alert('Error: ' + data.message); // Show error message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the user.');
    });
}

function closeEditModal() {
    document.getElementById('editUserModal').style.display = 'none';
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
    const height = document.getElementById('height').value;
    const weight = document.getElementById('weight').value;

    // Validate inputs
    if (!firstName || !lastName || !email || !role || !height || !weight) {
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
            'role': role,
            'height':height,
            'weight':weight
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

