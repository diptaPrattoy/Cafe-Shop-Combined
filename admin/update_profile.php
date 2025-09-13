<?php
include '../components/connect.php';
session_start();

// $admin_id = $_SESSION['user_id'];
// if (!isset($admin_id)) {
//     header('location:../login.php');
//     exit;
// }


// Get employee ID from URL
$admin_id = $_GET['id'] ?? null;
if (!$admin_id) {
    header('location:admin_accounts.php');
    exit;
}








// Fetch current admin details
$select_profile = $conn->prepare("SELECT * FROM `all_users` WHERE id = ? && type='admin'");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

$message = [];

if (isset($_POST['submit'])) {

    // Sanitize input; keep null if left empty
    $username = !empty($_POST['username']) ? filter_var($_POST['username'], FILTER_SANITIZE_STRING) : $fetch_profile['username'];
    $email = !empty($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : $fetch_profile['email'];
    $number = !empty($_POST['number']) ? filter_var($_POST['number'], FILTER_SANITIZE_STRING) : $fetch_profile['number'];
    $phone = !empty($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : $fetch_profile['phone'];
    $age = !empty($_POST['age']) ? filter_var($_POST['age'], FILTER_SANITIZE_NUMBER_INT) : $fetch_profile['age'];
    $sex = !empty($_POST['sex']) ? filter_var($_POST['sex'], FILTER_SANITIZE_STRING) : $fetch_profile['sex'];
    $address = !empty($_POST['address']) ? filter_var($_POST['address'], FILTER_SANITIZE_STRING) : $fetch_profile['address'];

    // Check if username is taken by another admin
    if ($username != $fetch_profile['username']) {
        $check_username = $conn->prepare("SELECT * FROM `all_users` WHERE username = ? AND id != ?");
        $check_username->execute([$username, $admin_id]);
        if ($check_username->rowCount() > 0) {
            $message[] = 'Username already taken!';
        }
    }
$profile_image_to_save = $fetch_profile['profile_image']; // default: keep current

// Check if user uploaded a file
if (!empty($_FILES['profile_image']['name'])) {
    $image_name = $_FILES['profile_image']['name'];
    $image_tmp = $_FILES['profile_image']['tmp_name'];
    $ext = pathinfo($image_name, PATHINFO_EXTENSION);
    $allowed_ext = ['jpg','jpeg','png','gif'];

    if (in_array(strtolower($ext), $allowed_ext)) {
        // Move uploaded file to folder
        move_uploaded_file($image_tmp, "../uploaded_img/$image_name");

        // Just save the filename, don't delete any existing image
        $profile_image_to_save = $image_name;
    } else {
        $message[] = 'Invalid image format! Allowed: jpg, jpeg, png, gif.';
    }
}

// If user chose an existing image from folder (e.g., via a select box)
if (!empty($_POST['existing_image'])) {
    $profile_image_to_save = $_POST['existing_image'];
}

// Update in database
$update_admin = $conn->prepare("UPDATE `all_users` SET username=?, email=?, number=?, phone=?, age=?, sex=?, address=?, profile_image=? WHERE id=?");
$update_admin->execute([$username, $email, $number, $phone, $age, $sex, $address, $profile_image_to_save, $admin_id]);


    // Update password if provided
    $empty_pass = '';
    if (!empty($_POST['old_pass']) || !empty($_POST['new_pass']) || !empty($_POST['confirm_pass'])) {
        $old_pass_input = sha1($_POST['old_pass']);
        $new_pass_input = sha1($_POST['new_pass']);
        $confirm_pass_input = sha1($_POST['confirm_pass']);
        if ($old_pass_input != $fetch_profile['password']) {
            $message[] = 'Old password not matched!';
        } elseif ($new_pass_input != $confirm_pass_input) {
            $message[] = 'Confirm password not matched!';
        } elseif ($new_pass_input != $empty_pass) {
            $update_pass = $conn->prepare("UPDATE `all_users` SET password = ? WHERE id = ?");
            $update_pass->execute([$confirm_pass_input, $admin_id]);
            $message[] = 'Password updated successfully!';
        }
    }

    // Update admin details
    if (empty($message)) {
        $update_admin = $conn->prepare("UPDATE `all_users` SET username=?, email=?, number=?, phone=?, age=?, sex=?, address=?, profile_image=? WHERE id=?");
        $update_admin->execute([$username, $email, $number, $phone, $age, $sex, $address, $profile_image_to_save, $admin_id]);
        $message[] = 'Profile updated successfully!';
    }

    // Refresh data
    $select_profile->execute([$admin_id]);
    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Admin Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard_style.css">
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <section class="form-container">
        <form action="" method="POST" enctype="multipart/form-data">
            <h3>Update Profile</h3>

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


            <img src="../uploaded_img/<?= htmlspecialchars($fetch_profile['profile_image']); ?>" alt="profile"
                style="width:80px;height:80px;border-radius:50%;margin-bottom:5px;">
            <input type="file" name="profile_image" class="box">

            <input type="text" name="username" maxlength="50" placeholder="Username" class="box"
                value="<?= htmlspecialchars($fetch_profile['username']); ?>">
            <input type="email" name="email" maxlength="100" placeholder="Email" class="box"
                value="<?= htmlspecialchars($fetch_profile['email']); ?>">
            <input type="text" name="number" maxlength="20" placeholder="Number" class="box"
                value="<?= htmlspecialchars($fetch_profile['number']); ?>">
            <input type="text" name="phone" maxlength="20" placeholder="Phone" class="box"
                value="<?= htmlspecialchars($fetch_profile['phone']); ?>">
            <input type="number" name="age" maxlength="3" placeholder="Age" class="box"
                value="<?= htmlspecialchars($fetch_profile['age']); ?>">
            <select name="sex" class="box">
                <option value="" disabled>Select Sex</option>
                <option value="Male" <?= $fetch_profile['sex']=='Male'?'selected':''; ?>>Male</option>
                <option value="Female" <?= $fetch_profile['sex']=='Female'?'selected':''; ?>>Female</option>
                <option value="Other" <?= $fetch_profile['sex']=='Other'?'selected':''; ?>>Other</option>
            </select>
            <textarea name="address" placeholder="Address" class="box"
                rows="3"><?= htmlspecialchars($fetch_profile['address']); ?></textarea>

            <h4>Change Password</h4>
            <input type="password" name="old_pass" placeholder="Old Password" class="box">
            <input type="password" name="new_pass" placeholder="New Password" class="box">
            <input type="password" name="confirm_pass" placeholder="Confirm Password" class="box">

            <input type="submit" name="submit" value="Update Now" class="btn">
        </form>
    </section>

    <script src="../js/admin_script.js"></script>
</body>

</html>