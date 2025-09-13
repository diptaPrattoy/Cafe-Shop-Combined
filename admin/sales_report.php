<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['user_id'];
$admin_type = $_SESSION['type'];
if (!isset($admin_id) || !isset($admin_type)) {
    header('location: ../login.php');
}

// Summary Calculations
$total_sales = $conn->query("SELECT SUM(total_price) as total_sales FROM `orders`")->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;
$total_orders = $conn->query("SELECT COUNT(*) as total_orders FROM `orders`")->fetch(PDO::FETCH_ASSOC)['total_orders'] ?? 0;
$completed_orders = $conn->query("SELECT COUNT(*) as completed_orders FROM `orders` WHERE payment_status='completed'")->fetch(PDO::FETCH_ASSOC)['completed_orders'] ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) as pending_orders FROM `orders` WHERE payment_status='pending'")->fetch(PDO::FETCH_ASSOC)['pending_orders'] ?? 0;
$total_users_orders = $conn->query("SELECT COUNT(DISTINCT user_id) as total_users FROM `orders`")->fetch(PDO::FETCH_ASSOC)['total_users'] ?? 0;
$total_products_sold = $conn->query("SELECT SUM(SUBSTRING_INDEX(SUBSTRING_INDEX(total_products,'(',-1),')',1)) as total_qty
                                     FROM `orders`")->fetch(PDO::FETCH_ASSOC)['total_qty'] ?? 0;

// Daily Sales - Last 30 Days
$daily_sales_stmt = $conn->query("SELECT DATE(placed_on) as day, SUM(total_price) as daily_total
                                  FROM orders
                                  WHERE placed_on >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                                  GROUP BY DATE(placed_on)
                                  ORDER BY day ASC");
$daily_sales_data = $daily_sales_stmt->fetchAll(PDO::FETCH_ASSOC);

// Monthly Sales - Last 12 Months
$monthly_sales_stmt = $conn->query("SELECT DATE_FORMAT(placed_on,'%Y-%m') as month, SUM(total_price) as monthly_total
                                    FROM orders
                                    WHERE placed_on >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                                    GROUP BY DATE_FORMAT(placed_on,'%Y-%m')
                                    ORDER BY month ASC");
$monthly_sales_data = $monthly_sales_stmt->fetchAll(PDO::FETCH_ASSOC);

// Top Selling Products
$products_stmt = $conn->query("SELECT p.name, SUM(SUBSTRING_INDEX(SUBSTRING_INDEX(o.total_products, p.name, -1), '-', 1) + 0) as sold_qty
                               FROM products p
                               JOIN orders o
                               ON o.total_products LIKE CONCAT('%',p.name,'%')
                               GROUP BY p.name
                               ORDER BY sold_qty DESC LIMIT 10");
$top_products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

// Orders Table
$orders_stmt = $conn->query("SELECT * FROM orders ORDER BY placed_on DESC");
$orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="../css/dashboard_style.css">
    <link rel="stylesheet" href="../css/nav.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <section class="accounts">
        <h1 class="heading">Sales Report</h1>

        <!-- Summary Cards -->
        <div class="cardBox" style="margin-bottom:2rem;">
            <div class="card">
                <div>
                    <div class="numbers">$<?= $total_sales ?></div>
                    <div class="cardName">Total Sales</div>
                </div>
                <div class="iconBx">
                    <ion-icon name="cash-outline"></ion-icon>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"><?= $total_orders ?></div>
                    <div class="cardName">Total Orders</div>
                </div>
                <div class="iconBx">
                    <ion-icon name="receipt-outline"></ion-icon>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"><?= $completed_orders ?></div>
                    <div class="cardName">Completed Orders</div>
                </div>
                <div class="iconBx">
                    <ion-icon name="checkmark-done-outline"></ion-icon>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"><?= $pending_orders ?></div>
                    <div class="cardName">Pending Orders</div>
                </div>
                <div class="iconBx">
                    <ion-icon name="time-outline"></ion-icon>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"><?= $total_users_orders ?></div>
                    <div class="cardName">Users Made Orders</div>
                </div>
                <div class="iconBx">
                    <ion-icon name="people-outline"></ion-icon>
                </div>
            </div>
            <div class="card">
                <div>
                    <div class="numbers"><?= $total_products_sold ?></div>
                    <div class="cardName">Total Products Sold</div>
                </div>
                <div class="iconBx">
                    <ion-icon name="cube-outline"></ion-icon>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div style="max-width: 800px; margin:auto; margin-bottom:3rem;">
            <canvas id="dailySalesChart"></canvas>
        </div>
        <div style="max-width: 800px; margin:auto; margin-bottom:3rem;">
            <canvas id="monthlySalesChart"></canvas>
        </div>

        <!-- Top Products -->
        <h3>Top Selling Products</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($top_products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= $product['sold_qty'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Orders Table -->
        <h3>Recent Orders</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Placed On</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= $order['user_id'] ?></td>
                    <td><?= htmlspecialchars($order['name']) ?></td>
                    <td>$<?= $order['total_price'] ?></td>
                    <td><?= htmlspecialchars($order['payment_status']) ?></td>
                    <td><?= $order['placed_on'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>




    </section>

    <script>
    // Daily Sales Chart
    // const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
    // new Chart(dailyCtx, {
    //     type: 'line',
    //     data: {
    //         labels: <?= json_encode(array_column($daily_sales_data, 'day')) ?>,
    //         datasets: [{
    //             label: 'Daily Sales ($)',
    //             data: <?= json_encode(array_column($daily_sales_data, 'daily_total')) ?>,
    //             fill: true,
    //             backgroundColor: 'rgba(75, 192, 192, 0.2)',
    //             borderColor: 'rgba(75, 192, 192, 1)',
    //             tension: 0.3
    //         }]
    //     }
    // });

    // Monthly Sales Chart
    const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($monthly_sales_data, 'month')) ?>,
            datasets: [{
                label: 'Monthly Sales ($)',
                data: <?= json_encode(array_column($monthly_sales_data, 'monthly_total')) ?>,
                backgroundColor: 'rgba(255, 159, 64, 0.6)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>

</body>

</html>