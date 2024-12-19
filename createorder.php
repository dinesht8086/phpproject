<?php
include "layout/conn.php";    
include "layout/header.php"; 
include "layout/nav.php";    
?>

<h2>Create Order</h2>

<!-- Main Form -->
<form id="orderForm" action="" method="POST">
    <!-- Customer Selection -->
    <label for="customer">Customer:</label>
    <select id="customer" name="customer_id" required>
        <option value="">-- Select Customer --</option>
        <?php
        $sql = "SELECT detail_id, fullname FROM userdetail";
        $result = $conn->query($sql);
        // print_r($result);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // print_r($row);
                echo "<option value='" . htmlspecialchars($row['detail_id']) . "'>" . htmlspecialchars($row['fullname']) . "</option>";
            }
        } else {
            echo "<option value=''>No customers found</option>";
        }
        ?>
    </select><br>

    <!-- Add Item Button -->
    <button type="button" id="additem_button">Add Item</button>

    <!-- Selected Items Table -->
    <h3>Selected Items</h3>
    <table id="selectedItemsTable" >
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Particulars</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
           <!------------------------------------------------------------------>
        </tbody>
    </table>

    <button id = "orderitem_button"type="submit">Submit Order</button>
</form>

<!-- Modal and Overlay -->
<div id="overlay"></div>
<div id="modal">
    <h2 >Select Item</h2>
    <form id="order_form">
        <!-- Dropdown for Items -->
        <label for="item_select">Item:</label>
        <select name="item_select" id="item_select" required>
            <option value="">-- Select Item --</option>
            <?php
            // Fetch items from the 'stocks' table
            $sql = "SELECT id, particulars FROM stocks";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['particulars']) . '</option>';
                }
            } else {
                echo '<option value="">No items available</option>';
            }
            ?>
        </select>
        <button  id="orderokbtn">Add</button>
        <button  id="orderremovebtn">Remove</button>
    </form>
</div>


<script>
    // const modal = document.getElementById('modal');
    // const overlay = document.getElementById('overlay');
    // const orderitem_button = document.getElementById('additem_button');
    // const modal_add_btn = document.getElementById('orderokbtn');
    // const modal_remove_btn = document.getElementById('orderremovebtn');
    // const selectedItemsTable = document.getElementById('selectedItemsTable').querySelector('tbody');
    // const itemselect = document.getElementById('item_select');
    // const form = document.getElementById('order_form');

    //  function displayItems() {
    //     selectedItemsTable.innerHTML = ''; // Clear the table
    //     selectedItems.forEach(item => {
    //         addItemToTable(item.id, item.text);
    //     });
    // }
    // displayItems(); // Call on page load


    // // submit button
    // orderitem_button.addEventListener('click', () => {
    //     modal.style.display = 'block';
    //     overlay.style.display = 'block';
    // });

    // // Function to close modal
    // function closeModal() {
    //     modal.style.display = 'none';
    //     overlay.style.display = 'none';
    //     itemselect.value = ""; // Reset dropdown
    // }

    //  overlay.addEventListener('click', closeModal);

          
    // modal_add_btn.addEventListener('click', (e) => {
    //     e.preventDefault();

        
    //     const selecteditemid = itemselect.value;
    //     const selecteditemtext = itemselect.options[itemselect.selectedIndex].text;


    //     if (!selecteditemid) {
    //         alert('Please select an item.');
    //         return;
    //     }

    //     if (selectedItems.includes(selecteditemid)) {
    //          alert('Item already added.');
    //          return;
    //     }
        

    //     // Add item to the table and array
    //     addItemToTable(selecteditemid, selecteditemtext);
    //     selectedItems.push({ id: selecteditemid, text: selecteditemtext });

    //     // Save to localStorage
    //     localStorage.setItem('selectedItems', JSON.stringify(selectedItems));

    //     closeModal();

    // });



    //     function addItemToTable(id, text) {
    //         const row = document.createElement('tr');
    //         row.innerHTML = `
    //             <td>${id}</td>
    //             <td>${text}</td>
    //             <td><button type="button" class="removeItemBtn" data-id="${id}">Remove</button></td>
    //         `;
    //         selectedItemsTable.appendChild(row);

    //         // Add event listener to remove button
    //         row.querySelector('.removeItemBtn').addEventListener('click', (e) => {
    //             const button = e.target;
    //             const idToRemove = button.getAttribute('data-id');

    //             // Remove item from table and array
    //             button.closest('tr').remove();
    //             selectedItems = selectedItems.filter(item => item.id !== idToRemove);

    //             // Update localStorage
    //             localStorage.setItem('selectedItems', JSON.stringify(selectedItems));
    //         });

    // }





    const modal = document.getElementById('modal');
    const overlay = document.getElementById('overlay');
    const orderitem_button = document.getElementById('additem_button');
    const modal_add_btn = document.getElementById('orderokbtn');
    const selectedItemsTable = document.getElementById('selectedItemsTable').querySelector('tbody');
    const itemselect = document.getElementById('item_select');

    // Load selected items from localStorage
    let selectedItems = JSON.parse(localStorage.getItem('selectedItems')) || [];

    // Display items on page load
    function displayItems() {
        selectedItemsTable.innerHTML = ''; // Clear the table
        selectedItems.forEach(item => {
            addItemToTable(item.id, item.text);
        });
    }
    displayItems(); // Call on page load

    // Open modal
    orderitem_button.addEventListener('click', () => {
        modal.style.display = 'block';
        overlay.style.display = 'block';
    });

    // Close modal
    function closeModal() {
        modal.style.display = 'none';
        overlay.style.display = 'none';
        itemselect.value = ""; // Reset dropdown
    }
    overlay.addEventListener('click', closeModal);

    // Add item from modal to table
    modal_add_btn.addEventListener('click', (e) => {
        e.preventDefault();

        const selecteditemid = itemselect.value;
        const selecteditemtext = itemselect.options[itemselect.selectedIndex].text;

        if (!selecteditemid) {
            alert('Please select an item.');
            return;
        }

        if (selectedItems.some(item => item.id === selecteditemid)) {
            alert('Item already added.');
            return;
        }

        // Add item to the table and array
        addItemToTable(selecteditemid, selecteditemtext);
        selectedItems.push({ id: selecteditemid, text: selecteditemtext });

        // Save to localStorage
        localStorage.setItem('selectedItems', JSON.stringify(selectedItems));

        closeModal();
    });

    // Function to add a row to the table
    function addItemToTable(id, text) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${id}</td>
            <td>${text}</td>
            <td><button type="button" class="removeItemBtn" data-id="${id}">Remove</button></td>
        `;
        selectedItemsTable.appendChild(row);

        // Add event listener to remove button
        row.querySelector('.removeItemBtn').addEventListener('click', (e) => {
            const button = e.target;
            const idToRemove = button.getAttribute('data-id');

            // Remove item from table and array
            button.closest('tr').remove();
            selectedItems = selectedItems.filter(item => item.id !== idToRemove);

            // Update localStorage
            localStorage.setItem('selectedItems', JSON.stringify(selectedItems));
        });
    }


//     orderitem_button.addEventListener('click', () => {
//         modal.style.display = 'block';
//         overlay.style.display = 'block';
//     });

//     // Function to close modal
//     function closeModal() {
//     modal.style.display = 'none';
//     overlay.style.display = 'none';
//     itemSelect.value = ""; // Reset dropdown
// }


//     // Close modal when overlay is clicked
//     overlay.addEventListener('click', closeModal);
//     let selectedItems = [];

//     modal_add_btn.addEventListener('click',(e)=>{

//          e.preventDefault();
         
//         const selecteditemid = itemselect.value;
//         const selecteditemtext = itemselect.options[itemselect.selectedIndex].text;
//         // console.log(selecteditemtext);

//         if(!selecteditemid ){
//             alert('please select an item.');
//             return;
//         }

//         if (selectedItems.includes(selecteditemid)) {
//             alert('Item already added.');
//             return;
//         }


//          const row = document.createElement('tr');
//         row.innerHTML = `
//             <td>${selecteditemid}</td>
//             <td>${selecteditemtext}</td>
//             <td><button type="button" class="removeItemBtn" data-id="">Remove</button></td>`;
             

//               selectedItemsTable.appendChild(row);

//               // Add item to the list of selected items  
//               selectedItems.push(selecteditemid);
           
//                // Add event listener to remove button dynamically
//     row.querySelector('.removeItemBtn').addEventListener('click', (e) => {
//         const button = e.target;
//         const idToRemove = button.getAttribute('data-id');

//         // Remove row from table
//         button.closest('tr').remove();

//         // Remove ID from the selectedItems array
//         selectedItems = selectedItems.filter(item => item !== idToRemove);
//     });
               
//                  closeModal();

//         });





//     const modal = document.getElementById('modal');
//     const overlay = document.getElementById('overlay');
//     const orderitem_button = document.getElementById('additem_button');
//     const modal_add_btn = document.getElementById('orderokbtn');
//     const selectedItemsTable = document.getElementById('selectedItemsTable').querySelector('tbody');
//     const itemselect = document.getElementById('item_select');

//     // Load selected items from localStorage
//     let selectedItems = JSON.parse(localStorage.getItem('selectedItems')) || [];

//     // Display items on page load
//     function displayItems() {
//         selectedItemsTable.innerHTML = ''; // Clear the table
//         selectedItems.forEach(item => {
//             addItemToTable(item.id, item.text);
//         });
//     }
//     displayItems(); // Call on page load

//     orderitem_button.addEventListener('click', () => {
//         modal.style.display = 'block';
//         overlay.style.display = 'block';
//     });

//     // Function to close modal
//     function closeModal() {
//         modal.style.display = 'none';
//         overlay.style.display = 'none';
//         itemselect.value = ""; // Reset dropdown
//     }

//     overlay.addEventListener('click', closeModal);

//     modal_add_btn.addEventListener('click', (e) => {
//         e.preventDefault();

//         const selecteditemid = itemselect.value;
//         const selecteditemtext = itemselect.options[itemselect.selectedIndex].text;

//         if (!selecteditemid) {
//             alert('Please select an item.');
//             return;
//         }

//         if (selectedItems.some(item => item.id === selecteditemid)) {
//             alert('Item already added.');
//             return;
//         }

//         // Add item to the table and array
//         addItemToTable(selecteditemid, selecteditemtext);
//         selectedItems.push({ id: selecteditemid, text: selecteditemtext });

//         // Save to localStorage
//         localStorage.setItem('selectedItems', JSON.stringify(selectedItems));

//         closeModal();
//     });

//     function addItemToTable(id, text) {
//         const row = document.createElement('tr');
//         row.innerHTML = `
//             <td>${id}</td>
//             <td>${text}</td>
//             <td><button type="button" class="removeItemBtn" data-id="${id}">Remove</button></td>
//         `;
//         selectedItemsTable.appendChild(row);

//         // Add event listener to remove button
//         row.querySelector('.removeItemBtn').addEventListener('click', (e) => {
//             const button = e.target;
//             const idToRemove = button.getAttribute('data-id');

//             // Remove item from table and array
//             button.closest('tr').remove();
//             selectedItems = selectedItems.filter(item => item.id !== idToRemove);

//             // Update localStorage
//             localStorage.setItem('selectedItems', JSON.stringify(selectedItems));
//         });
//     }


        
</script>

<?php
include "layout/footer.php"; 
?>
