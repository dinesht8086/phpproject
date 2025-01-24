<?php
// Include database connection
include "layout/conn.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form values
    $shop_name = $_POST['shop_name'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // File upload logic
    $shop_logo = $_FILES['shop_logo']['name']; // Get the file name
    $target_dir = "uploads/"; // Directory to save the file
    $target_file = $target_dir . basename($shop_logo); // Full path of the file

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['shop_logo']['tmp_name'], $target_file)) {
        // Insert query
        $sql = "INSERT INTO shop_details (shop_name, shop_logo, latitude, longitude) 
                VALUES ('$shop_name', '$shop_logo', '$latitude', '$longitude')";

        // Execute the query
        if (mysqli_query($conn, $sql)) {
            echo "Shop details added successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload the shop logo.";
    }
}
?>
