<?php
 ini_set('display_errors', E_ALL);

error_reporting(1);
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


// Fetch the latest order's items to display in the table
$orderItemsSql = "SELECT oi.order_item_id, s.particulars, oi.quantity, oi.sub_total 
                  FROM order_items oi 
                  JOIN stocks s ON oi.stock_item_id = s.id 
                  WHERE oi.order_id =".$_GET['order_id'];
                  
$orderItemsResult = $conn->query($orderItemsSql);



// Fetch the latest order and customer details from userdetail table
$orderSelectSql = "SELECT o.order_id, o.order_subtotal, o.discount_total, o.order_total, ud.fullname, ud.user_address, ud.phone 
                   FROM billingsystem.order o
                   JOIN userdetail ud ON o.user_id = ud.detail_id
                   WHERE o.order_id = (SELECT MAX(order_id) FROM billingsystem.order)";
            
                   
$orderResult = $conn->query($orderSelectSql);


?>
<div class = "main-container">
<div class="printbtn-container">
     <button class="printbtn" onclick="window.print()">Print</button>
</div>
<hr>

<div class = " order_container">
<h3 class = "htag_order">CRACKERS</h2>

<hr>

<!-- <h2>Order Details</h2> -->

<?php
if ($orderResult && $orderResult->num_rows > 0) {
    // Fetch customer and order details
    $orderData = $orderResult->fetch_assoc();
    echo "<h5>Bill to:</h3>";
    echo "<p>" . htmlspecialchars($orderData['fullname']) ."</p>" ;
    echo  "<p>" . htmlspecialchars($orderData['user_address']) . "<p>";
    echo "<p><strong>phone:</strong> " . htmlspecialchars($orderData['phone']) . "</p>";
    // echo "<h3>Order Summary</h3>";
    // echo "<p><strong>Order ID:</strong> " . htmlspecialchars($orderData['order_id']) . "</p>";
    // echo "<p><strong>Subtotal:</strong> " . htmlspecialchars($orderData['order_subtotal']) . "</p>";
    // echo "<p><strong>Discount:</strong> " . htmlspecialchars($orderData['discount_total']) . "</p>";
    //  echo "<p><strong>Total:</strong> " . htmlspecialchars($orderData['order_total']) . "</p>";
} else {
    echo "<p>No order details found.</p>";
}
?>
<hr>

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
        <?php
        if ($orderItemsResult && $orderItemsResult->num_rows > 0) {
            while ($row = $orderItemsResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['order_item_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['particulars']) . "</td>";
                echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sub_total']) . "</td>";
                echo "</tr>";
            }
            // Add a row for the total
            echo "<tr  style='background-color:grey;'>";
            echo "<td colspan='3' style='text-align: center; color:white; font-weight: bold;'>Total:</td>";
            echo "<td style=' color:white; font-weight: bold;'>" . htmlspecialchars($orderData['order_total']) . "</td>";
            echo "</tr>";
        } else {
            echo "<tr><td colspan='4'>No items found</td></tr>";
        }
        ?>
    </tbody>
</table>
</div>   
 <?php
include "layout/footer.php"; 
?>
   