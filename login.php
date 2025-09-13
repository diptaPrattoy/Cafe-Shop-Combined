<?php
session_start();
include 'components/connect.php'; // this should contain your PDO connection ($conn)

$errors = [];
$username = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $errors["username"] = "Username is required";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $errors["password"] = "Password is required";
    } else {
        $password = $_POST["password"];
    }

    if (empty($errors)) {
        // ✅ PDO query
        $stmt = $conn->prepare("SELECT id, username, password, type FROM all_users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

<<<<<<< HEAD
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // ✅ verify hashed password
            if (password_verify($password, $row["password"])) {
=======
        if ($row) {
            // ✅ verify password (use hashing in production)
            if (password_verify($password, $row["password"])|| $password === $row["password"] ) {
>>>>>>> 1b6bd1e30e3f5b1f6d285e08debe2589a4e7e072
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["type"] = $row["type"];

                // ✅ redirect by type
                if ($row["type"] === "admin") {
                    header("Location: admin/dashboard.php");
                } elseif ($row["type"] === "employee") {
                    header("Location: employee_dashboard.php");
                } elseif ($row["type"] === "user") {
                    header("Location: customer/customer_dashboard.php");
                } else {
                    header("Location: home.php");
                }
                exit();
            } else {
                $errors["login"] = "Invalid username or password";
            }
        } else {
            $errors["login"] = "Invalid username or password";
        }
    }
}
?>



<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div id="main">
        <!-- <legend>Log In</legend> -->

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div id="logo"> <img id="cafeicon" src="./icon/cafe.png" alt="">
                <p id="yourcafe"><i>Cozy Cafe</i></p>
            </div>
            <h1>Log In</h1>

            <div id="username">
                <label for="username" name="username">Username</label>
                <input type="text" name="username" value="<?php echo $username;?>">
                <?php if(isset($errors["username"])): ?> <span class="error"><?php echo $errors["username"]; ?></span>
                <?php endif; ?>

            </div>
            <div id="password">
                <label for="password" name="password">Password</label>
                <input type="password" name="password">
                <?php if (isset($errors["password"])): ?>
                <span class="error"><?php echo $errors["password"]; ?></span>
                <?php endif; ?>

            </div>
            <input type="submit" id="submit" name="submit" value="Log In">
            <p id="registerLink">Dont haves an account? <a href="signup.php ">Register</a> </p>
        </form>
    </div>
</body>

</html>