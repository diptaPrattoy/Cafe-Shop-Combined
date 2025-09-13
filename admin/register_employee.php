<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['user_id'] ?? null;

if (!$admin_id) {
   header('location:../login.php');
   exit;
}

if (isset($_POST['submit'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $age = filter_var($_POST['age'], FILTER_SANITIZE_NUMBER_INT);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   // handle profile image
   $image = $_FILES['profile_image']['name'];
   $image_tmp_name = $_FILES['profile_image']['tmp_name'];
   $image_size = $_FILES['profile_image']['size'];
   $image_folder = '../uploaded_img/' . $image;

   // check duplicate username
   $select_employee = $conn->prepare("SELECT * FROM `all_users` WHERE username = ?");
   $select_employee->execute([$name]);

   if ($select_employee->rowCount() > 0) {
      $message[] = 'Username already exists!';
   } else {
      if ($pass !== $cpass) {
         $message[] = 'Confirm password not matched!';
      } else {
         // hash password
         $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

         // validate image size
         if ($image_size > 2000000) { // 2MB limit
            $message[] = 'Image size is too large!';
         } else {
            if (!empty($image)) {
               move_uploaded_file($image_tmp_name, $image_folder);
            } else {
               $image = 'default.png'; // fallback if no image uploaded
            }

            // insert employee
            $insert_employee = $conn->prepare("INSERT INTO `all_users` (username, age, password, profile_image, type) VALUES (?, ?, ?, ?, 'employee')");
            $insert_employee->execute([$name, $age, $hashedPassword, $image]);

            $message[] = 'New employee registered!';
         }
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
    <title>Register Employee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard_style.css">
</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <section class="form-container">
        <form action="" method="POST" enctype="multipart/form-data">
            <h3>Register New Employee</h3>

            <input type="text" name="name" maxlength="20" required placeholder="Enter employee username" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">

            <input type="number" name="age" min="18" max="99" required placeholder="Enter employee age" class="box">

            <input type="password" name="pass" maxlength="20" required placeholder="Enter password" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">

            <input type="password" name="cpass" maxlength="20" required placeholder="Confirm password" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">

            <input type="file" name="profile_image" accept="image/*" class="box">

            <input type="submit" value="Register Now" name="submit" class="btn">
        </form>

        <?php if (!empty($message)): ?>
        <div class="messages">
            <?php foreach ($message as $msg): ?>
            <p><?= $msg ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <script src="../js/admin_script.js"></script>
</body>

</html>