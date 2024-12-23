<?php
require_once "layout/conn.php"; // Connect to the database

// Check if the form is submitted via POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $particulars = $_POST['particulars'];
    $mrp_price = $_POST['mrp_price'];
    $selling_price = $_POST['selling_price'];
    $gst_rate = $_POST['gst_rate'];
    $quantity = $_POST['quantity'];

    // SQL query to insert data into the database
    $sql = "INSERT INTO stocks (particulars, mrp_price, selling_price, gst_rate, quantity) 
            VALUES ('$particulars', '$mrp_price', '$selling_price', '$gst_rate', '$quantity')";

    // Execute the query and check for success
    if ($conn->query($sql) === TRUE) {
        echo "Stock item added successfully!";
    } else {
        echo "Error: " . $conn->error; // If there's an error
    }

    $conn->close(); // Close the database connection
}
?>
