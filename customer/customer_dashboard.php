<?php

session_start();
include '../components/connect.php';


$admin_id = $_SESSION['user_id'];
$admin_type= $_SESSION['type'];
if (!isset($admin_id) || !isset($admin_type)) {
    header('location: ../login.php');
}

?>

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

<?php
$select_profile = $conn->prepare("SELECT * FROM `all_users` WHERE  id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>

    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="customerCSS/customer_dashboard.css">
    <link rel="stylesheet" href="customerCSS/customer_products.css">

</head>

<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <img class="logo-image" src="../images/08052021-05_generated-removebg-preview.png"
                            alt="Cozy Cafe">
                        <P class="logo-text"><span class="title">Cozy Cafe</span></P>

                    </a>

                </li>

                <li>
                    <a href="customer_dashboard.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="profile.php">
                        <span class="icon">
                            <ion-icon name="person-outline"></ion-icon>
                        </span>
                        <span class="title">My Profile</span>
                    </a>
                </li>

                <li>
                    <a href="cart.php">
                        <span class="icon">
                            <ion-icon name="basket-outline"></ion-icon>
                        </span>
                        <span class="title">My Cart</span>
                    </a>
                </li>
                <li>
                    <a href="../home.php">
                        <span class="icon">
                            <ion-icon name="basket-outline"></ion-icon>
                        </span>
                        <span class="title">Home</span>
                    </a>
                </li>

                <!-- <li>
                    <a href="placed_orders.php">
                        <span class="icon">
                            <ion-icon name="receipt-outline"></ion-icon>
                        </span>
                        <span class="title">My Orders</span>
                    </a>
                </li> -->

                <li>
                    <a href="messages.php">
                        <span class="icon">
                            <ion-icon name="chatbubble-outline"></ion-icon>
                        </span>
                        <span class="title">messages</span>
                    </a>
                </li>

                <li>
                    <a href="../signout.php" onclick="return confirm('logout from this website?');"
                        class="delete-btn"><span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span></a>
                </li>
            </ul>
        </div>

        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>

                <a href="customer_dashboard.php" class="logo">
                    <h1 class="customer-panel-title">Customer<span class="panel-accent">Panel</span></h1>
                </a>

                <div class="">
                    <p>Welcome! <?= $fetch_profile['username']; ?></p>
                    <div class="icons">
                        <div id="user-btn" class="fas fa-user"></div>
                    </div>
                </div>


            </div>

            <section class="dashboard">
                <h1 class="heading dashboard-heading">CUSTOMER DASHBOARD</h1>

                <?php include 'customer_products.php'; ?>

            </section>



        </div>
    </div>
    </div>

    <script src="../js/nav.js"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>