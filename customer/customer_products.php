<?php
session_start();
include '../components/connect.php';

// Handle add to cart action
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "add":
            if (!empty($_POST["quantity"])) {
                $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                $select_product->execute([$_GET["code"]]);
                $productByCode = $select_product->fetch(PDO::FETCH_ASSOC);

                if ($productByCode) {
                    $user_id = $_SESSION['user_id'] ?? 0;
                    $quantity = intval($_POST["quantity"]);

                    if ($user_id > 0) {
                        // Check if item already exists in cart
                        $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND pid = ?");
                        $check_cart->execute([$user_id, $productByCode["id"]]);

                        if ($check_cart->rowCount() > 0) {
                            // Update existing cart item
                            $existing_item = $check_cart->fetch(PDO::FETCH_ASSOC);
                            $new_quantity = $existing_item['quantity'] + $quantity;

                            $update_cart = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE user_id = ? AND pid = ?");
                            $update_cart->execute([$new_quantity, $user_id, $productByCode["id"]]);
                        } else {
                            // Insert new cart item
                            $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
                            $insert_cart->execute([
                                $user_id,
                                $productByCode["id"],
                                $productByCode["name"],
                                $productByCode["price"],
                                $quantity,
                                $productByCode["image"]
                            ]);
                        }

                        echo '<div class="success-message">Product added to cart successfully!</div>';
                    } else {
                        echo '<div class="error-message">Please log in to add items to cart.</div>';
                    }
                }
            }
            break;
    }
}

// Get cart count from database
$cart_count = 0;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    $user_id = $_SESSION['user_id'];
    $cart_count_query = $conn->prepare("SELECT SUM(quantity) as total_quantity FROM `cart` WHERE user_id = ?");
    $cart_count_query->execute([$user_id]);
    $cart_count_result = $cart_count_query->fetch(PDO::FETCH_ASSOC);
    $cart_count = $cart_count_result['total_quantity'] ?? 0;
}

// Handle search
$searchQuery = "";
$whereClause = "";
$params = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $whereClause = "WHERE name LIKE :search OR category LIKE :search";
    $params[':search'] = "%{$searchQuery}%";
}
?>

<!-- Cart and Search Section -->
<div class="cart-search-section">
    <div>
        <a href="cart.php" class="cart-link">
            View Cart
            <?php if ($cart_count > 0): ?>
            (<?php echo $cart_count; ?>)
            <?php endif; ?>
        </a>
    </div>

    <!-- Search Form -->
    <form method="GET" action="" class="search-form">
        <input type="text" name="search" placeholder="Search products..."
            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" class="search-input">
        <button type="submit" class="search-button">
            Search
        </button>
        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
        <a href="customer_product.php" class="clear-link">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Products Grid -->
<div class="products-container">
    <?php
    // Fetch products
    if (!empty($whereClause)) {
        $select_products = $conn->prepare("SELECT * FROM `products` $whereClause ORDER BY name ASC");
        $select_products->execute($params);
    } else {
        $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY name ASC");
        $select_products->execute();
    }

    if ($select_products->rowCount() > 0) {
        echo '<div class="products-grid">';

        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
    ?>
    <div class="product-card">
        <form method="post" action="customer_dashboard.php?action=add&code=<?php echo $fetch_products["id"]; ?>">
            <div class="product-image-container">
                <?php if (!empty($fetch_products["image"])): ?>
                <img src="../uploaded_img/<?php echo $fetch_products["image"]; ?>"
                    alt="<?php echo $fetch_products["name"]; ?>" class="product-image">
                <?php else: ?>
                <span class="no-image-text">No Image</span>
                <?php endif; ?>
            </div>

            <div class="product-name">
                <?php echo htmlspecialchars($fetch_products["name"]); ?>
            </div>

            <div class="product-category">
                <?php echo htmlspecialchars($fetch_products["category"]); ?>
            </div>

            <div class="product-price">
                TK <?php echo number_format($fetch_products["price"], 2); ?>
            </div>

            <div class="product-actions">
                <input type="number" name="quantity" value="1" min="1" max="10" class="quantity-input">
                <input type="submit" value="Add to Cart" class="add-to-cart-btn">
            </div>
        </form>
    </div>
    <?php
        }
        echo '</div>';
    } else {
        echo '<div class="no-products">';
        if (!empty($searchQuery)) {
            echo 'No products found matching your search.';
        } else {
            echo 'No products available.';
        }
        echo '</div>';
    }
    ?>
</div>