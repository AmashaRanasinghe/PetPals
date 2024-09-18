<?php
session_start();

if (isset($_GET['action']) && $_GET['action'] == 'signout') {
    session_unset(); 
    session_destroy(); 
    header('Location: index.php'); 
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petpals";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    $name = $_POST['name'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $special_conditions = $_POST['special_conditions'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];

    $sql = "INSERT INTO adopt (name, breed, age, special_conditions, description, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisss", $name, $breed, $age, $special_conditions, $description, $image_url);

    if ($stmt->execute()) {
        header("Location: admin.php?message=pet_added");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $admin_username = $_POST['admin_username'];
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password'];
    $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'administrator')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $admin_username, $admin_email, $hashed_password);

    if ($stmt->execute()) {
        header("Location: admin.php?message=admin_added");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_admin'])) {
    $user_id = $_POST['user_id'];

    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: admin.php?message=admin_deleted");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_pet'])) {
    $pet_id = $_POST['pet_id'];

    $sql = "DELETE FROM adopt WHERE pet_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pet_id);

    if ($stmt->execute()) {
        header("Location: admin.php?message=pet_deleted");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $inquiry_id = $_POST['inquiry_id'];
    $status = $_POST['status'];

    $sql = "UPDATE inquiries SET status = ? WHERE inquiry_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $inquiry_id);

    if ($stmt->execute()) {
        header("Location: admin.php?message=status_updated");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$sql_admins = "SELECT user_id, username, email FROM users WHERE role = 'administrator'";
$result_admins = $conn->query($sql_admins);

$sql_pets = "SELECT * FROM adopt";
$result_pets = $conn->query($sql_pets);

$sql_inquiries = "SELECT i.inquiry_id AS inquiry_id, i.pet_id, i.user_id, i.status, i.inquiry_date AS pet_name
                  FROM inquiries i
                  JOIN adopt a ON i.pet_id = a.pet_id";
$result_inquiries = $conn->query($sql_inquiries);

$sql = "SELECT id, name, email, phone, availability, skills, duration, message FROM volunteers";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Manage Adoptions</title>
    <style>
        body{
            background-color: #FF941D;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #FF941D;
        }
        nav{
            position: fixed;
            display: flex;
            flex-direction: row;
            justify-content:space-around;
            background-color: #FF941D;
            top: 0;
            width: 100%;
            padding-top: 1%;
            padding-bottom: 1%;
        }
        nav li{
            list-style: none;
        }
        nav li a{
            color: black;
            text-decoration: none;
            font-size: 20px;
            white-space: nowrap;
        }
        nav li a:hover{
            color:white;
        }
        nav li:last-child a{
            color: black;
            text-decoration: none;
            font-size: 20px;
            padding: 10%;
            border-radius: 50px;
            background-color: black;
            color: #FF941D;
        }
        nav li:last-child a:hover{
            background-color: white;
            color: black;
        }
        #admin{
            display: flex;
            flex-direction: column;
            margin: 2%;
            padding: 5%;
            background-color: white;
            min-height: 100vh;
            height: 100%;
        }
        .add-admin,.add-pet{
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .add-admin,.add-pet label{
            float: left;
        }
        .add-admin,.add-pet input,textarea{
            float: right;
        }
        #adopt{
            display: flex;
            flex-direction: column;
            margin: 2%;
            padding: 5%;
            background-color: white; 
            min-height: 100vh;
            height: 100%;
        }
        #inquire{
            margin: 2%;
            padding: 5%;
            background-color: white;
            min-height: 100vh;
            height: 100%;
        }
        #volunteer{
            margin: 2%;
            padding: 5%;
            background-color: white;
            min-height: 100vh;
            height: 100%;
        }
    </style>
</head>
<body>
    <nav>
        <li><a href="#admin">Manage Admin</a></li>
        <li><a href="#adopt">Manage Adoptions</a></li>
        <li><a href="#inquire">Manage Inquiries</a></li>
        <li><a href="#volunteer">Volunteers</a></li>
        <li><a href="?action=signout">SignOut</a></li>
    </nav>
    <div id="admin">
        <div class="add-admin">
            <h1>Add New Administrator</h1>
            <form method="POST" action="admin.php">
                <label for="admin_username">Username:</label>
                <input type="text" id="admin_username" name="admin_username" required><br><br>
                <label for="admin_email">Email:</label>
                <input type="email" id="admin_email" name="admin_email" required><br><br>
                <label for="admin_password">Password:</label>
                <input type="password" id="admin_password" name="admin_password" required><br><br>
                <input type="submit" name="add_admin" value="Add Administrator">
            </form>
        </div>

        <div class="display-admins">
            <h1>Administrators</h1>
            <?php
            if ($result_admins->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Username</th><th>Email</th><th>Action</th></tr>";
                while ($row = $result_admins->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>
                            <form method='POST' action='admin.php'>
                                <input type='hidden' name='user_id' value='" . htmlspecialchars($row['user_id']) . "'>
                                <input type='submit' name='delete_admin' value='Delete'>
                            </form>
                        </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No administrators found.</p>";
            }
            ?>
        </div>
    </div>
    
    <div id="adopt">
        <div class="add-pet">
            <h1>Add New Pet for Adoption</h1>
            <form method="POST" action="admin.php">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="breed">Breed:</label>
                <input type="text" id="breed" name="breed" required><br><br>
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" required><br><br>
                <label for="special_conditions">Special Conditions:</label>
                <input type="text" id="special_conditions" name="special_conditions"><br><br>
                <label for="description">Description:</label>
                <textarea id="description" name="description"></textarea><br><br>
                <label for="image_url">Image URL:</label>
                <input type="text" id="image_url" name="image_url"><br><br>
                <input type="submit" name="add_pet" value="Add Pet">
            </form>
        </div>

        <div class="display-pets">
            <h1>Pets</h1>
            <?php
            if ($result_pets->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Name</th><th>Breed</th><th>Age</th><th>Special Conditions</th><th>Description</th><th>Image URL</th><th>Action</th></tr>";
                while ($row = $result_pets->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['breed']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['special_conditions']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['image_url']) . "</td>";
                    echo "<td>
                            <form method='POST' action='admin.php'>
                                <input type='hidden' name='pet_id' value='" . htmlspecialchars($row['pet_id']) . "'>
                                <input type='submit' name='delete_pet' value='Delete'>
                            </form>
                        </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No pets found.</p>";
            }
            ?>
        </div>
    </div>

    <div id="inquire">
        <h1>Adoption Inquiries</h1>
        <?php
        if ($result_inquiries->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Pet ID</th><th>User ID</th><th>Status</th><th>Date</th><th>Action</th></tr>";
            while ($row = $result_inquiries->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['pet_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['pet_name']) . "</td>";
                echo "<td>
                        <form method='POST' action='admin.php'>
                            <input type='hidden' name='inquiry_id' value='" . htmlspecialchars($row['inquiry_id']) . "'>
                            <select name='status'>
                                <option value='pending'>Pending</option>
                                <option value='approved'>Approved</option>
                                <option value='rejected'>Rejected</option>
                            </select>
                            <input type='submit' name='update_status' value='Update Status'>
                        </form>
                      </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No inquiries found.</p>";
        }
        ?>
    </div>
    <div id="volunteer">
        <h1>Volunteers</h1>
        <?php
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Availability</th><th>Skills</th><th>Duration</th><th>Message</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
                echo "<td>" . $row["phone"] . "</td>";
                echo "<td>" . $row["availability"] . "</td>";
                echo "<td>" . $row["skills"] . "</td>";
                echo "<td>" . $row["duration"] . "</td>";
                echo "<td>" . $row["message"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No volunteers found.";
        }
        ?>
    </div>
</body>
</html>
