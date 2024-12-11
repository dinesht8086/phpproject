<?php
  require_once "conn.php"; // Ensure conn.php contains the correct database connection.
  include "layout/header.php";
  include "layout/nav.php";
  ?>
   

<header>
        <h1>Create Order</h1>
    </header>
    <main>
        <?php
        require_once "conn.php";

        // Fetch stock items
        $sql = "SELECT * FROM stocks";
        $result = $conn->query($sql);

        // Initialize stock items array
        $stockItems = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stockItems[] = $row;
            }
        }
        $conn->close();
        ?>
        <form id="orderForm" action="" method="">
            <!-- Customer Selection -->
            <label for="customer">Customer:</label>
            <select id="customer" name="customer_id" required>
                <!-- Populate dynamically using PHP -->
            </select>

            <!-- Item Selection -->
            <!-- <label for="items">Select Items:</label> -->
            <table id="itemsTable">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>GST Rate</th>
                        <th>Available Quantity</th>
                        <th>Order Quantity</th>
                        <th>Total Amount</th> <!-- Add this column -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stockItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['particulars']); ?></td>
                        <td><?php echo number_format($item['selling_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['gst_rate']); ?>%</td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>
                            <input type="number" name="items[<?php echo $item['id']; ?>][quantity]" 
                                   min="0" max="<?php echo $item['quantity']; ?>" value="0" class="quantity" data-price="<?php echo $item['selling_price']; ?>" data-gst="<?php echo $item['gst_rate']; ?>" data-available="<?php echo $item['quantity']; ?>">
                        </td>
                        <td><span class="total-amount">₹0.00</span></td> <!-- Total amount column -->
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button id = "orderlist" type="submit">Generate Bill</button>
        </form>
    </main>
    <?php
    include "layout/footer.php";
    ?>