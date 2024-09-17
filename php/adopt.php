<?php
session_start();

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset(); 
    session_destroy(); 
    header('Location: index.php'); 
    exit();
}

$logged_in = false;
$role = '';
$user_id = $_SESSION['user_id'];

if (isset($_SESSION['username'])) {
    $logged_in = true;
    $role = $_SESSION['role']; 
} else {
    header('Location: signin.php'); 
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petpals";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['inquire'])) {
    $pet_id = $_POST['pet_id'];
    $sql = "INSERT INTO inquiries (user_id, pet_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $pet_id);

    if ($stmt->execute()) {
        echo "Adoption inquiry sent!";
    } else {
        echo "Error submitting inquiry: " . $stmt->error;
    }
    $stmt->close();
}

$sql = "SELECT a.*, i.status AS inquiry_status 
        FROM adopt a
        LEFT JOIN inquiries i ON a.pet_id = i.pet_id AND i.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>PetPals | Adopt</title>
    <style>
    body{
        background-color: #FF941D;
    }
    nav{
        display: flex;
        justify-content: space-between;
        padding: 5%;
    }
    nav li{
        list-style: none;
    }
    nav li a{
        text-decoration: none;
        font-size: 20px;
        color: #FF941D;
        background-color: black;
        padding: 10px;
        border-radius: 50px;
    }
    nav li a:hover{
        background-color: white;
        color: black;
    }
    h1{
        font-family: cursive;
        text-align: center;
    }
    .container{
        background-color: white;
        display: flex;
        flex-direction: column;
        background-color: #FF941D;
    }
    .description{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        border: 5px solid black;
        margin-bottom: 5%;
        background-color: white;
        padding:2% ;
    }

    .details{
        display: flex;
        flex-direction: column;
        max-width: 50%;
    }
    </style>
</head>
<body>
    <nav>
        <li><a href="index.php">Home</a></li>
        <li><a href="?action=signout">SignOut</a></li>
    </nav>
    <div class="container">
        <h1>ADOPT</h1>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='description'>";
                echo "<div class='image'><img src='" . htmlspecialchars($row['image_url']) . "' alt='Pet Image' style='width:300px;height:200px;'></div>";
                echo "<div class='details'>";
                echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
                echo "<p><strong>Breed:</strong> " . htmlspecialchars($row['breed']) . "</p>";
                echo "<p><strong>Age:</strong> " . htmlspecialchars($row['age']) . "</p>";
                echo "<p><strong>Special Conditions:</strong> " . htmlspecialchars($row['special_conditions']) . "</p>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='pet_id' value='" . $row['pet_id'] . "'>";
                echo "<input type='submit' name='inquire' value='Adopt'>";
                echo "</form>";
                if (isset($row['inquiry_status'])) {
                    echo "<p><strong>Status:</strong> " . htmlspecialchars($row['inquiry_status']) . "</p>";
                } else {
                    echo "<p><strong>Status:</strong> No inquiry made yet</p>";
                }
                echo "</div></div>";
            }
        } else {
            echo "<p>No pets available for adoption.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
