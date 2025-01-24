<?php
include "layout/header.php";
include "layout/conn.php";
include "layout/nav.php";
?>


<div class="container" id="shop">
    <h2>List of Shops</h2>
    <button id="add_shop">Add Shop</button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Shop Name</th>
                <th>Shop Logo</th>
                <th>Latitude</th>
                <th>Longitude</th>
            </tr>
        </thead>
        <tbody id="shopTable">
            <?php
            $sql = "SELECT shop_id, shop_name, shop_logo, latitude, longitude FROM shop_details";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['id']) . "</td>
                            <td>" . htmlspecialchars($row['shop_name']) . "</td>
                            <td><img src='uploads/" . htmlspecialchars($row['shop_logo']) . "' width='50' height='50'></td>
                            <td>" . htmlspecialchars($row['latitude']) . "</td>
                            <td>" . htmlspecialchars($row['longitude']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No shops found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</div>

<div id="overlay"></div>

    <div id="modal" >
 <h1 id ="shop_head">Add Shop Details</h1>
    <form action="shop.php" method="POST" enctype="multipart/form-data" id = "shop_form">
        <label for="shop_name">Shop Name:</label><br>
        <input type="text" id="shop_name" name="shop_name" required><br><br>

        <label for="shop_logo">Shop Logo:</label><br>
        <input type="file" id="shop_logo" name="shop_logo" accept="image/*" required><br><br>

    <div id="map" style="width: 100%; height: 400px;"></div><br>
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        <!-- <button   type="submit">Save Shop</button> -->
        <button  id ="shopsave_btn" type="submit">Save Shop</button>
        <button class ="shopclose_btn"type="button" id="closeModal">Close</button>
    </form>
</div>
</div>
       
<script>
    // Modal and Overlay Elements
const addShopButton = document.getElementById('add_shop');
const modal = document.getElementById('modal');
const overlay = document.getElementById('overlay');
const closeModalButton = document.getElementById('closeModal');
const form = document.getElementById('shop_form');

// Open Modal
addShopButton.addEventListener('click', () => {
    modal.style.display = 'block';
    overlay.style.display = 'block';
});

// Close Modal
const closeModal = () => {
    modal.style.display = 'none';
    overlay.style.display = 'none';
};

closeModalButton.addEventListener('click', closeModal);
overlay.addEventListener('click', closeModal);

// Handle Form Submission
form.addEventListener('submit', (e) => {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(form);

    fetch('shop.php', {
        method: 'POST',
        body: formData,
    })
    .then((response) => response.text())
    .then((data) => {
        console.log(data);
        closeModal();

        // Reload the page or dynamically update the shop table
        location.reload();
    })
    .catch((error) => {
        console.error('Error:', error);
        alert('An error occurred while submitting the form.');
    });
});


  let map;
    let marker;

    function initMap() {
        const defaultLocation = { lat: -34.397, lng: 150.644 }; // Default location
        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 8,
        });

        // Add a marker on the map when clicked
        map.addListener("click", (event) => {
            const { lat, lng } = event.latLng.toJSON();

            // Set marker position
            if (marker) {
                marker.setPosition(event.latLng);
            } else {
                marker = new google.maps.Marker({
                    position: event.latLng,
                    map: map,
                });
            }

            // Update hidden inputs with the selected latitude and longitude
            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
        });
    }

    // Load Google Maps JavaScript API
    
        

</script>


<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"></script>


   <?php
include "layout/footer.php";
?>
