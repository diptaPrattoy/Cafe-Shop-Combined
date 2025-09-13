<?php
session_start();
include '../components/connect.php';

$employee_id = $_SESSION['user_id'];
$type = $_SESSION['type'];

// if (!isset($employee_id) || $type !== 'employee') {
//     header('location: ../login.php');
//     exit();
// }

// Filter options
$filter = $_GET['filter'] ?? 'all';
$whereClause = "";

switch($filter) {
    case 'daily':
        $whereClause = "WHERE placed_on = CURDATE()";
        break;
    case 'weekly':
        $whereClause = "WHERE placed_on >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'pending':
        $whereClause = "WHERE payment_status = 'pending'";
        break;
    default:
        $whereClause = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Transactions</title>
<style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
    h1 { color: #007BFF; text-align: center; margin-bottom: 20px; }
    .filter { margin-bottom: 20px; text-align: center; }
    .filter a { margin: 0 10px; text-decoration: none; padding: 8px 16px; background: #007BFF; color: #fff; border-radius: 5px; }
    .filter a.active { background: #0056b3; }
    table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background-color: #007BFF; color: #fff; }
    tr:hover { background: #f1f1f1; }
    .action a { text-decoration: none; color: #fff; background: #28a745; padding: 5px 10px; border-radius: 4px; }
    .action a:hover { background: #218838; }
</style>
</head>
<body>

<h1>Employee Transactions</h1>

<div class="filter">
    <a href="?filter=all" class="<?php if($filter=='all') echo 'active'; ?>">All</a>
    <a href="?filter=daily" class="<?php if($filter=='daily') echo 'active'; ?>">Today</a>
    <a href="?filter=weekly" class="<?php if($filter=='weekly') echo 'active'; ?>">Last 7 Days</a>
    <a href="?filter=pending" class="<?php if($filter=='pending') echo 'active'; ?>">Pending</a>
</div>

<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Phone</th>
            <th>Total Price</th>
            <th>Placed On</th>
            <th>Payment Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $conn->prepare("SELECT orders.*, all_users.username, all_users.number 
                                FROM orders 
                                JOIN all_users ON orders.user_id = all_users.id
                                $whereClause
                                ORDER BY placed_on DESC");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($orders) {
            foreach($orders as $order) {
                echo "<tr>";
                echo "<td>{$order['id']}</td>";
                echo "<td>{$order['username']}</td>";
                echo "<td>{$order['number']}</td>";
                echo "<td>TK {$order['total_price']}</td>";
                echo "<td>{$order['placed_on']}</td>";
                echo "<td>{$order['payment_status']}</td>";
                echo "<td class='action'><a href='receipt.php?order_id={$order['id']}' target='_blank'>Receipt</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>No orders found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
