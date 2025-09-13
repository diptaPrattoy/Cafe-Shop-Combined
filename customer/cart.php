<?php
session_start();
include '../components/connect.php';

// Handle cart actions
if(!empty($_GET["action"])) {
    $user_id = $_SESSION['user_id'];
    
    switch($_GET["action"]) {
        case "remove":
            if(!empty($_GET["code"])) {
                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ? AND pid = ?");
                $delete_cart->execute([$user_id, $_GET["code"]]);
            }
            break;
        case "empty":
            $empty_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $empty_cart->execute([$user_id]);
            break;
        case "update":
            if(isset($_POST["quantity"])) {
                foreach($_POST["quantity"] as $pid => $qty) {
                    $qty = intval($qty);
                    if($qty > 0) {
                        $update_cart = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE user_id = ? AND pid = ?");
                        $update_cart->execute([$qty, $user_id, $pid]);
                    } else {
                        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ? AND pid = ?");
                        $delete_cart->execute([$user_id, $pid]);
                    }
                }
            }
            break;
    }
}

// Get cart data from database
$user_id = $_SESSION['user_id'];
$select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$select_cart->execute([$user_id]);
$cart_items = $select_cart->fetchAll(PDO::FETCH_ASSOC);

$cart_count = 0;
$cart_total = 0;
foreach($cart_items as $item) {
    $cart_count += $item["quantity"];
    $cart_total += ($item["price"] * $item["quantity"]);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Shopping Cart</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="customerCSS/cart.css">
<script>
function confirmEmpty() {
    return confirm('Are you sure you want to empty your cart?');
}

function validateQuantity(input) {
    if (input.value < 0) {
        input.value = 0;
    }
}
</script>
</head>
<body>
<div class="header">
    <h1>Shopping Cart</h1>
    <a href="customer_dashboard.php" class="products-link">Continue Shopping</a>
</div>

<div class="container">
    <?php if(!empty($cart_items)): ?>
    
    <div class="cart-header">
        <h2>Your Cart (<?php echo $cart_count; ?> items)</h2>
        <div class="cart-actions">
            <form method="post" action="cart.php?action=update" style="display: inline;">
                <?php foreach($cart_items as $item): ?>
                    <input type="hidden" name="quantity[<?php echo $item['pid']; ?>]" value="<?php echo $item['quantity']; ?>">
                <?php endforeach; ?>
                <button type="submit" class="update-cart-btn" style="display: none;" id="updateBtn">Update Cart</button>
            </form>
            <a href="cart.php?action=empty" class="empty-cart-btn" onclick="return confirmEmpty()">Empty Cart</a>
        </div>
    </div>

    <form method="post" action="cart.php?action=update" id="cartForm">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cart_items as $item): ?>
                <tr>
                    <td>
                        <?php if(!empty($item["image"])): ?>
                            <img src="../images/<?php echo $item["image"]; ?>" alt="<?php echo $item["name"]; ?>" class="product-image">
                        <?php else: ?>
                            <div style="width: 80px; height: 80px; background-color: #f0f0f0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; color: #999; font-size: 12px;">No Image</div>
                        <?php endif; ?>
                    </td>
                    <td class="product-name"><?php echo $item["name"]; ?></td>
                    <td>TK <?php echo $item["price"]; ?></td>
                    <td>
                        <input type="number" 
                               name="quantity[<?php echo $item["pid"]; ?>]" 
                               value="<?php echo $item["quantity"]; ?>" 
                               min="0" 
                               class="quantity-input"
                               onchange="showUpdateButton(); validateQuantity(this);">
                    </td>
                    <td>TK <?php echo number_format(($item["price"] * $item["quantity"]), 2); ?></td>
                    <td>
                        <a href="cart.php?action=remove&code=<?php echo $item["pid"]; ?>" 
                           class="remove-btn" 
                           onclick="return confirm('Are you sure you want to remove this item?')">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="update-cart-btn" style="display: none;" id="updateFormBtn">Update Cart</button>
    </form>

    <div class="cart-total">
        <div>
            <strong>Total Items: <?php echo $cart_count; ?></strong>
        </div>
        <div class="total-amount">
            Total Amount: TK <?php echo number_format($cart_total, 2); ?>
        </div>
    </div>

    <div class="checkout-section">
        <h3>Ready to Checkout?</h3>
        <p>Review your items above and proceed to checkout when ready.</p>
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        <div style="margin-top: 15px;">
            <a href="customer_dashboard.php" class="continue-shopping">Add More Items</a>
        </div>
    </div>

    <?php else: ?>
    
    <div class="empty-cart">
        <h2>Your cart is empty</h2>
        <p>You haven't added any items to your cart yet.</p>
        <a href="customer_dashboard.php" class="continue-shopping">Start Shopping</a>
    </div>
    
    <?php endif; ?>
</div>

<script>
function showUpdateButton() {
    document.getElementById('updateFormBtn').style.display = 'inline-block';
}
</script>
</body>
</html>