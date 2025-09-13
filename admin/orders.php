<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['user_id'];
$admin_type = $_SESSION['type'];

if (!isset($admin_id) || !isset($admin_type)) {
    header('location: ../login.php');
    exit;
}

// Fetch all orders
$select_orders = $conn->prepare("SELECT * FROM `orders` ORDER BY placed_on DESC");
$select_orders->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders</title>
    <link rel="stylesheet" href="../css/dashboard_style.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <section class="orders-section">
        <h1 class="heading">All Orders</h1>

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Email</th>
                        <th>Payment Method</th>
                        <th>Address</th>
                        <th>Products</th>
                        <th>Total Price</th>
                        <th>Placed On</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                if ($select_orders->rowCount() > 0) {
                    while ($order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>
                            <td>' . htmlspecialchars($order['id']) . '</td>
                            <td>' . htmlspecialchars($order['user_id']) . '</td>
                            <td>' . htmlspecialchars($order['name']) . '</td>
                            <td>' . htmlspecialchars($order['number']) . '</td>
                            <td>' . htmlspecialchars($order['email']) . '</td>
                            <td>' . htmlspecialchars($order['method']) . '</td>
                            <td>' . htmlspecialchars($order['address']) . '</td>
                            <td>' . htmlspecialchars($order['total_products']) . '</td>
                            <td>$' . htmlspecialchars($order['total_price']) . '</td>
                            <td>' . htmlspecialchars($order['placed_on']) . '</td>
                            <td>' . htmlspecialchars($order['payment_status']) . '</td>
                        </tr>';
                    }
                } else {
                    echo '<tr><td colspan="11" style="text-align:center;">No orders found</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </section>

    <script src="../js/admin_script.js"></script>
</body>

</html>