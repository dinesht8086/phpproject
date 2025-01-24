<?php
include "layout/conn.php";    
include "layout/header.php"; 
include "layout/nav.php";    
?>

<h2 id = "createorder">Create Order</h2>


                       <!----------------------------------Search for Existing User------------------------------------------->

 <div>
            <h3 id ="searchuser_h3">Search Existing User</h3>
        <!-- <form id="searchForm" action="" method="POST" class="search-form">
            <label for="mobile">Enter Mobile Number:</label>
            <input type="number" id="phone" name="phone" required autocomplete="off">
            <button type="submit" name="searchUser">Search</button>
        </form> -->
        <div class="search-box">
            <form id="searchForm" action="" method="POST" >
    <button  type = "submit" name ="searchUser" class="btn-search"><i class="fas fa-search"></i></button>
    <input type="number" id ="phone" name ="phone" class="input-search" placeholder="Type to Search..." autocomplete="off" required>
</form>
  </div>

<?php
if (isset($_POST['searchUser'])) {
    $phone = $_POST['phone'];
    
    // Corrected SQL query to join customer_detail and customer_address
    $stmt = $conn->prepare("
        SELECT 
            cd.detail_id, 
            cd.fullname, 
            cd.phone, 
            GROUP_CONCAT(c.customer_address SEPARATOR ', ') AS addresses 
        FROM 
            customer_detail cd
        JOIN 
            customer_address c ON cd.detail_id = c.customer_id
        WHERE 
            cd.phone = ?
        GROUP BY 
            cd.detail_id, cd.fullname, cd.phone
    ");
    
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Display the user details along with addresses
       echo "<div id='olduserform'>";
echo "<p><strong>Name:</strong> " . htmlspecialchars($row['fullname']) . "</p>";
echo "<p><strong>Mobile:</strong> " . htmlspecialchars($row['phone']) . "</p>";

$addresses = explode(', ', $row['addresses']);
echo "<label for='userAddress'><strong>Address:</strong></label>";
echo "<select id='userAddress' name='userAddress' required>";
echo "<option value=''>-- Select an Address --</option>"; // Default option

// Generate options for each address
foreach ($addresses as $index => $address) {
    echo "<option value='" . htmlspecialchars($address) . "'>" . htmlspecialchars($address) . "</option>";
}

echo "</select>";
echo "<input type='hidden' id='existingUserId' name='existingUserId' value='" . htmlspecialchars($row['detail_id']) . "'>";
echo "</div>";

    } else {
                // If no user is found, show the "Create New User" form
                ?>
                <div id="newUserForm">
                    <h3>Create New User</h3>
                    <form id="userform" action="address.php" method="POST" autocomplete="off">
                        <label for="fullname">Full Name:</label>
                        <input type="text" id="fullname" name="fullname" required>

                        <label for="phone">Mobile Number:</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                        
                        <div id="addressFields">
                        <label for="user_address">Address</label>
                        <textarea id="auser_address" name="addresses[]"  rows="4" required></textarea>
                        </div>
                        <button type="button" id="addAddress">Add Another Address</button>
                        <!-- <label for="user_address">Address:</label>
                        <input type="text" id="user_address" name="address" required> -->

                        <button type="submit" id ="create_user" name="createuser">Create User</button>
                    </form>
                </div>
                <?php
            }
            $stmt->close();
        } else {
            // echo "<p>Please enter a valid mobile number.</p>";
        }
    
    ?>
</div>


                                      <!---------------------------------Main Form--------------------------------------->


<form id="orderForm" name="orderForm" action="order-detail.php" method="POST">
    <!-- Customer Selection -->
    <label id ="orderformlabel" for="customer">Customer:</label>
    <select id="customer" name="customer_id" required>
        <option value="">-- Select Customer --</option>
        <?php
        $sql = "SELECT detail_id, fullname FROM customer_detail";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['detail_id']) . "'>" . htmlspecialchars($row['fullname']) . "</option>";
            }
        } else {
            echo "<option value=''>No customers found</option>";
        }
        ?>
    </select><br>

    <!-- Selected Items Table -->
    <h3>Selected Items</h3>
    <table id="selectedItemsTable">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Available Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Initial row -->
            <tr>
                <td>
                    <select name="itemIds[]" class="itemDropdown" >
                        <option value="">-- Select Item --</option>
                        <?php
                        $sql = "SELECT id, particulars, quantity FROM stocks";
                        $result = $conn->query($sql);
                       
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                
                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['particulars']) . ' (' . htmlspecialchars($row['quantity']) . ' available)</option>';
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input name="itemQts[]" type="number" value="1" class="quantityInput" min="1" style="width:60px" />
                </td>
                <td>
                    <span class="availableQuantity">0</span>
                </td>
                <td>
                     <button type="button" class="removeRowButton"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
        </tbody>
    </table>

    <button id="orderitem_button" type="submit" name="submitOrder">Submit Order</button>
</form>

<script>
const selectedItemsTable = document.getElementById('selectedItemsTable').querySelector('tbody');

// Automatically add a new row when an item is selected
selectedItemsTable.addEventListener('change', (e) => {
    if (e.target.classList.contains('itemDropdown')) {
        const row = e.target.closest('tr');

        // Update available quantity for the selected item
        const selectedOption = e.target.options[e.target.selectedIndex];
        // console.log(selectedOption);
        const match = selectedOption.text.match(/\((\d+) available\)/);
        // console.log(match);
        const availableQuantity = match ? parseInt(match[1], 10) : 0;
        // console.log(availableQuantity);
        row.querySelector('.availableQuantity').textContent = availableQuantity;
        row.querySelector('.quantityInput').setAttribute('max', availableQuantity);

        // Check if a new row is needed
        if (row.nextElementSibling === null && e.target.value !== "") {
            addNewRow();
        }
    }
});

// Add a new row to the table
function addNewRow() {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="itemIds[]" class="itemDropdown" >
                <option value="">-- Select Item --</option>
                <?php
                $sql = "SELECT id, particulars, quantity FROM stocks";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['particulars']) . ' (' . htmlspecialchars($row['quantity']) . ' available)</option>';
                    }
                }
                ?>
            </select>
        </td>
        <td>
            <input name="itemQts[]" type="number" value="1" class="quantityInput" min="1" style="width:60px" />
        </td>
        <td>
            <span class="availableQuantity">0</span>
        </td>
        <td>
           <button type="button" class="removeRowButton"><i class="fas fa-trash-alt"></i></button>
        </td>
    `;
    selectedItemsTable.appendChild(row);

    
}

// Remove row functionality for initial row
selectedItemsTable.addEventListener('click', (e) => {
    if (e.target.classList.contains('removeRowButton')) {
        e.target.closest('tr').remove();
    }
});

document.getElementById('orderForm').addEventListener('submit', (e) => {
    const rows = selectedItemsTable.querySelectorAll('tr');
    rows.forEach(row => {
        const select = row.querySelector('select');
        if (select && select.value === "") {
            row.remove();  // Remove the row if the select option is empty
        }
    });
});





document.addEventListener('DOMContentLoaded', () => {
    const existingUserId = document.getElementById('existingUserId');
    if (existingUserId) {
        const customerDropdown = document.getElementById('customer');
        if (customerDropdown) {
            customerDropdown.value = existingUserId.value;
        }
    }

    const newUserForm = document.getElementById('userform');
    if (newUserForm) {
        newUserForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent the form from submitting the traditional way
            const formData = new FormData(newUserForm);

            // Submit the form data using fetch
            fetch('address.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Expect JSON response from the server
            .then(newUser => {
                // Check if newUser data is returned
                  console.log('New user added:', newUser);

                // Display new user details in the 'newUserForm' div
                // const userDetails = `
                //     <p><strong>Name:</strong> ${newUser.name}</p>
                //     <p><strong>Mobile:</strong> ${newUser.phone}</p>
                //     <p><strong>Address:</strong> ${newUser.addresses}</p>
                // `;

                  const addressOptions = newUser.addresses.map(address => 
        `<option value="${address}">${address}</option>`
    ).join('');

    const userDetails = `
        <p><strong>Name:</strong> ${newUser.name}</p>
        <p><strong>Mobile:</strong> ${newUser.phone}</p>
        <label for="addressSelect"><strong>Select Address:</strong></label>
        <select id="addressSelect" name="selectedAddress" required>
            ${addressOptions}
        </select>
    `;


                // Insert the user details into the 'newUserForm' element
                document.getElementById('newUserForm').innerHTML = userDetails;
                // document.getElementById('olduserform').innerHTML = userDetails;

                // Add the new user to the dropdown
                const customerDropdown = document.getElementById('customer');
                if (customerDropdown && newUser) {
                    const newOption = document.createElement('option');
                    newOption.value = newUser.id; // Set the value to the new user's ID
                    newOption.textContent = newUser.name; // Set the display name to the new user's name
                    
                    // Add the new option to the dropdown
                    customerDropdown.appendChild(newOption);
                    
                    // Optionally, select the newly added user
                    customerDropdown.value = newUser.id;

                    console.log('New user added to dropdown:', newOption);
                }
            })
            .catch(error => {
                // Handle fetch or server errors
                console.error('Error adding new user:', error);
            });
        });
    }
});

 document.getElementById('addAddress').addEventListener('click', function() {
        var addressFields = document.getElementById('addressFields');
        var newAddressField = document.createElement('div');
        newAddressField.innerHTML = `
            <label for="user_address">Address</label>
            <textarea name="addresses[]" rows="4" required></textarea>
        `;
        addressFields.appendChild(newAddressField);
    });


</script>

<?php
include "layout/footer.php";
?>