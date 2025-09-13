<?php
session_start();
include '../components/connect.php';

$employee_id = $_SESSION['user_id'];
$type = $_SESSION['type'];

// if (!isset($employee_id) || $type !== 'employee') {
//     header('location: ../login.php');
//     exit();
// }


if (!isset($_GET['order_id'])) {
    echo "No order selected.";
    exit();
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$stmt = $conn->prepare("SELECT orders.*, all_users.username, all_users.email, all_users.number 
                        FROM orders 
                        JOIN all_users ON orders.user_id = all_users.id 
                        WHERE orders.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit();
}

// Parse products string into array
$products = array_filter(array_map('trim', explode('-', $order['total_products'])));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt - Order #<?php echo $order['id']; ?></title>
<style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; }
    .receipt-container { width: 600px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 8px; }
    h2 { text-align: center; color: #007BFF; }
    .order-info, .customer-info { margin-bottom: 20px; }
    .order-info div, .customer-info div { margin-bottom: 5px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background-color: #007BFF; color: #fff; }
    .total { text-align: right; font-weight: bold; font-size: 18px; }
    .print-btn { display: block; width: 100%; padding: 10px; background: #007BFF; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
    .print-btn:hover { background: #0056b3; }
</style>
</head>
<body>

<div class="receipt-container">
    <h2>Cozy Cafe - Receipt</h2>

    <div class="customer-info">
        <h3>Customer Info:</h3>
        <div><strong>Name:</strong> <?php echo htmlspecialchars($order['username']); ?></div>
        <div><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></div>
        <div><strong>Phone:</strong> <?php echo htmlspecialchars($order['number']); ?></div>
        <div><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></div>
    </div>

    <div class="order-info">
        <h3>Order Info:</h3>
        <div><strong>Order ID:</strong> <?php echo $order['id']; ?></div>
        <div><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['method']); ?></div>
        <div><strong>Payment Status:</strong> <?php echo $order['payment_status']; ?></div>
        <div><strong>Placed On:</strong> <?php echo $order['placed_on']; ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $product): 
                if(empty($product)) continue;
                // Example: "Cortado (20 x 1)"
                preg_match('/(.+)\((\d+) x (\d+)\)/', $product, $matches);
                $name = $matches[1] ?? $product;
                $price = $matches[2] ?? '-';
                $qty = $matches[3] ?? '-';
            ?>
            <tr>
                <td><?php echo htmlspecialchars($name); ?></td>
                <td><?php echo htmlspecialchars($qty); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total">Total Price: TK <?php echo $order['total_price']; ?></div>

    <button class="print-btn" onclick="window.print();">Print Receipt</button>
</div>

</body>
</html>
