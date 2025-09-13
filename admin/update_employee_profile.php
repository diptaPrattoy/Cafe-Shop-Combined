<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['user_id'];
if (!isset($admin_id)) {
    header('location:../login.php');
    exit;
}

// Get employee ID from URL
$employee_id = $_GET['id'] ?? null;
if (!$employee_id) {
    header('location:employee_accounts.php');
    exit;
}

// Fetch employee details
$select_employee = $conn->prepare("SELECT * FROM `all_users` WHERE id = ? AND type='employee'");
$select_employee->execute([$employee_id]);
$employee = $select_employee->fetch(PDO::FETCH_ASSOC);
if (!$employee) {
    header('location:employee_accounts.php');
    exit;
}

$message = [];

if (isset($_POST['submit'])) {

    // Keep previous value if input is empty
    $username = trim($_POST['username']) ?: $employee['username'];
    $email    = trim($_POST['email']) ?: $employee['email'];
    $number   = trim($_POST['number']) !== '' ? trim($_POST['number']) : $employee['number'];
    $phone    = trim($_POST['phone']) !== '' ? trim($_POST['phone']) : $employee['phone'];
    $age      = trim($_POST['age']) !== '' ? trim($_POST['age']) : $employee['age'];
    $sex      = trim($_POST['sex']) ?: $employee['sex'];
    $address  = trim($_POST['address']) ?: $employee['address'];

    // Check if username is taken by another employee
    $check_username = $conn->prepare("SELECT * FROM `all_users` WHERE username = ? AND id != ?");
    $check_username->execute([$username, $employee_id]);
    if ($check_username->rowCount() > 0) {
        $message[] = 'Username already taken!';
    }

    // Handle profile image
    $profile_image_to_save = $employee['profile_image']; // default keep current

    // If user uploaded a new image
    if (!empty($_FILES['profile_image']['name'])) {
        $image_name = $_FILES['profile_image']['name'];
        $image_tmp = $_FILES['profile_image']['tmp_name'];
        $ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg','jpeg','png','gif'];

        if (in_array(strtolower($ext), $allowed_ext)) {
            // Move uploaded image to folder
            move_uploaded_file($image_tmp, "../uploaded_img/$image_name");
            $profile_image_to_save = $image_name; // store filename
        } else {
            $message[] = 'Invalid image format! Allowed: jpg, jpeg, png, gif.';
        }
    }

    // If user chose an existing image from folder
    if (!empty($_POST['existing_image'])) {
        $profile_image_to_save = $_POST['existing_image'];
    }

    // Update password if provided
    if (!empty($_POST['new_pass']) && !empty($_POST['confirm_pass'])) {
        $new_pass = $_POST['new_pass'];
        $confirm_pass = $_POST['confirm_pass'];

        if ($new_pass !== $confirm_pass) {
            $message[] = 'Password confirmation does not match!';
        } else {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_pass = $conn->prepare("UPDATE `all_users` SET password=? WHERE id=?");
            $update_pass->execute([$hashed_pass, $employee_id]);
            $message[] = 'Password updated!';
        }
    }

    // Update employee details
    if (empty($message)) {
        $update_employee = $conn->prepare("UPDATE `all_users`
            SET username=?, email=?, number=?, phone=?, age=?, sex=?, address=?, profile_image=?
            WHERE id=?");
        $update_employee->execute([$username, $email, $number, $phone, $age, $sex, $address, $profile_image_to_save, $employee_id]);
        $message[] = 'Employee details updated!';
    }

    // Refresh employee data
    $select_employee->execute([$employee_id]);
    $employee = $select_employee->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee</title>
    <link rel="stylesheet" href="../css/dashboard_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="form-container">
        <form action="" method="POST" enctype="multipart/form-data">
            <h3>Update Employee</h3>

            <?php
if (!empty($message)) {
    if (is_array($message)) {
        foreach ($message as $msg) {
            echo '<p class="message">' . $msg . '</p>';
        }
    } else {
        // If $message is a string
        echo '<p class="message">' . $message . '</p>';
    }
}
?>

            <img src="../uploaded_img/<?= htmlspecialchars($employee['profile_image']); ?>" alt="profile"
                style="width:80px;height:80px;border-radius:50%;margin-bottom:10px;">
            <input type="file" name="profile_image" class="box">

            <input type="text" name="username" class="box" required placeholder="Username"
                value="<?= htmlspecialchars($employee['username']); ?>">
            <input type="email" name="email" class="box" placeholder="Email"
                value="<?= htmlspecialchars($employee['email']); ?>">
            <input type="text" name="number" class="box" placeholder="Number"
                value="<?= htmlspecialchars($employee['number']); ?>">
            <input type="text" name="phone" class="box" placeholder="Phone"
                value="<?= htmlspecialchars($employee['phone']); ?>">
            <input type="number" name="age" class="box" placeholder="Age"
                value="<?= htmlspecialchars($employee['age']); ?>">
            <input type="text" name="sex" class="box" placeholder="Sex"
                value="<?= htmlspecialchars($employee['sex']); ?>">
            <textarea name="address" class="box"
                placeholder="Address"><?= htmlspecialchars($employee['address']); ?></textarea>

            <h4>Change Password</h4>
            <input type="password" name="new_pass" class="box" placeholder="New Password">
            <input type="password" name="confirm_pass" class="box" placeholder="Confirm Password">

            <input type="submit" value="Update Employee" name="submit" class="btn">
        </form>
    </section>

    <script src="../js/admin_script.js"></script>
</body>

</html>