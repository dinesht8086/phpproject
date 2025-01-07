    const addCustomerButton = document.getElementById('customer');
    const modal = document.getElementById('modal');
    const closeModalButton = document.getElementById('closeModal');
    const overlay = document.getElementById('overlay');
    const form = document.getElementById('userForm');
    
    // Show modal
    addCustomerButton.addEventListener('click', () => {
         console.log("Add Customer button clicked");
        modal.style.display = 'block';
         overlay.style.display = 'block';
    });
    
    // Hide modal
    closeModalButton.addEventListener('click', () => {
        modal.style.display = 'none';
        overlay.style.display = 'none';
    });

    // Submit form using fetch
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        fetch('address.php', {
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
            fetch('customerlist.php')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    document.getElementById('customertable').innerHTML = doc.getElementById('customertable').innerHTML;
                });
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    });

