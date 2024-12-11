<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <style>
       /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body Styling */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f9;
    color: #333;
    line-height: 1.6;
}

/* Header Styling */
header {
    background-color: #4CAF50;
    color: white;
    padding: 20px;
    text-align: center;
    border-radius: 8px;
    margin-bottom: 20px;
}

header h1 {
    font-size: 2rem;
}

/* Main Container */
main {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Form Styling */
form {
    display: flex;
    flex-direction: column;
}

label {
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 8px;
}

select, input, textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}

input[type="number"] {
    width: auto;
}

/* Button Styling */
button {
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    font-size: 1rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #45a049;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

thead {
    background-color: #f2f2f2;
}

th, td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    font-weight: bold;
}

td input {
    width: 70px;
    text-align: center;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

td .total-amount {
    font-weight: bold;
}

/* Focus and Active States */
select:focus, input:focus, textarea:focus {
    border-color: #4CAF50;
    outline: none;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
}

/* Responsive Design */
@media (max-width: 600px) {
    body {
        font-size: 14px;
    }

    header h1 {
        font-size: 1.5rem;
    }

    main {
        margin: 10px;
        padding: 15px;
    }

    table th, table td {
        font-size: 0.9rem;
    }

    button {
        padding: 10px;
        font-size: 0.9rem;
    }
}

    </style>
</head>
<body>
    <header>
        <h1>Create Order</h1>
    </header>
    <main>
        <?php
        require_once "conn.php";

        // Fetch stock items
        $sql = "SELECT * FROM stocks";
        $result = $conn->query($sql);

        // Initialize stock items array
        $stockItems = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stockItems[] = $row;
            }
        }
        $conn->close();
        ?>
        <form id="orderForm" action="process_order.php" method="POST">
            <!-- Customer Selection -->
            <label for="customer">Customer:</label>
            <select id="customer" name="customer_id" required>
                <!-- Populate dynamically using PHP -->
            </select>

            <!-- Item Selection -->
            <label for="items">Select Items:</label>
            <table id="itemsTable">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>GST Rate</th>
                        <th>Available Quantity</th>
                        <th>Order Quantity</th>
                        <th>Total Amount</th> <!-- Add this column -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stockItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['particulars']); ?></td>
                        <td><?php echo number_format($item['selling_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['gst_rate']); ?>%</td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>
                            <input type="number" name="items[<?php echo $item['id']; ?>][quantity]" 
                                   min="0" max="<?php echo $item['quantity']; ?>" value="0" class="quantity" data-price="<?php echo $item['selling_price']; ?>" data-gst="<?php echo $item['gst_rate']; ?>" data-available="<?php echo $item['quantity']; ?>">
                        </td>
                        <td><span class="total-amount">₹0.00</span></td> <!-- Total amount column -->
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit">Generate Bill</button>
        </form>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const quantityInputs = document.querySelectorAll('.quantity');
            quantityInputs.forEach(input => {
                input.addEventListener('input', function () {
                    const row = input.closest('tr');
                    const price = parseFloat(input.getAttribute('data-price'));
                    const gstRate = parseFloat(input.getAttribute('data-gst'));
                    const quantity = parseInt(input.value);
                    const totalAmountElement = row.querySelector('.total-amount');

                    if (quantity > 0) {
                        const itemTotal = price * quantity;
                        const gstAmount = (gstRate / 100) * itemTotal;
                        const totalAmount = itemTotal + gstAmount;

                        totalAmountElement.textContent = `₹${totalAmount.toFixed(2)}`;
                    } else {
                        totalAmountElement.textContent = '₹0.00';
                    }
                });
            });
        });
    </script>
</body>
</html>
