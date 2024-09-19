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

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$user_query = "SELECT username, email FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $user_query);

if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
    $username = htmlspecialchars($user_data['username']);
    $email = htmlspecialchars($user_data['email']);
} else {
    $username = "User";
    $email = "Email not available";
}

$greeting = "Hello " . $username . "!";
$password_change_message = '';
$username_change_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    if (strlen($new_password) < 6 || !preg_match('/[0-9]/', $new_password) || !preg_match('/[\W_]/', $new_password)) {
        $password_change_message = "Password must be at least 6 characters long, include at least one number and one symbol.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $update_query = "UPDATE users SET password = '$hashed_password' WHERE user_id = '$user_id'";
        if (mysqli_query($conn, $update_query)) {
            $password_change_message = "Password changed successfully.";
        } else {
            $password_change_message = "Error changing password: " . mysqli_error($conn);
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_username'])) {
    $new_username = $_POST['new_username'];

    $update_query = "UPDATE users SET username = '$new_username' WHERE user_id = '$user_id'";
    if (mysqli_query($conn, $update_query)) {
        $username_change_message = "Username updated successfully.";
        $username = $new_username;
    } else {
        $username_change_message = "Error updating username: " . mysqli_error($conn);
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'signout') {
    session_unset(); 
    session_destroy();
    header('Location: index.php'); 
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>PetPals| User Profile</title>
        <style>
        body{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: white;
        }
        .container{
            padding: 2%;
            background-color: #FF941D;
            max-height: 80%;
            width: 60%;
        }
        .content{
            text-align: center;
        }
        .header{
            display: flex;
            flex-direction: row;
            font-size: 24px;
            justify-content: center;
            font-weight: bold;
            font-family: cursive;
            width: 100%;
        }
        .link{
            display: flex;
            flex-direction: row;
            width: 100%;
            justify-content: space-around;
        }
        .link a{
            background-color: black;
            color:#FF941D;
            border-radius: 50px;
            padding: 10px;
            text-decoration: none;
        }
        .link a:hover{
            background-color: white;
            color:black ;
        }
        .top{
            position: absolute;
            top: 0;
        }
        .bottom{
            position: absolute;
            bottom: 2.2%;
        }
        </style>
        
        <script>
            <?php if (!empty($password_change_message)): ?>
                alert('<?php echo htmlspecialchars($password_change_message); ?>');
            <?php endif; ?>
            <?php if (!empty($username_change_message)): ?>
                alert('<?php echo htmlspecialchars($username_change_message); ?>');
            <?php endif; ?>
        </script>
    </head>
    <body>
        <div class="top">
          <img src="../imgs/top.png">
        </div>
        <div class="container">
            <div class="header">
                <p><?php echo htmlspecialchars($greeting); ?></p>
            </div>
            <div class="content">
                <div class="profile-details">
                    <h2>Profile Details</h2>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                </div>

                <div class="username-change">
                    <h2>Change Username</h2>
                    <form method="POST" action="">
                        <label for="new_username">New Username:</label>
                        <input type="text" id="new_username" name="new_username" required>
                        <button type="submit" name="change_username">Change Username</button>
                    </form>
                </div>

                <div class="password-change">
                    <h2>Change Password</h2>
                    <form method="POST" action="">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <button type="submit" name="change_password">Change Password</button>
                    </form>
                </div>
                <br><br><br>
                <div class="link">
                        <a href="index.php">Back</a>
                        <a href="?action=signout">SignOut</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom">
            <img src="../imgs/bottom.png">
        </div>

    </body>
</html>
