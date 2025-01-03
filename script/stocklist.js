     const addStockButton = document.getElementById('add_stock');
        const modal = document.getElementById('modal');
        const closeModalButton = document.getElementById('closeModal');
        const form = document.getElementById('addStockForm');
        const overlay = document.getElementById('overlay');

        // Show modal
        addStockButton.addEventListener('click', () => {
            modal.style.display = 'block';
             overlay.style.display = 'block';
        });

        // Hide modal
        closeModalButton.addEventListener('click', () => {
            modal.style.display = 'none';
            overlay.style.display = 'none';
        });

        // Hide modal and overlay when clicking on the overlay itself
// overlay.addEventListener('click', () => {
//     modal.style.display = 'none';
//     overlay.style.display = 'none';
// });

        // Submit form using fetch
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            console.log(formData);
           
            fetch('stock.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(message => {
                alert(message); // Show success message
                modal.style.display = 'none'; // Hide modal
                 overlay.style.display = 'none';
                form.reset(); // Reset form

                // Refresh the stock list
                fetch('stocklist.php')
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        document.getElementById('stockTable').innerHTML = doc.getElementById('stockTable').innerHTML;
                    });
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        });