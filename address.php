<?php
require_once "layout/conn.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['fullname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    $sql = "INSERT INTO userdetail (fullname, user_address, phone) 
            VALUES ('$name', '$address', '$phone')";

    if ($conn->query($sql) === TRUE) {
        echo "Customer  details added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>

