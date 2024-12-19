<?php
require_once "layout/conn.php";
include "layout/header.php";
include "layout/nav.php";
?>
    <div class="container" id="stock">
        <h2>List of Stock Items</h2>
         <button id="add_stock">Add Stocks</button>
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
                // print_r($result);

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
                // ?>
            </tbody>
        </table>
       
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
 <script src = "script/stocklist.js"></script>
<?php
require "layout/footer.php"?>
