<?php
 ini_set('display_errors', E_ALL);

error_reporting(1);
$lastOrderId = "";
include "layout/conn.php";
if(isset($_POST['submitOrder'])) {
    $itemIds = $_POST['itemIds'];
    $customerId= $_POST['customer_id'];
    $itemQtys = $_POST['itemQts'];
    
    $sql = "SELECT * FROM `stocks` WHERE id in (".implode(',', $itemIds).")";

    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        $orderSubTotal = 0;
        $rowIndex = 0;
        $itemList = array();
        while($row = $result->fetch_assoc()){
            $itemSubTotal = $row['selling_price'] * $itemQtys[$rowIndex];
            $orderSubTotal+= $itemSubTotal;
            $itemDiscount = 0;
            $cgst = 0;
            $sgst = 0;
            $total = $itemSubTotal + $cgst + $sgst - $itemDiscount;
            $itemRow = array(
                "id"=> $row["id"],
                "price"=>$row["selling_price"],
                "qty"=>$itemQtys[$rowIndex],
                "sub_total"=> $itemSubTotal,
                "total"=> $total
            );
            $itemList[] = $itemRow;
            $rowIndex++;
        }
        $discountTotal =  0; // To Do based discount percentage and coupon code, eg (orderSubTotal*5%)
        $orderTotal = $orderSubTotal - $discountTotal;
        $orderSql = "insert into billingsystem.order(user_id, order_subtotal, discount_total, order_total) values('$customerId', '$orderSubTotal', '$discountTotal', '$orderTotal')";
        $result = $conn->query($orderSql);
        
        if(!$result) {
            header('location:'.$_SERVER["HTTP_REFERER"]);
            exit(0);
        } else {
            $lastOrderId = $conn->insert_id;
            //echo '<pre>';print_r($itemList); echo '</pre>';             ////////////
            $itemsSql = "insert into order_items(order_id, stock_item_id, quantity, price, sub_total, total) values";
            for($rowIndex = 0; $rowIndex <  count($itemList); $rowIndex++) {
                $itemRow = $itemList[$rowIndex];
                 if($rowIndex > 0) {
                    $itemsSql.=",";
                }
                $itemsSql.="($lastOrderId, ".$itemRow['id'].", ".$itemRow['qty'].", ".$itemRow['price'].", ".$itemRow['sub_total'].", ".$itemRow['total'].")";
            }
            $itemInsert = $conn->query($itemsSql);

             // Update stock quantity after order is placed
             foreach($itemList as $itemRow){
                $stockId = $itemRow['id'];
                $orderedQty = $itemRow['qty'];
                $updateStockSql = "UPDATE stocks SET quantity = quantity - $orderedQty WHERE id = $stockId";
                // print_r($updateStockSql);
                $conn->query($updateStockSql); 
             }


        }
    }
}

$orderSelectSql = "select * from billingsystem.order";
$orderResult = $conn->query($orderSelectSql);

//echo '<pre>';print_r($orderResult); echo '</pre>';                        ///////////

?>

<?php   
include "layout/header.php"; 
//  include "layout/nav.php"; 
include "layout/conn.php";   

$orderDetailId = $lastOrderId;
if (isset($_GET['order_id'])) {
    $orderDetailId = intval($_GET['order_id']);
}

$orderSelectSql = "
    SELECT o.order_id, o.order_subtotal, o.discount_total, o.order_total, 
           cd.fullname, cd.phone, GROUP_CONCAT(ca.customer_address SEPARATOR ', ') AS addresses
    FROM billingsystem.order AS o
    JOIN customer_detail AS cd ON o.user_id = cd.detail_id
    LEFT JOIN customer_address AS ca ON cd.detail_id = ca.customer_id
    WHERE o.order_id = $orderDetailId
    GROUP BY o.order_id, cd.detail_id
";
$orderResult = $conn->query($orderSelectSql);
$orderData = $orderResult ? $orderResult->fetch_assoc() : null;

$orderItemsSql = "
    SELECT oi.order_item_id, s.particulars, oi.quantity, oi.sub_total 
    FROM order_items oi 
    JOIN stocks s ON oi.stock_item_id = s.id 
    WHERE oi.order_id = $orderDetailId
";
$orderItemsResult = $conn->query($orderItemsSql);
?>
<div class="main-container">
    <div class="printbtn-container">
        <button class="printbtn" onclick="window.print()">Print</button>
    </div>
    <hr>
    <div class="order_container">
        <h3 class="htag_order">CRACKERS</h3>
        <hr>
        <?php if ($orderData): ?>
            <h5>Bill to:</h5>
            <p><strong>Name:</strong> <?= htmlspecialchars($orderData['fullname']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($orderData['addresses']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($orderData['phone']) ?></p>
            <hr>
        <?php else: ?>
            <p>No order details found.</p>
        <?php endif; ?>
    </div>
    <table id="selectedItemsTable">
        <thead>
            <tr>
                <th>No.</th>
                <th>Particulars</th>
                <th>Quantity</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($orderItemsResult && $orderItemsResult->num_rows > 0): ?>
                <?php while ($row = $orderItemsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['order_item_id']) ?></td>
                        <td><?= htmlspecialchars($row['particulars']) ?></td>
                        <td><?= htmlspecialchars($row['quantity']) ?></td>
                        <td><?= htmlspecialchars($row['sub_total']) ?></td>
                    </tr>
                <?php endwhile; ?>
                <tr style="background-color:grey;">
                    <td colspan="3" style="text-align: center; color:white; font-weight: bold;">Total:</td>
                    <td style="color:white; font-weight: bold;"><?= htmlspecialchars($orderData['order_total']) ?></td>
                </tr>
            <?php else: ?>
                <tr><td colspan="4">No items found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include "layout/footer.php"; ?>
   