<?php
require_once "layout/conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['fullname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    // SQL to insert user details into the database
    $sql = "INSERT INTO userdetail (fullname, user_address, phone) 
            VALUES ('$name', '$address', '$phone')";

    if ($conn->query($sql) === TRUE) {
        // Get the last inserted ID (new user's ID)
        $newUserId = $conn->insert_id;

        // Prepare the response with the newly inserted user details
        $response = [
            'id' => $newUserId,
            'name' => $name,  // New user's name
            'address' => $address, // New user's address
            'phone' => $phone // New user's phone
        ];

       
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // If there is an error, return an error message
        echo json_encode(['error' => 'Error: ' . $conn->error]);
    }

    // Close the database connection
    $conn->close();
}
?>
