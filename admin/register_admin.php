<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['user_id'];

if (!isset($admin_id)) {
   header('location:../login.php');
   exit;
}

if (isset($_POST['submit'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   $select_user = $conn->prepare("SELECT * FROM `all_users` WHERE username = ?  ");
   $select_user->execute([$name]);

   if ($select_user->rowCount() > 0) {
      $message[] = 'Username already exists!';
   } else {
      if ($pass !== $cpass) {
         $message[] = 'Confirm password not matched!';
      } else {
         // ✅ Secure hash
         $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

         $insert_user = $conn->prepare("INSERT INTO `all_users` (username, password, type) VALUES (?, ?, ?)");
         $insert_user->execute([$name, $hashedPassword, 'admin']);
         $message[] = 'New admin registered!';
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
    <title>Register</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/dashboard_style.css">

</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <section class="form-container">
        <form action="" method="POST">
            <h3>Register new admin</h3>
            <input type="text" name="name" maxlength="20" required placeholder="Enter username" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" maxlength="20" required placeholder="Enter password" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="cpass" maxlength="20" required placeholder="Confirm password" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Register now" name="submit" class="btn">
        </form>
    </section>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>