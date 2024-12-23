<?php
ini_set('display_erros', E_ALL);
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
            echo '<pre>';print_r($itemList); echo '</pre>';
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

echo '<pre>';print_r($orderResult); echo '</pre>';

?>

<?php   
include "layout/header.php"; 
include "layout/nav.php";
include "layout/conn.php";   
?>
 <table id="selectedItemsTable" >
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Particulars</th>
                <th>Quantity</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
           
        </tbody>
    </table>

 <?php
include "layout/footer.php"; 
?>
   