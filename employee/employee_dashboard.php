<?php
session_start();
include '../components/connect.php';

$employee_id = $_SESSION['user_id'] ?? null;
$type = $_SESSION['type'] ?? null;

if (!$employee_id || $type !== 'employee') {
    header('location: ../login.php');
    exit();
}

// Handle update of order details (except total_price)
if (isset($_POST['update_order']) && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $name = $_POST['name'];
    $number = $_POST['number'];
    $email = $_POST['email'];
    $method = $_POST['method'];
    $address = $_POST['address'];
    $total_products = $_POST['total_products'];
    $payment_status = $_POST['payment_status'];

    $update_stmt = $conn->prepare("UPDATE orders SET name=?, number=?, email=?, method=?, address=?, total_products=?, payment_status=? WHERE id=?");
    $update_stmt->execute([$name, $number, $email, $method, $address, $total_products, $payment_status, $order_id]);

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fetch orders for dashboard
$orders_query = $conn->prepare("SELECT * FROM orders ORDER BY placed_on DESC");
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
    <link rel="stylesheet" href="employee_dashboard.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    .container {
        max-width: 1400px;
        margin: auto;
    }

    .summary-cards {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .summary-cards div {
        padding: 10px 20px;
        background: #eee;
        border-radius: 5px;
        font-weight: bold;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
        vertical-align: top;
    }

    th {
        background-color: #f4f4f4;
        color: black;
    }

    input,
    select {
        width: 100%;
        padding: 5px;
        margin: 2px 0;
        box-sizing: border-box;
    }

    .update-btn {
        padding: 5px 10px;
        margin-top: 5px;
    }

    textarea {
        width: 100%;
        padding: 5px;
        box-sizing: border-box;
    }

    .readonly {
        background-color: #f9f9f9;
    }
    </style>
</head>

<body>

    <div class="container">
        <h1>Employee Dashboard</h1>

        <div class="summary-cards">
            <div>Total Orders: <?= $total_orders ?></div>
            <div>Pending Orders: <?= $pending_orders ?></div>
        </div>

        <h2>All Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Email</th>
                    <th>Method</th>
                    <th>Address</th>
                    <th>Products</th>
                    <th>Total Price</th>
                    <th>Payment Status</th>
                    <th>Placed On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <form method="POST" action="">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <td><?= $order['id'] ?></td>
                        <td><input type="text" name="name" value="<?= htmlspecialchars($order['name']) ?>"></td>
                        <td><input type="text" name="number" value="<?= htmlspecialchars($order['number']) ?>"></td>
                        <td><input type="email" name="email" value="<?= htmlspecialchars($order['email']) ?>"></td>
                        <td><input type="text" name="method" value="<?= htmlspecialchars($order['method']) ?>"></td>
                        <td><textarea name="address"><?= htmlspecialchars($order['address']) ?></textarea></td>
                        <td><textarea name="total_products"><?= htmlspecialchars($order['total_products']) ?></textarea>
                        </td>
                        <td><input type="number" name="total_price" value="<?= $order['total_price'] ?>"
                                class="readonly" readonly></td>
                        <td>
                            <select name="payment_status">
                                <option value="pending" <?= $order['payment_status']=='pending' ? 'selected' : '' ?>>
                                    Pending</option>
                                <option value="completed"
                                    <?= $order['payment_status']=='completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled"
                                    <?= $order['payment_status']=='cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </td>
                        <td><?= $order['placed_on'] ?></td>
                        <td>
                            <button type="submit" name="update_order" class="update-btn">Update</button>
                            <a href="receipt.php?order_id=<?= $order['id'] ?>" target="_blank">
                                <button type="button">Print</button>
                            </a>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="11" style="text-align:center;">No orders found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>