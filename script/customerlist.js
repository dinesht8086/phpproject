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

document.getElementById('addAddress').addEventListener('click', function () {
        const addressFields = document.getElementById('addressFields');
        const addressCount = addressFields.getElementsByTagName('textarea').length + 1;

        // Create a new label
        const newLabel = document.createElement('label');
        newLabel.setAttribute('for', `address-${addressCount}`);
        newLabel.textContent = `Address ${addressCount}`;

        // Create a new textarea
        const newTextarea = document.createElement('textarea');
        newTextarea.setAttribute('id', `address-${addressCount}`);
        newTextarea.setAttribute('name', 'addresses[]');
        newTextarea.setAttribute('placeholder', 'Enter your address');
        newTextarea.setAttribute('rows', '4');
        newTextarea.setAttribute('required', true);

        // Append the new label and textarea
        addressFields.appendChild(newLabel);
        addressFields.appendChild(newTextarea);
    }); 