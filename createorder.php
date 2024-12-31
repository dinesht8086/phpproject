<?php
include "layout/conn.php";    
include "layout/header.php"; 
include "layout/nav.php";    
?>



<h2>Create Order</h2>

<!-- Main Form -->
<form id="orderForm" name="orderForm" action="order-detail.php" method="POST">
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
                <th>Quantity</th>
                <th>Available Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
           <!------------------------------------------------------------------>
        </tbody>
    </table>

    <button id = "orderitem_button"type="submit" name="submitOrder">Submit Order</button>
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
            $sql = "SELECT id, particulars, quantity FROM stocks";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['particulars']) . ' (' . htmlspecialchars($row['quantity']) . ' available)</option>';

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
const modal = document.getElementById('modal');
const overlay = document.getElementById('overlay');
const orderitem_button = document.getElementById('additem_button');
const modal_add_btn = document.getElementById('orderokbtn');
const selectedItemsTable = document.getElementById('selectedItemsTable').querySelector('tbody');
const itemselect = document.getElementById('item_select');

// Load selected items from localStorage
let selectedItems = JSON.parse(localStorage.getItem('selectedItems')) || [];
console.log(selectedItems);

// Display items on page load
function displayItems() {
    selectedItemsTable.innerHTML = ''; // Clear the table
    selectedItems.forEach(item => {
        addItemToTable(item.id, item.text, item.quantity, item.available);
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

    const selecteditemid = itemselect.value;  //itemid
    // console.log(selecteditemid);
    const selecteditemtext = itemselect.options[itemselect.selectedIndex].text;
    // console.log(selecteditemtext);

    if (!selecteditemid) {
        alert('Please select an item.');
        return;
    }

     /// Check if the item is already added
if (selectedItems.some(item => item.id === selecteditemid)) {
    alert('Item already added.');
    return;
}

    // Extract particulars and available quantity from the selected text
    const itemDetails = selecteditemtext.match(/(.*) \((\d+) available\)/);
    console.log(itemDetails);
    const particulars = itemDetails ? itemDetails[1] : selecteditemtext;
    console.log(particulars);
    const availableQuantity = itemDetails ? itemDetails[2] : 0;
    console.log(availableQuantity);
   
   

    // Add item to the table and array
    const item = {
        id: selecteditemid,
        text: particulars,
        quantity: 1, // default quantity
        available: availableQuantity // store available quantity
    };
    addItemToTable(item.id, item.text, item.quantity, item.available);
    selectedItems.push(item);

    // Save to localStorage
    localStorage.setItem('selectedItems', JSON.stringify(selectedItems));

    closeModal();
});

// Function to add a row to the table
function addItemToTable(id, text, quantity, available) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${id}<input type="hidden" name="itemIds[]" value="${id}" </td>
        <td>${text}</td>
        <td>
            <input name="itemQts[]" type="number" value="${quantity}" class="quantityInput" data-id="${id}" min="1" max="${available}" style="width:60px" />
        </td>
        <td>${available}</td>
        <td>
            <button type="button" class="removeItemBtn" data-id="${id}">Remove</button>
        </td>
    `;
    selectedItemsTable.appendChild(row);

    // Add event listener to the quantity input
     const quantityInput = row.querySelector('.quantityInput');
    //  quantityInput.addEventListener('input', (e) => {
    //     const newQuantity = e.target.value;
    //     const itemId = e.target.getAttribute('data-id');

    //     // Check if the entered quantity is within limits
    //     if (newQuantity > available) {
    //         alert(`Quantity cannot exceed available quantity (${available}).`);
    //         e.target.value = available; // Reset to max available
    //         return;
    //     }

    //     updateItemQuantity(itemId, newQuantity);
    // });



    // Add event listener to the quantity input
     quantityInput.addEventListener('input', (e) => {
    const newQuantity = parseInt(e.target.value, 10); // Get the new quantity
    const itemId = e.target.getAttribute('data-id'); // Get the item ID
    const maxQuantity = parseInt(e.target.getAttribute('max'), 10); // Get the max allowed quantity

    // Validate the entered quantity
    if (newQuantity > maxQuantity) {
        alert(`Quantity cannot exceed available quantity (${maxQuantity}).`);
        e.target.value = maxQuantity; // Reset to the max available if exceeded
        updateItemQuantity(itemId, maxQuantity); // Update with max quantity
        return;
    }

    if (newQuantity < 1 || isNaN(newQuantity)) {
        alert('Quantity must be at least 1.');
        e.target.value = 1; // Reset to minimum quantity
        updateItemQuantity(itemId, 1); // Update with minimum quantity
        return;
    }

    // If valid, update the quantity
    updateItemQuantity(itemId, newQuantity);
});


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

// Update the quantity of an item in the selectedItems array and localStorage
function updateItemQuantity(id, newQuantity) {
    selectedItems = selectedItems.map(item => {
        if (item.id === id) {
            item.quantity = parseInt(newQuantity, 10);
        }
        return item;
    });

    // Update localStorage
    localStorage.setItem('selectedItems', JSON.stringify(selectedItems));
}



// Clear localStorage on form submission
const orderForm = document.getElementById('orderForm');
orderForm.addEventListener('submit', () => {
    localStorage.removeItem('selectedItems');
    //  clearTable(); // Clear the table
     selectedItems = []; // Reset the array
    console.log('Local storage cleared after form submission.');
});

// Function to clear the table
function clearTable() {
    selectedItemsTable.innerHTML = '';
    console.log('Table cleared.');
}
<?php
   include "layout/conn.php";
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitOrder'])) {
      $itemIds = $_POST['itemIds']; 
      $itemQuantities = $_POST['itemQts']; 

   }
?>
        
</script>

<?php
include "layout/footer.php"; 
?>
