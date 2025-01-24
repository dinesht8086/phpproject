<?php
require_once "layout/conn.php";
include "layout/header.php";
include "layout/nav.php";
?>
<?php
$sql = "
    SELECT 
        cd.detail_id, 
        cd.fullname, 
        cd.phone, 
        GROUP_CONCAT(c.customer_address SEPARATOR ', ') AS addresses 
    FROM 
        customer_detail cd
    LEFT JOIN 
        customer_address c 
    ON 
        cd.detail_id = c.customer_id
    GROUP BY 
        cd.detail_id, cd.fullname, cd.phone
";
$result = $conn->query($sql);
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<div id="customer-section" class="container">
    <h1>List of Users</h1>
      <button id="customer">Add Customer</button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Address</th>
                <th>Phone Number</th>
            </tr>
        </thead>
        <tbody id="customertable">
          <?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['detail_id']) . "</td>
                <td>" . htmlspecialchars($row['fullname']) . "</td>
                <td>" . htmlspecialchars($row['addresses']) . "</td>
                <td>" . htmlspecialchars($row['phone']) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No users found.</td></tr>";
}
?>

        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="overlay"></div>
<div id="modal">
    <div class="form-container" id="form-container">
        <h1>User Details</h1>
        <form id="userForm" method="POST">
            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="fullname" placeholder="Enter your full name" required>

            
            <label for="phone">Phone Number</label>
            <input type="number" id="phone" name="phone" placeholder="Enter your phone number" required>
            
            <div id="addressFields">
            <label for="address-1">Address</label>
            <textarea id="address-1" name="addresses[]" placeholder="Enter your address" rows="4" required></textarea>
            </div>
            <button type="button" id="addAddress">Add Another Address</button>
            
            <button type="submit">Submit</button>
            <button type="button" id="closeModal">Close</button>
        </form>
    </div>
</div>

<script src ="script/customerlist.js" defer></script>
<?php include "layout/footer.php"; ?>
