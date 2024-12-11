<?php
  require_once "conn.php"; 
  include "layout/header.php";
  include "layout/nav.php";
  ?>
   
  
 
 
 <div id="customer-section" class="container">
        <h1>List of Users</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Retrieve all users from the database
                $sql = "SELECT detail_id, fullname, user_address, phone FROM userdetail";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['detail_id']) . "</td>
                                <td>" . htmlspecialchars($row['fullname']) . "</td>
                                <td>" . htmlspecialchars($row['user_address']) . "</td>
                                <td>" . htmlspecialchars($row['phone']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <button id="customer">Add Customer</button>
    </div>

    <!--  -->

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

    <?php
    include "layout/footer.php";
    ?>