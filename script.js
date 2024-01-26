// Fetch data from PHP endpoint
fetch('get_images.php') // Replace with your PHP file URL
    .then(response => response.json())
    .then(images => {
        const imageGrid = document.getElementById('imageGrid');

        images.forEach(image => {
            const gridItem = document.createElement('div');
            gridItem.classList.add('grid-item');

            const img = document.createElement('img');
            img.src = image.imageUrl; // Replace 'imageUrl' with your image URL property in the database
            img.alt = image.title; // Replace 'title' with your image title property

            gridItem.appendChild(img);
            imageGrid.appendChild(gridItem);
        });
    })
    .catch(error => console.error('Error:', error));
