<?php
require_once "layout/conn.php"; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['fullname']; 
    $phone = $_POST['phone'];   
    $addresses = $_POST['addresses']; 

    // Insert user details into the `userdetail` table
    $sql = "INSERT INTO customer_detail (fullname, phone) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $name, $phone);

        if ($stmt->execute()) {
            
            $newUserId = $conn->insert_id;
            // print_r($newUserId);
            // error_log($newUserId);

            // Insert each address into the `customer_address` table
            foreach ($addresses as $address) {
                $sqlAddress = "INSERT INTO customer_address (customer_id, customer_address) VALUES (?, ?)";
                $stmtAddress = $conn->prepare($sqlAddress);

                if ($stmtAddress) {
                    $stmtAddress->bind_param("is", $newUserId, $address); 
                    $stmtAddress->execute(); 
                    $stmtAddress->close();  
                } else {
                    // Show an error if the address statement could not be prepared
                    echo json_encode(['error' => 'Failed to prepare address query.']);
                    exit; 
                }
            }

            // Prepare the response with the user's details
            $response = [
                'id' => $newUserId,
                'name' => $name,
                'phone' => $phone,
                'addresses' => $addresses
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // If user details insertion fails, show an error
            echo json_encode(['error' => 'Failed to insert user details.']);
        }
    } else {
        // If the user details statement couldn't be prepared
        echo json_encode(['error' => 'Failed to prepare user query.']);
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>
