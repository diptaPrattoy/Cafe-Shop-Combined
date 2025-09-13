<?php
session_start();
include '../components/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
$user_id = intval($_SESSION['user_id']);

// Fetch current user info using PDO
$select_user = $conn->prepare("SELECT * FROM all_users WHERE id = ?");
$select_user->execute([$user_id]);
$user = $select_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_address'])) {
        try {
            $address = $_POST['address'];
            $update_address = $conn->prepare("UPDATE all_users SET address = ? WHERE id = ?");
            $result = $update_address->execute([$address, $user_id]);
            
            if ($result) {
                $success = "Address updated successfully.";
                // Re-fetch user data
                $select_user = $conn->prepare("SELECT * FROM all_users WHERE id = ?");
                $select_user->execute([$user_id]);
                $user = $select_user->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Failed to update address.";
            }
        } catch (Exception $e) {
            $error = "An error occurred while updating address.";
        }
    }
    
    if (isset($_POST['update_password'])) {
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        
        if ($password === $confirm && !empty($password)) {
            try {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $update_pass = $conn->prepare("UPDATE all_users SET password = ? WHERE id = ?");
                $result = $update_pass->execute([$hashed, $user_id]);
                
                if ($result) {
                    $success = "Password updated successfully.";
                } else {
                    $error = "Failed to update password.";
                }
            } catch (Exception $e) {
                $error = "An error occurred while updating password.";
            }
        } else {
            $error = "Passwords do not match or are empty.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="customerCSS/settings.css" type="text/css" rel="stylesheet" />
</head>
<body>

<div class="navigation-header">
    <a href="customer_dashboard.php" class="back-link">← Back to Dashboard</a>
    <h1>Account Settings</h1>
</div>

<div class="settings-container">
    <h2>Account Settings</h2>
    <?php if ($success) { echo '<div class="success">'.$success.'</div>'; } ?>
    <?php if ($error) { echo '<div class="error">'.$error.'</div>'; } ?>
    <form method="post" action="" style="margin-bottom:2em;">
        <label for="address">Address:</label><br>
        <textarea id="address" name="address" rows="3" cols="40" required><?php echo htmlspecialchars($user['address']); ?></textarea><br><br>
        <input type="submit" name="update_address" value="Update Address">
    </form>
    <form method="post" action="">
        <label for="password">New Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <label for="confirm_password">Rewrite Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="submit" name="update_password" value="Update Password">
    </form>
</div>
</body>
</html>
