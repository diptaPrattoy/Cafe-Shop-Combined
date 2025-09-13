<?php
session_start();
include 'components/connect.php';

$errors = [];
$username = "";
$password = "";
$email = "";
$Cpassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ---------------- VALIDATION ----------------
    // Username
    if (empty($_POST["username"])) {
        $errors["username"] = "Username is required";
    } else {
        $username = trim($_POST["username"]);
        if (strlen($username) < 5) {
            $errors["username"] = "Username must be at least 5 characters long";
        } elseif (!preg_match("/^[a-zA-Z0-9._-]+$/", $username)) {
            $errors["username"] = "Username can only contain letters, numbers, periods, dashes, or underscores";
        }
    }

    // Password
    if (empty($_POST["password"])) {
        $errors["password"] = "Password is required";
    } else {
        $password = $_POST["password"];
        if (strlen($password) < 8) {
            $errors["password"] = "Password must be at least 8 characters long";
        } elseif (!preg_match("/[@#$%]/", $password)) {
            $errors["password"] = "Password must contain at least one special character (@, #, $, %)";
        }
    }

    // Confirm password
    if (empty($_POST["Cpassword"])) {
        $errors["Cpassword"] = "Confirm your password";
    } else {
        $Cpassword = $_POST["Cpassword"];
        if ($password !== $Cpassword) {
            $errors["Cpassword"] = "Passwords do not match";
        }
    }

    // Email
    if (empty($_POST["email"])) {
        $errors["email"] = "Email is required";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Invalid email format";
        }
    }

    // ---------------- INSERT INTO DB IF NO ERRORS ----------------
    if (empty($errors)) {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM all_users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $errors["username"] = "Username or email already exists";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO all_users(username, password, email, type) VALUES (?, ?, ?, 'user')");
            $inserted = $stmt->execute([$username, $hashedPassword, $email]);

            if ($inserted) {
                $last_id = $conn->lastInsertId();
                $select_user = $conn->prepare("SELECT * FROM all_users WHERE id = ?");
                $select_user->execute([$last_id]);
                $row = $select_user->fetch(PDO::FETCH_ASSOC);

                $_SESSION["username"] = $row["username"];
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["type"] = $row["type"];

                header("Location: customer/customer_dashboard.php");
                exit();
            } else {
                $errors["signup"] = "Database error: Could not register user";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Cozy Cafe</title>
    <link rel="stylesheet" href="style2.css">
</head>

<body>
    <div id="main">
        <div id="left_div">
            <img src="./images/signUp.jpg" alt="Sign Up">
        </div>
        <div id="right_div">
            <form action="" method="POST">
                <div id="logo">
                    <img id="cafeicon" src="./icon/cafe.png" alt="">
                    <p id="yourcafe"><i>Cozy Cafe</i></p>
                </div>
                <h1>Register Here!</h1>

                <?php if(isset($errors["signup"])): ?>
                <span class="error"><?php echo $errors["signup"]; ?></span><br>
                <?php endif; ?>

                <div id="username">
                    <label for="username">Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                    <?php if(isset($errors["username"])): ?>
                    <span class="error"><?php echo $errors["username"]; ?></span>
                    <?php endif; ?>
                </div>

                <div id="password">
                    <label for="password">Password</label>
                    <input type="password" name="password">
                    <?php if(isset($errors["password"])): ?>
                    <span class="error"><?php echo $errors["password"]; ?></span>
                    <?php endif; ?>
                </div>

                <div id="Cpassword">
                    <label for="Cpassword">Confirm Password</label>
                    <input type="password" name="Cpassword">
                    <?php if(isset($errors["Cpassword"])): ?>
                    <span class="error"><?php echo $errors["Cpassword"]; ?></span>
                    <?php endif; ?>
                </div>

                <div id="email">
                    <label for="email">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <?php if(isset($errors["email"])): ?>
                    <span class="error"><?php echo $errors["email"]; ?></span>
                    <?php endif; ?>
                </div>

                <input type="submit" id="submit" name="submit" value="Register">
                <p id="registerLink">Already have an account? <a href="login.php">Log In</a></p>
            </form>
        </div>
    </div>
</body>

</html>