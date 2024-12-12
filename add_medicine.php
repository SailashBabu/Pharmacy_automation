<?php
include('connect.php');

// Handle adding new medicines
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $bulk = new MongoDB\Driver\BulkWrite;
    
//     // Validate input before inserting
//     if (!empty($_POST['medicine_name']) && !empty($_POST['quantity']) && !empty($_POST['mrp'])) {
//         $medicine = [
//             'Name' => $_POST['medicine_name'],
//             'Type' => $_POST['type'],
//             'Manufacturer' => $_POST['manufacturer'],
//             'Batch_No' => $_POST['batch_no'],
//             'Expiry_Date' => $_POST['expiry_date'],
//             'Quantity' => (int)$_POST['quantity'],
//             'MRP' => (float)$_POST['mrp']
//         ];

//         // Insert medicine into the database
//         $bulk->insert($medicine);
//         executeBulkWrite('medicines', $bulk);
//         echo "<script>alert('Medicine Added Successfully');</script>";
//         header("Location: inventory.php");
//         exit;
//     } else {
//         echo "<script>alert('Please fill all required fields.');</script>";
//     }
// }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adjust your MongoDB connection details
    
    // Validate input before processing
    if (!empty($_POST['medicine_name']) && !empty($_POST['quantity']) && !empty($_POST['mrp'])) {
        $medicine_name = $_POST['medicine_name'];
        $batch_no = $_POST['batch_no'];
        $quantity = (int)$_POST['quantity'];
        
        // Query to check if the medicine already exists
        $filter = [
            'Name' => $medicine_name,
            'Batch_No' => $batch_no
        ];

        $query = new MongoDB\Driver\Query($filter);
        $rows = $manager->executeQuery('pharmacy.medicines', $query); // Replace 'your_database' with your database name
        $existingMedicine = current($rows->toArray());

        $bulk = new MongoDB\Driver\BulkWrite;

        if ($existingMedicine) {
            // Update the existing document
            $bulk->update(
                ['_id' => $existingMedicine->_id], // Match document by its unique _id
                ['$inc' => ['Quantity' => $quantity]], // Increment quantity
                ['multi' => false, 'upsert' => false]
            );
            $message = 'Medicine quantity updated successfully.';
        } else {
            // Insert new document
            $medicine = [
                'Name' => $medicine_name,
                'Type' => $_POST['type'],
                'Manufacturer' => $_POST['manufacturer'],
                'Batch_No' => $batch_no,
                'Expiry_Date' => $_POST['expiry_date'],
                'Quantity' => $quantity,
                'MRP' => (float)$_POST['mrp']
            ];
            $bulk->insert($medicine);
            $message = 'Medicine added successfully.';
        }

        // Execute the bulk write operation
        $manager->executeBulkWrite('pharmacy.medicines', $bulk); // Replace 'your_database' with your database name
        echo "<script>alert('$message');</script>";
        header("Location: inventory.php");
        exit;
    } else {
        echo "<script>alert('Please fill all required fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medicine</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
    require "navbar.php";
    ?>
    <div class="form-container" style="max-height: 80vh; padding: 30px;">
        <h2 class="form-heading">Add New Medicine</h2>
        <form action="add_medicine.php" method="post">
            <div class="input-row">
                <label for="medicine_name">Medicine Name:</label>
                <input type="text" id="medicine_name" name="medicine_name" required>

                <label for="type">Type:</label>
                <select id="type" name="type">
                    <option value="Tablet">Tablet</option>
                    <option value="Syrup">Syrup</option>
                    <option value="Capsule">Capsule</option>
                    <option value="Injection">Injection</option>
                </select>
            </div>

            <div class="input-row">
                <label for="manufacturer">Manufacturer:</label>
                <input type="text" id="manufacturer" name="manufacturer" required>

                <label for="batch_no">Batch No:</label>
                <input type="text" id="batch_no" name="batch_no">
            </div>

            <div class="input-row">
                <label for="expiry_date">Expiry Date:</label>
                <input type="date" id="expiry_date" name="expiry_date">

                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>

            <div class="input-row">
                <label for="mrp">MRP:</label>
                <input type="number" id="mrp" name="mrp" step="0.01" required>
            </div>

            <div class="button-group" style="display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
                <button type="submit" class="button">Add Medicine</button>
                <a href="inventory.php" class="button">Back to Inventory</a>
            </div>
        </form>
    </div>
</body>
</html>
