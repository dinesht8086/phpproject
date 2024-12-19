<?php
include "layout/conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $customer_id =  $_POST['customer_id'];
   

$conn->close();
?>
