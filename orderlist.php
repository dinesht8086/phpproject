<?php
ini_set('display_errors', E_ALL);
error_reporting(1);
include "layout/conn.php";
include "layout/nav.php";
include "layout/header.php"; 
?>
<?php
// Fetch all customers to populate the dropdown for selecting a customer
$customerSql = "SELECT detail_id, fullname FROM customer_detail";
$customerResult =  $conn->query($customerSql);

// Check if a customer is selected
$selectedCustomerId = isset($_GET['customer_id']) ? $_GET['customer_id'] : null;

// Fetch the order list for the selected customer
$orderListSql = "SELECT o.order_id, o.order_subtotal, o.discount_total, o.order_total
                     FROM billingsystem.order o "; //Order By o.order_id DESC
if ($selectedCustomerId) {
    $orderListSql .= " WHERE o.user_id = '" . $conn->real_escape_string($selectedCustomerId) . "'";
}

// echo $orderListSql;
$orderListSql .= " ORDER BY o.order_id DESC";
$orderListResult = $conn->query($orderListSql);

?>

<div class="container">
    <h2>Order List</h2>

    <!-- Customer selection form -->
    <form method="get" action="orderlist.php">
        <label for="customer_id">Select Customer:</label>
        <select name="customer_id" id="customer_id" onchange="this.form.submit()">
            <option value="">-- Select Customer --</option>
            <?php
            if ($customerResult && $customerResult->num_rows > 0) {
                while ($customer = $customerResult->fetch_assoc()) {
                    $selected = $selectedCustomerId == $customer['detail_id'] ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($customer['detail_id']) . "' $selected>" . htmlspecialchars($customer['fullname']) . "</option>";
                }
            }
            ?>
        </select>
    </form>

    <hr>

        <h3>Orders for Customer: <?php echo htmlspecialchars($selectedCustomerId); ?></h3>

        <!-- Order List Table -->
        <?php if ($orderListResult && $orderListResult->num_rows > 0): ?>
            <table id="orderListTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($order = $orderListResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['order_subtotal']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['discount_total']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['order_total']) . "</td>";
                        echo "<td><a href='order-detail.php?order_id=" . urlencode($order['order_id']) . "'>View Details</a></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found for this customer.</p>
        <?php endif; ?>
</div>

<?php
include "layout/footer.php";
?>
