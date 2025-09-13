<?php
session_start();
include '../components/connect.php';

$employee_id = $_SESSION['user_id'];
$type = $_SESSION['type'];

// if (!isset($employee_id) || $type !== 'employee') {
//     header('location: ../login.php');
//     exit();
// }

// Fetch orders for dashboard
$orders_query = $conn->prepare("SELECT orders.*, all_users.username FROM orders 
                                JOIN all_users ON orders.user_id = all_users.id 
                                ORDER BY placed_on DESC");
$orders_query->execute();
$orders = $orders_query->fetchAll(PDO::FETCH_ASSOC);

// Summary counts
$total_orders = count($orders);
$pending_orders = count(array_filter($orders, fn($o) => $o['payment_status'] === 'pending'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="employeeCSS/employee_dashboard.css">
</head>
<body>
<div class="container">
    <h1>Employee Dashboard</h1>

    <div class="summary-cards">
        <div>Total Orders: <?php echo $total_orders; ?></div>
        <div>Pending Orders: <?php echo $pending_orders; ?></div>
    </div>

    <h2>All Orders</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Total Products</th>
                <th>Total Price</th>
                <th>Payment Status</th>
                <th>Placed On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $order): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['username']); ?></td>
                <td><?php echo htmlspecialchars($order['total_products']); ?></td>
                <td>TK <?php echo $order['total_price']; ?></td>
                <td><?php echo $order['payment_status']; ?></td>
                <td><?php echo $order['placed_on']; ?></td>
                <td>
                    <a href="receipt.php?order_id=<?php echo $order['id']; ?>" target="_blank">
                        <button class="print-btn">Print Receipt</button>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
