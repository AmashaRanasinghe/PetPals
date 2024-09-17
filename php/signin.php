<?php
session_start(); 

$servername = "localhost";
$username = "root";
$password = "";
$database = "petpals";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT user_id, password, role FROM users WHERE username = ?");
    if ($stmt === false) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $user_id, $hashed_pass, $role);
        mysqli_stmt_fetch($stmt);

        if (password_verify($pass, $hashed_pass)) {
            $_SESSION['username'] = $user;
            $_SESSION['role'] = $role; 
            $_SESSION['user_id'] = $user_id; 

            if ($role === 'administrator') {
                header('Location: admin.php'); 
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>PetPals |SignIn</title>
    <style>
            body{
                display: flex;
                background-color: #FF941D;
                height: 100%;
            }
            img{
                display: flex;
                position: absolute;
                height: 45%;
                width: auto;
                bottom: 0;
            }
            .container {
                max-width: 400px;
                margin: 6% auto;
                padding: 10px;
                border: 3px solid black;
                border-radius: 8px;
                background-color: white;
                display: flex;
                flex-direction: column;
                justify-content: center;
                text-align: center;
                max-height: 50%;
            }
            form {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            form label,
            form input,
            form button {
                margin-bottom: 10px;
            }
            form button{
                background-color: #FF941D;
                color: black;
                padding: 2%;
                size: 30px;
                width: 30%;
                font-weight: bold;
            }
            .password-requirements {
                color: #555;
                font-size:14px;
            }
            .error {
                color:black;
                font-style: italic;
                font-size:14px;
            }
            h1{
                font-style: oblique;
            }
        </style>
</head>
<body>
    <div class="container">
        <h1>Sign In</h1>
        <img src="../imgs/su.png" alt="a happy dog">
        <?php
        if (isset($_SESSION['signup_success'])) {
            echo "<p style='color: green;'>" . $_SESSION['signup_success'] . "</p>";
            unset($_SESSION['signup_success']);
        }
        ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">SIGN IN</button>
        </form>
        <?php if (!empty($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <p>Don't have an account? <a href="signup.php">Sign up</a> here.</p>
    </div>
</body>
</html>