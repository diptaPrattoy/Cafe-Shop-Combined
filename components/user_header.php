<?php
if (isset($message)) {
    foreach ($message as $message) {
        echo '
        <div class="message">
            <span>' . $message . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<header class="header">

    <section class="flex">

        <a href="home.php" class="logo">
            <!-- <img src="../icon/cafe.png" alt=" Cozy Cafe"> -->
            <img src="./icon/cafe.png" height="" width="" alt=" Cozy Cafe">
        </a>

        <nav class="navbar">
            <a href="home.php">HOME</a>
            <a href="about.php">ABOUT</a>
            <a href="product.php">PRODUCT</a>
            <a href="menu.php">MENU</a>
            <a href="orders.php">ORDER</a>
        </nav>

        <div class="icons">
            <?php
            // Count cart items for the logged-in user
            $count_cart_items = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
            ?>
            <a href="search.php"><i class="fas fa-search"></i></a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_items; ?>)</span></a>
            <div id="user-btn" class="fas fa-user"></div>
            <div id="menu-btn" class="fas fa-bars"></div>
        </div>

        <div class="profile">
            <?php
            // Fetch user profile from all_users table
            $select_profile = $conn->prepare("SELECT * FROM all_users WHERE id = ?");
            $select_profile->execute([$user_id]);
            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <p class="name"><?= htmlspecialchars($fetch_profile['username']); ?></p>
            <div class="flex">
                <a style="margin-left: 10px;" href="customer/profile.php" class="btn">profile</a>
                <a href="./signout.php" onclick="return confirm('Logout from this website?');"
                    class="delete-btn">logout</a>
            </div>
            <?php
            } else {
            ?>
            <p class="name">please login first!</p>
            <a href="login.php" class="btn">login</a>
            <?php
            }
            ?>
        </div>

    </section>

</header>