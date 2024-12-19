
   <?php
      include "layout/header.php";
      include "layout/nav.php";
   ?>

    <!-- Homepage Image Section -->
    
    <!-- Stock Items Section -->
    

    <!-- Customer Section -->
    

    <!-- order page -->

   <!-- <script>
    // Function to update total amount when the order quantity changes
    function updateTotal(input) {
        const price = parseFloat(input.dataset.price); // Get the price of the item
        const gstRate = parseFloat(input.dataset.gst); // Get the GST rate of the item
        const quantity = parseInt(input.value, 10) || 0; // Get the ordered quantity (defaults to 0 if invalid)

        // Calculate the total price and GST
        const gstAmount = (price * gstRate / 100) * quantity;
        const totalAmount = (price * quantity) + gstAmount;

        // Update the total amount for this row
        const totalAmountElement = input.closest('tr').querySelector('.total-amount');
        totalAmountElement.textContent = `₹${totalAmount.toFixed(2)}`;
    }
</script> -->
<?php
include "layout/footer.php";
?>
