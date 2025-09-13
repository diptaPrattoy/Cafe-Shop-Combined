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

        <a href="dashboard.php" class="logo">Admin Panel</a>

        <nav class="navbar">
            <a href="dashboard.php">Home</a>
            <a href="admin_accounts.php">Admins</a>
            <a href="employee_accounts.php">Employees</a>
            <a href="users_accounts.php">Users</a>
            <a href="products.php">Products</a>
            <a href="placed_orders.php">Orders</a>
            <a href="messages.php">Messages</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>

            <?php
            // Fetch admin profile info
            $select_profile = $conn->prepare("SELECT * FROM `all_users` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

            // Determine profile image
            $profile_img = !empty($fetch_profile['profile_image']) ? $fetch_profile['profile_image'] : 'default.png';
            ?>

            <!-- Show profile image instead of generic icon -->
            <div id="user-btn" class="user-icon">
                <img src="../uploaded_img/<?= htmlspecialchars($profile_img); ?>" alt="profile"
                    style="width:35px;height:35px;border-radius:50%;object-fit:cover;">
            </div>
        </div>

        <div class="profile">
            <img src="../uploaded_img/<?= htmlspecialchars($profile_img); ?>" alt="profile"
                style="width:50px;height:50px;border-radius:50%;margin-bottom:5px;object-fit:cover;">
            <p><?= htmlspecialchars($fetch_profile['username']); ?></p>
            <a href="update_profile.php" class="btn">update profile</a>
            <a href="../admin/admin_logout.php" onclick="return confirm('logout from this website?');"
                class="delete-btn">logout</a>
        </div>

    </section>

</header>