<?php
include '../components/connect.php';
session_start();

// If you want only admins to register users
$admin_id = $_SESSION['user_id'];
if (!isset($admin_id)) {
   header('location:../login.php');
   exit;
}

if (isset($_POST['submit'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);

   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   // Check if username or email already exists
   $select_user = $conn->prepare("SELECT * FROM `all_users` WHERE username = ? OR email = ?");
   $select_user->execute([$name, $email]);

   if ($select_user->rowCount() > 0) {
      $message[] = 'Username or email already exists!';
   } else {
      if ($pass !== $cpass) {
         $message[] = 'Confirm password not matched!';
      } else {
         // ✅ Hash password before saving
         $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

         // Insert new user into all_users
         $insert_user = $conn->prepare("INSERT INTO `all_users` (username, password, email, type) VALUES (?, ?, ?, ?)");
         $insert_user->execute([$name, $hashedPassword, $email, 'user']);

         $message[] = 'New user registered successfully!';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/dashboard_style.css">
</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <section class="form-container">
        <form action="" method="POST">
            <h3>Register new user</h3>

            <input type="text" name="name" maxlength="20" required placeholder="Enter username" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">

            <input type="email" name="email" maxlength="50" required placeholder="Enter email" class="box">

            <input type="password" name="pass" maxlength="20" required placeholder="Enter password" class="box">

            <input type="password" name="cpass" maxlength="20" required placeholder="Confirm password" class="box">

            <input type="submit" value="Register now" name="submit" class="btn">
        </form>

        <?php if (!empty($message) && is_array($message)): ?>
        <div class="messages">
            <?php foreach ($message as $msg): ?>
            <p><?= $msg ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>
</body>

</html>