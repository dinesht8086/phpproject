<?php
require_once "conn.php";
include "layout/header.php";
include "layout/nav.php";
?>
    <div class="container" id="stock">
        <h2>List of Stock Items</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Particulars</th>
                    <th>MRP Price</th>
                    <th>Selling Price</th>
                    <th>GST Rate</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody id="stockTable">
                <?php
                $sql = "SELECT id, particulars, mrp_price, selling_price, gst_rate, quantity FROM stocks";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['id']) . "</td>
                                <td>" . htmlspecialchars($row['particulars']) . "</td>
                                <td>" . htmlspecialchars($row['mrp_price']) . "</td>
                                <td>" . htmlspecialchars($row['selling_price']) . "</td>
                                <td>" . htmlspecialchars($row['gst_rate']) . "</td>
                                <td>" . htmlspecialchars($row['quantity']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No stock items found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <button id="add_stock">Add Stocks</button>
    </div>

    <!-- Modal -->

    <!-- Overlay -->
<div id="overlay"></div>

    <div id="modal" >
        <form id="addStockForm" method  = "post">
            <h3>Add New Stock Item</h3>
            <label>Particulars: <input type="text" name="particulars" required></label><br>
            <label>MRP Price: <input type="text" name="mrp_price" required></label><br>
            <label>Selling Price: <input type="text" name="selling_price" required></label><br>
            <label>GST Rate: <input type="text" name="gst_rate" required></label><br>
            <label>Quantity: <input type="text" name="quantity" required></label><br><br>
            <button type="submit">Submit</button>
            <button type="button" id="closeModal">Close</button>
        </form>
    </div>

    

    <script>
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
overlay.addEventListener('click', () => {
    modal.style.display = 'none';
    overlay.style.display = 'none';
});

        // Submit form using fetch
        form.addEventListener('submit', (e) => {
            // e.preventDefault();

            const formData = new FormData(form);
           
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
    </script>
<?php
require "layout/footer.php"?>
