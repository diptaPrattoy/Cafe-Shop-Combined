<?php
session_start();
include '../components/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
$user_id = intval($_SESSION['user_id']);

// Fetch user info using PDO
$select_user = $conn->prepare("SELECT * FROM all_users WHERE id = ?");
$select_user->execute([$user_id]);
$user = $select_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_profile'])) {
        $fields = ['username', 'email', 'number', 'address', 'age', 'sex', 'phone'];
        $update_data = [];
        $placeholders = [];

        foreach ($fields as $field) {
            if (isset($_POST[$field]) && !empty($_POST[$field])) {
                $update_data[] = $field . " = ?";
                $placeholders[] = $_POST[$field];
            }
        }

        // Handle password change
        if (isset($_POST['change_password']) && !empty($_POST['change_password'])) {
            $update_data[] = "password = ?";
            $placeholders[] = password_hash($_POST['change_password'], PASSWORD_DEFAULT);
        }

        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $img_name = basename($_FILES['profile_image']['name']);
            $target = '../images/' . $img_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
                $update_data[] = "profile_image = ?";
                $placeholders[] = $img_name;
            }
        }

        if (!empty($update_data)) {
            $placeholders[] = $user_id; // Add user_id for WHERE clause
            $sql = "UPDATE all_users SET " . implode(", ", $update_data) . " WHERE id = ?";
            $update_stmt = $conn->prepare($sql);
            $result = $update_stmt->execute($placeholders);

            if ($result) {
                $success = "Profile updated successfully.";
                // Re-fetch user data
                $select_user = $conn->prepare("SELECT * FROM all_users WHERE id = ?");
                $select_user->execute([$user_id]);
                $user = $select_user->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Failed to update profile.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="customerCSS/profile.css" type="text/css" rel="stylesheet" />
</head>

<body>

    <div class="navigation-header">
        <a href="customer_dashboard.php" class="back-link">← Back to Dashboard</a>
        <h1>My Profile</h1>
    </div>

    <div class="profile-card">
        <img src="../uploaded_img/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Picture">
        <h2><?php echo htmlspecialchars($user['username'] ?? 'User'); ?></h2>
        <div class="profile-details">
            <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
            <strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?><br>
            <strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?><br>
            <strong>Sex:</strong> <?php echo htmlspecialchars($user['sex']); ?><br>
            <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?><br>
            <strong>Type:</strong> <?php echo htmlspecialchars($user['type']); ?><br>
        </div>
        <button class="edit-btn"
            onclick="document.getElementById('edit-form').style.display='block';this.style.display='none';">Edit
            Profile</button>
        <?php if ($success) { echo '<div class="success">'.$success.'</div>'; } ?>
        <?php if ($error) { echo '<div class="error">'.$error.'</div>'; } ?>
        <form id="edit-form" class="edit-form" method="post" action="" enctype="multipart/form-data">

            <label for="profile_image">Profile Image:</label><br>
            <input type="file" id="profile_image" name="profile_image" accept="image/*"><br><br>

            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username"
                value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"><br><br>

            <label for="number">Number:</label><br>
            <input type="text" id="number" name="number"
                value="<?php echo htmlspecialchars($user['number']); ?>"><br><br>


            <label for="change_password">Change Password:</label><br>
            <input type="password" id="change_password" name="change_password"
                placeholder="Leave blank to keep current password"><br><br>

            <label for="address">Address:</label><br>
            <textarea id="address" name="address" rows="3"
                cols="40"><?php echo htmlspecialchars($user['address']); ?></textarea><br><br>

            <label for="age">Age:</label><br>
            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>"><br><br>

            <label for="sex">Sex:</label><br>
            <input type="text" id="sex" name="sex" value="<?php echo htmlspecialchars($user['sex']); ?>"><br><br>

            <label for="phone">Phone:</label><br>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"><br><br>



            <input type="submit" name="edit_profile" value="Save Changes">
        </form>
    </div>
</body>

</html>