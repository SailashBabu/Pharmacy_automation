<?php
include('connect.php');

// Start session
session_start();

// Initialize an empty cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Retrieve medicines from the database
$filter = [];
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $filter = ['Name' => new MongoDB\BSON\Regex($search_term, 'i')];
}
$medicines = executeQuery('medicines', $filter); // Fetch all medicines

// Handle form submission for going to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['go_to_cart'])) {
    // Prepare cart items from posted data
   
    
        // Prepare cart items from posted data
        foreach ($_POST['medicine_names'] as $index => $medicine_name) {
            $quantity = $_POST['quantities'][$index] ?? 0; // Use null coalescing operator
            $batch_no = $_POST['batch_nos'][$index] ?? ''; // Batch number
            $expiry_date = $_POST['expiry_dates'][$index] ?? ''; // Expiry date
            if (!empty($quantity) && $quantity > 0) {
                $price = $_POST['prices'][$index] ?? 0; // Use null coalescing operator
    
                // Check if the medicine is already in the cart
                $found = false;
                foreach ($_SESSION['cart'] as &$cart_item) {
                    if ($cart_item['name'] === $medicine_name) {
                        // Update the quantity and mark as found
                        $cart_item['quantity'] = $cart_item['quantity'] + $quantity;
                        $found = true;
                        break;
                    }
                }
                // If not found, add as a new entry
                if (!$found) {
                    $_SESSION['cart'][] = [
                        'name' => $medicine_name,
                        'batch_no' => $batch_no,
                        'expiry_date' => $expiry_date,
                        'quantity' => $quantity,
                        'price' => $price
                    ];
                }
            }
        }
        
    // Check if there are any items in the cart before redirecting
    if (!empty($_SESSION['cart'])) {
        // Redirect to cart page
        header("Location: cart.php");
        exit;
    } else {
        echo "<script>alert('Please enter a valid quantity for at least one medicine.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Billing</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
    require "navbar.php";
    ?>
    <div class="container">
        <header style="display: flex; justify-content: space-between; align-items: center;margin-top:100px;">
            <h1 style="text-align: center; flex-grow: 1;">Customer Billing</h1>
            <a href="index.php" class="button back-home">Back to Homepage</a> <!-- Back to Homepage Button -->
        </header>
        
            <form method="get" class="search-form">
                <input type="text" name="search"  placeholder="Search Medicine" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="button">Search</button>
            </form>
        <!-- Medicines Table -->
        <h2 style="margin-top: 20px;">Available Medicines</h2>
        <form action="billing.php" method="post">
            <table border='1' style='width: 100%; border-collapse: collapse;'>
                <thead>
                    <tr style='background-color: #4CAF50; color: white;'>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Manufacturer</th>
                        <th>Batch No</th>
                        <th>Expiry Date</th>
                        <th>Quantity Available</th>
                        <th>MRP</th>
                        <th>Quantity to Add</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($medicines)): ?>
                        <tr><td colspan="8">No medicines found.</td></tr> <!-- Message when no medicines are found -->
                    <?php else: ?>
                        <?php foreach ($medicines as $medicine): ?>
                            <tr>
                                <td><?= htmlspecialchars($medicine->Name) ?></td>
                                <td><?= htmlspecialchars($medicine->Type) ?></td>
                                <td><?= htmlspecialchars($medicine->Manufacturer) ?></td>
                                <td><?= htmlspecialchars($medicine->Batch_No) ?></td>
                                <td><?= htmlspecialchars($medicine->Expiry_Date) ?></td>
                                <td><?= htmlspecialchars($medicine->Quantity) ?></td>
                                <td><?= htmlspecialchars($medicine->MRP) ?></td>

                                <!-- Hidden inputs for medicine details -->
                                <input type="hidden" name="medicine_names[]" value="<?= htmlspecialchars($medicine->Name) ?>">
                                <input type="hidden" name="prices[]" value="<?= htmlspecialchars($medicine->MRP) ?>">
                                <input type="hidden" name="batch_nos[]" value="<?= htmlspecialchars($medicine->Batch_No) ?>">
                                <input type="hidden" name="expiry_dates[]" value="<?= htmlspecialchars($medicine->Expiry_Date) ?>">


                                <!-- Input for quantity -->
                                <td><input type="number" name="quantities[]" min="0" max="<?= htmlspecialchars($medicine->Quantity) ?>" placeholder="Qty"></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Go to Cart Button -->
            <button type="submit" name="go_to_cart" class="button" style='margin-top: 20px;'>Go to Cart</button>
        </form>

    </div>

</body>
</html>
