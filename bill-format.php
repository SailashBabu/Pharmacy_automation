<?php
session_start();
include('connect.php');

// Check if there are transaction details in session
if (!isset($_SESSION['transaction_details'])) {
    header("Location: index.php"); // Redirect if no transaction details are available
    exit;
}

// Fetch transaction details from session
$transaction_details = $_SESSION['transaction_details'];
$cart_items = $transaction_details['items'];
$customer_name = isset($transaction_details['customer_name']) ? htmlspecialchars($transaction_details['customer_name']) : "Unknown";
$customer_mobile = isset($transaction_details['customer_mobile']) ? htmlspecialchars($transaction_details['customer_mobile']) : "Unknown";
$doctor_name = isset($transaction_details['doctor_name']) ? htmlspecialchars($transaction_details['doctor_name']) : "Unknown";
$age = isset($transaction_details['age']) ? htmlspecialchars($transaction_details['age']) : "Unknown";
$gender = isset($transaction_details['gender']) ? htmlspecialchars($transaction_details['gender']) : "Unknown";
$time = isset($transaction_details['timestamp']) ? htmlspecialchars($transaction_details['timestamp']) : "Unknown";

// Calculate total amount for display (if needed)
$total_amount = $transaction_details['total_amount'];

// Clear transaction details from session after fetching them (optional)
unset($_SESSION['transaction_details']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill</title>
</head>
<script>
// Automatically trigger print dialog on load.
window.onload = function() {
   window.print();
};
</script>
<body>
    <div class="bill">
        <div class="bill-top">
            <div class="shop-details">
                <p class="licence">DL CBE 1271/21</p>
                <h2 class="shop-name">Sri Sai Suriya Medicals</h2>
                <p class="address"><strong>1/233-1,Rangammal Colony,N.G.G.O Colony,CBE-22</strong></p><br>
                <p class="gst">GSTIN: 33CCJPS5050D1ZR &nbsp;<strong>Phn :</strong> 9894180852</p>
                
            </div>
            
            <div class="bill-top-right">
                <p class="bill-no"><strong> Bill No:</strong><span></span></p>
                <p class="doc-name"><strong>Doctor Name:</strong> <?= $doctor_name ?><span></span></p>
                <p class="doc-name"><strong>Time:</strong> <?= $time ?><span></span></p>
            </div>
        </div>
        <div class="bill-bottom">
            <div class="customer-details"> <p class="customer-name">Name:<span><?= $customer_name ?></span></p>
                <p class="age">Age:<span><?= $age ?></span></p>
                <p class="gender">Sex:<span><?= $gender ?></span></p>
                <p class="customer-number">Mobile No:<span><?= $customer_mobile ?></span></p>
            </div>
        </div>
        <p style="text-align:center;">Bill Summary</p>
        <div class="bill-summary">
        <table border='1'>
            <thead>
            <tr style='background-color: #4CAF50; color: white;'>
            <th>Name</th><th>Batch No</th><th>Expiry Date</th><th>Quantity</th><th>MRP</th><th>Total Price</th>
            </tr>
        </thead>
        <tbody><?php 
            foreach ($cart_items as $item): 
            ?>
            <?php 
            echo "<tr><td>" . htmlspecialchars($item['name']) . "</td><td>" . htmlspecialchars($item['batch_no']) . "</td><td>" . htmlspecialchars($item['expiry_date']) . "</td><td>" . htmlspecialchars($item['quantity']) . "</td><td>" . htmlspecialchars($item['price']) . "</td><td>" . htmlspecialchars($item['quantity'] * $item['price']) . "</td></tr>"; 
            endforeach; 
            ?>
             </tbody>
             <tr>
                <td style="text-align: right;"colspan="6">
                <!-- <div class="total-amt"> -->
                <!-- <ul style="list-style-type: none;margin: 0%; padding: 0%;"> -->
                    <strong>Grand Total : </strong><?= htmlspecialchars($total_amount); ?><br>
                    <strong>Discount :</strong> 8% <br>
                    <strong>Amt To Be Paid :</strong><?= round(htmlspecialchars($total_amount) - ((8/100)*htmlspecialchars($total_amount))); ?>
                </td>
            </tr>
        </table>
        </div>
            <!-- Total Price Calculation -->
            
            
            <!-- Back Button -->
    </div>
    <br>
    <a href='index.php' class='button'>Back to Homepage</a>

</body>
<style>

    .total-amt{
        margin: 0%;
        float: right;
        width: 300px;
    }
    table {
    border-collapse: collapse;
    margin: 20px 0;
    text-align: center;
    padding:10px;
    width:1000px;
}
th{
    border: 1px solid #4CAF50;
    font-size: small;
    height:30px;
    width:150px;
}
td {
    border: 1px solid #4CAF50;
    padding: 10px;
    height: 30px;
    width:150px;
    font-size: small;
}

thead th {
    background-color: #4CAF50;
    color: white;
}

    .bill-summary{
        margin-top: 20px;
        width: 1000px;
    }

   button, .button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    cursor: pointer;
    margin: 5px 0;
    text-align: center;
}

    
    .bill-top{
        height: 140px;
        width:1000px;
        display: flex;
    }
    
    .bill-no{
        font-size: large;
        height: 30px;
        width:300px;
        
    }
    .doc-name{
        width:300px;
    }
    .bill{
        width:1000px;
        border: 1px solid #4CAF50;
    }
    .shop-details{
        width: 700px; 
        height: 140px;
        border: 1px solid #4CAF50;
    }
    .licence{
        font-size: small;
        margin: 0%;
        padding: 5px 0px 0px 5px;
        width: 100px;
    }
    .shop-name{
        text-align: center;
        font-size: 50px;
        padding-bottom: 0px;
        margin: 0%;
        display: flex;
        justify-content: center;
    }
    .address{
        display: flex;
        font-size: 20px;
        justify-content: center;
        margin: 0%;
        text-align: center;
    }
    .gst{
        display: flex;
        justify-content: center;
        margin: 0%;
        align-items: center;
        height: 20px;
        font-size: 1.2rem;
    }
    .shop-number{
        text-align:right;

    }
    .customer-details{
        border: 1px solid #4CAF50;
        width: 1000px;
        height: 60px;
        display: flex;
        justify-content: center;
       
    }
    .customer-name{
        display: flex;
        font-size: larger;
        width: 340px;
        margin-left: 0%;
    }
    .age{
        width:100px;
        display:flex;
        font-size: larger;
    }
    .gender{
        width:100px;
        font-size: larger;
        display:flex;

    }
    .customer-number{
        width: 300px;
        height: 20px;
        font-size: larger;
        
    }
    .bill-top-right{
        border: 1px solid #4CAF50;
        width: 300px;
        height: 140px;
    }



</style>
</html>