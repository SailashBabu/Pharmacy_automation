<?php
session_start();
include('connect.php');

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: billing.php");
    exit;
}

// Fetch cart items from session
$cart_items = $_SESSION['cart'];

// Process the transaction when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create a BulkWrite object for updating inventory
    $bulk = new MongoDB\Driver\BulkWrite;

    // Prepare transaction details
    $transaction_details = [
        'items' => [],
        'total_amount' => 0,
        'timestamp' => new MongoDB\BSON\UTCDateTime()
    ];

    foreach ($cart_items as $item) {
        // Calculate total amount for this item
        $total_price = $item['quantity'] * $item['price'];
        $transaction_details['total_amount'] += $total_price;

        // Prepare update for inventory
        $filter = ['Name' => $item['name']];
        $update = ['$inc' => ['Quantity' => -$item['quantity']]]; // Decrease quantity

        // Add item to transaction details
        $transaction_details['items'][] = [
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'total_price' => $total_price
        ];

        // Add update operation to bulk write
        $bulk->update($filter, $update);
    }

    // Execute bulk write to update inventory
    executeBulkWrite('medicines', $bulk);

    // Check if any medicine's quantity is now zero and delete it
    foreach ($cart_items as $item) {
        // Check if quantity is now zero after decrementing
        $medicine_filter = ['Name' => $item['name']];
        $updated_medicine = executeQuery('medicines', $medicine_filter);
        
        if (!empty($updated_medicine) && $updated_medicine[0]->Quantity <= 0) {
            // Create a BulkWrite object for deleting medicine
            $delete_bulk = new MongoDB\Driver\BulkWrite;
            $delete_bulk->delete($medicine_filter);
            executeBulkWrite('medicines', $delete_bulk);
        }
    }

    // Insert transaction details into transactions collection
    $bulk_transaction = new MongoDB\Driver\BulkWrite;
    $bulk_transaction->insert($transaction_details);
    executeBulkWrite('transactions', $bulk_transaction);

    // Clear the cart after processing the transaction
    $_SESSION['cart'] = [];

    // Redirect to index page after successful transaction
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Bill</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Bill</h1>

        <!-- Cart Summary Table -->
        <table border='1'>
            <thead>
                <tr style='background-color: #4CAF50; color: white;'>
                    <th>Name</th><th>Quantity Selected</th><th>MRP</th><th>Total Price</th></tr></thead><tbody><?php 
                    foreach ($cart_items as $item): 
                        ?>
                        <?php 
                        $total_price = $item['quantity'] * $item['price'];
                        echo "<tr><td>" . htmlspecialchars($item['name']) . "</td><td>" . htmlspecialchars($item['quantity']) . "</td><td>" . htmlspecialchars($item['price']) . "</td><td>" . htmlspecialchars($total_price) . "</td></tr>"; 
                    endforeach; 
                    ?>
                </tbody></table>

                <!-- Total Price Calculation -->
                <?php 
                $grandTotal = 0;
                foreach ($cart_items as $item) {
                    $grandTotal += $item['quantity'] * $item['price'];
                }
                ?>
                
                Total Amount: <?= htmlspecialchars($grandTotal); ?><br/><br/>

                <!-- Form to Finalize Bill -->
                <form method="post">
                    <button type="submit" class="button">Finalize Bill and Print</button>
                </form>

                <!-- Back to Billing Button -->
                <a href="billing.php" class="button">Back to Billing</a>

            </div>
        </body>
        </html>
