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
    $email = $_POST['email'];
    $role = 'user';

    if (strlen($pass) < 6 || !preg_match('/[0-9]/', $pass) || !preg_match('/[\W_]/', $pass)) {
        $error = "Password must be at least 6 characters long and include at least one number and one symbol.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($count > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $user, $hashed_pass, $email, $role);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['signup_success'] = "Registration successful. You can now <a href='signin.php'>Sign In</a>.";
                header("Location: signin.php");
                exit();
            } else {
                $error = "Error: " . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>PetPals |Sign Up</title>
        <link rel="stylesheet" href="../css/users.css">
        <style>
            body{
                display: flex;
                background-color: #FF941D;
                height: 100%;
            }
            img{
                display: flex;
                position: absolute;
                bottom: 0;
                height: 45%;
            }
            .container {
                max-width: 400px;
                margin: 4% auto;
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
            p{
                margin-bottom: 30px;
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
            <h1>Sign Up</h1>
            <img src="../imgs/sn.png" alt="dog">
            <form method="POST" action="">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>
                <button type="submit">SIGN UP</button>
            </form>
            <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
            <p>Already have an account? <a href="signin.php">Sign In</a> here</p>
        </div>
    </body>
</html>
