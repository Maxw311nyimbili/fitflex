
function openEntryModal() {
    document.getElementById('myModal').style.display = 'block';
}

function closeEntryModal() {
    document.getElementById('myModal').style.display = 'none';
}

function openWeightModal() {
    document.getElementById('myModal-1').style.display = 'block';
}



function closeWeightModal() {
    document.getElementById('myModal-1').style.display = 'none';
}




// IMAGE STUFF
function openImageModal(userId) {
    document.getElementById("imageModal").style.display = "block";
    loadImages(userId);
}

function closeImageModal() {
    document.getElementById("imageModal").style.display = "none";
}

function loadImages(userId) {
    const imageContainer = document.getElementById("imageContainer");
    imageContainer.innerHTML = ''; // Clear previous images

    // Show a loading message or spinner while fetching images
    imageContainer.innerHTML = '<p>Loading images...</p>';

    fetch(`../templates/view_progress_pictures.php?user_id=${userId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const pictures = data.pictures;
                if (pictures.length > 0) {
                    pictures.forEach(picture => {
                        const img = document.createElement("img");
                        img.src = picture.image_url;
                        img.alt = "Progress Image";
                        img.style.width = "200px";
                        img.style.margin = "10px";
                        imageContainer.appendChild(img);
                    });
                } else {
                    imageContainer.innerHTML = '<p>No progress pictures available.</p>';
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            imageContainer.innerHTML = '<p>Failed to load images. Please check your connection or try again later.</p>';
            // Optionally, you can add a retry button
            const retryButton = document.createElement('button');
            retryButton.innerText = 'Retry';
            retryButton.onclick = () => loadImages(userId);
            imageContainer.appendChild(retryButton);
        });
        
}

