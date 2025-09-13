<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['user_id'] ?? null;
$admin_type = $_SESSION['type'] ?? null;

if (!$admin_id || $admin_type !== 'admin') {
    header('location: ../login.php');
    exit;
}

// Handle order update
if (isset($_POST['update_order'])) {
    $order_id = intval($_POST['order_id']);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $products = filter_var($_POST['total_products'], FILTER_SANITIZE_STRING);
    $total_price = floatval($_POST['total_price']); // added total_price
    $payment_status = $_POST['payment_status'];

    $update_order = $conn->prepare(
        "UPDATE `orders`
         SET name = ?, number = ?, email = ?, method = ?, address = ?, total_products = ?, total_price = ?, payment_status = ?
         WHERE id = ?"
    );
    $update_order->execute([$name, $number, $email, $method, $address, $products, $total_price, $payment_status, $order_id]);

    $message[] = "Order #$order_id updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order</title>
    <link rel="stylesheet" href="../css/dashboard_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
    .update-form input,
    .update-form select,
    .update-form textarea {
        width: 100%;
        padding: 6px;
        margin: 4px 0;
        box-sizing: border-box;
    }

    .update-form button {
        padding: 8px 15px;
        margin-top: 10px;
    }

    .message {
        color: green;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="update-order">
        <h1 class="heading">Update Order</h1>

        <?php
    if (isset($_GET['order_id'])) {
        $order_id = intval($_GET['order_id']);
        $show_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
        $show_order->execute([$order_id]);

        if ($show_order->rowCount() > 0) {
            $order = $show_order->fetch(PDO::FETCH_ASSOC);
            if (!empty($message)) {
                foreach ($message as $msg) {
                    echo "<div class='message'>$msg</div>";
                }
            }
    ?>
        <form action="" method="POST" class="update-form">
            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">

            <span>User ID</span>
            <input type="number" value="<?= $order['user_id']; ?>" readonly>

            <span>Name</span>
            <input type="text" name="name" value="<?= htmlspecialchars($order['name']); ?>" required>

            <span>Number</span>
            <input type="text" name="number" value="<?= htmlspecialchars($order['number']); ?>" required>

            <span>Email</span>
            <input type="email" name="email" value="<?= htmlspecialchars($order['email']); ?>" required>

            <span>Payment Method</span>
            <select name="method" required>
                <option value="<?= htmlspecialchars($order['method']); ?>" selected>
                    <?= htmlspecialchars($order['method']); ?></option>
                <option value="cash">Cash</option>
                <option value="credit card">Credit Card</option>
                <option value="bkash">Bkash</option>
                <option value="paypal">Paypal</option>
            </select>

            <span>Address</span>
            <textarea name="address" rows="3" required><?= htmlspecialchars($order['address']); ?></textarea>

            <span>Products</span>
            <textarea name="total_products" rows="3"
                required><?= htmlspecialchars($order['total_products']); ?></textarea>

            <span>Total Price</span>
            <input type="number" name="total_price" min="0" value="<?= htmlspecialchars($order['total_price']); ?>"
                required>

            <span>Payment Status</span>
            <select name="payment_status" required>
                <option value="pending" <?= $order['payment_status']=='pending'?'selected':''; ?>>Pending</option>
                <option value="completed" <?= $order['payment_status']=='completed'?'selected':''; ?>>Completed</option>
                <option value="cancelled" <?= $order['payment_status']=='cancelled'?'selected':''; ?>>Cancelled</option>
            </select>

            <div class="flex-btn">
                <button type="submit" class="btn" name="update_order">Update Order</button>
                <a href="orders.php" class="option-btn">Go Back</a>
            </div>
        </form>
        <?php
        } else {
            echo '<p class="empty">Order not found!</p>';
        }
    } else {
        echo '<p class="empty">No order selected!</p>';
    }
    ?>
    </section>

    <script src="../js/admin_script.js"></script>
</body>

</html>