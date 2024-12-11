<?php
require_once "conn.php";


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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details Form</title>
    <link rel = "stylesheet" href = "address.css">
   
</head>
<body>
    <div class="form-container">
        <h1>User Details</h1>
        <form action="" method = "POST">
            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="fullname" placeholder="Enter your full name" required>

            <label for="address">Address</label>
            <textarea id="address" name="address" placeholder="Enter your address" rows="4" required></textarea>

            <label for="phone">Phone Number</label>
            <input type="number" id="phone" name="phone" placeholder="Enter your phone number" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
