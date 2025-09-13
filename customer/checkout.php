<?php
session_start();
include '../components/connect.php';

// Get cart data from database
$user_id = $_SESSION['user_id'];
$select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$select_cart->execute([$user_id]);
$cart_items = $select_cart->fetchAll(PDO::FETCH_ASSOC);

// Check if cart is empty
if(empty($cart_items)) {
    header("Location: customer_dashboard.php");
    exit();
}

$message = "";
$error = "";

// Calculate cart totals
$cart_count = 0;
$cart_total = 0;
$total_products = "";

foreach($cart_items as $item) {
    $cart_count += $item["quantity"];
    $cart_total += ($item["price"] * $item["quantity"]);
    $total_products .= $item["name"] . " (" . $item["price"] . " x " . $item["quantity"] . ") - ";
}
$total_products = rtrim($total_products, " - ");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $number = trim($_POST["number"]);
    $address = trim($_POST["address"]);
    $payment_method = $_POST["payment_method"];
    
    // Basic validation
    if(empty($name) || empty($email) || empty($number) || empty($address) || empty($payment_method)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } elseif (!preg_match('/^[0-9+\-\s()]+$/', $number)) {
        $error = "Please enter a valid phone number!";
    } else {
        try {
            // Use the actual logged-in user ID
            $user_id = $_SESSION['user_id'];
            
            // Insert order into database using PDO prepared statement
            $insert_query = $conn->prepare("INSERT INTO orders (user_id, name, number, email, method, address, total_products, total_price, payment_status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            
            $result = $insert_query->execute([$user_id, $name, $number, $email, $payment_method, $address, $total_products, $cart_total]);
            
            if($result) {
                // Clear the database cart after successful order
                $clear_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
                $clear_cart->execute([$user_id]);
                
                $order_id = $conn->lastInsertId();
                $message = "Order placed successfully! Your order ID is: " . $order_id;
            } else {
                $error = "Failed to place order. Please try again.";
            }
        } catch (Exception $e) {
            $error = "An error occurred while processing your order. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="customerCSS/checkout.css">
</head>
<body>
<div class="header">
    <h1>Checkout</h1>
    <a href="cart.php" class="back-link">Back to Cart</a>
</div>

<?php if($message): ?>
<div class="success-container">
    <div class="success-icon">✅</div>
    <h2>Order Placed Successfully!</h2>
    <div class="message success"><?php echo $message; ?></div>
    <p>Thank you for your order! You will receive a confirmation email shortly.</p>
    <a href="customer_dashboard.php" class="continue-shopping">Continue Shopping</a>
    <a href="profile.php" class="continue-shopping">View Profile</a>
</div>
<?php else: ?>

<div class="container">
    <!-- Checkout Form -->
    <div class="checkout-form">
        <h2>Billing Information</h2>
        
        <?php if($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" id="checkoutForm">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="number">Phone Number *</label>
                <input type="tel" id="number" name="number" required 
                       value="<?php echo isset($_POST['number']) ? htmlspecialchars($_POST['number']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Address *</label>
                <textarea id="address" name="address" required 
                         placeholder="Enter your complete address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Payment Method *</label>
                <div class="payment-methods">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="cash on delivery" required>
                        Cash on Delivery
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="credit card" required>
                        Credit Card
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="debit card" required>
                        Debit Card
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="bkash" required>
                        bKash
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="nagad" required>
                        Nagad
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="bank transfer" required>
                        Bank Transfer
                    </label>
                </div>
            </div>
            
            <button type="submit" class="place-order-btn">Place Order</button>
        </form>
    </div>
    
    <!-- Order Summary -->
    <div class="order-summary">
        <h2>Order Summary</h2>
        
        <?php if($cart_count > 0): ?>
            <?php foreach($cart_items as $item): ?>
            <div class="order-item">
                <div class="item-info">
                    <div class="item-name"><?php echo $item["name"]; ?></div>
                    <div class="item-price">TK <?php echo $item["price"]; ?> x <?php echo $item["quantity"]; ?></div>
                </div>
                <div class="item-total">TK <?php echo number_format(($item["price"] * $item["quantity"]), 2); ?></div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="order-total">
            <div class="total-row">
                <span>Total Items:</span>
                <span><?php echo $cart_count; ?></span>
            </div>
            <div class="total-row">
                <span>Subtotal:</span>
                <span>TK <?php echo number_format($cart_total, 2); ?></span>
            </div>
            <div class="total-row">
                <span>Shipping:</span>
                <span>Free</span>
            </div>
            <div class="total-row grand-total">
                <span>Total Amount:</span>
                <span>TK <?php echo number_format($cart_total, 2); ?></span>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<script>
// Handle payment method selection styling
document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        // Remove selected class from all options
        document.querySelectorAll('.payment-option').forEach(function(option) {
            option.classList.remove('selected');
        });
        
        // Add selected class to chosen option
        this.closest('.payment-option').classList.add('selected');
    });
});

// Form validation
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const number = document.getElementById('number').value.trim();
    const address = document.getElementById('address').value.trim();
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    
    if (!name || !email || !number || !address || !paymentMethod) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Please enter a valid email address.');
        return false;
    }
    
    // Phone validation
    const phoneRegex = /^[0-9+\-\s()]+$/;
    if (!phoneRegex.test(number)) {
        e.preventDefault();
        alert('Please enter a valid phone number.');
        return false;
    }
    
    // Confirm order
    if (!confirm('Are you sure you want to place this order?')) {
        e.preventDefault();
        return false;
    }
});
</script>
</body>
</html>