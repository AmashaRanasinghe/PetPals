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
    }//restricting access if the user is not logged in

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "petpals";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $age_filter = isset($_GET['age_filter']) ? $_GET['age_filter'] : '';

    $sql = "SELECT a.*, i.status AS inquiry_status 
            FROM adopt a
            LEFT JOIN inquiries i ON a.pet_id = i.pet_id AND i.user_id = ?";

    switch ($age_filter) {
        case 'below_6_months':
            $sql .= " WHERE a.age < 0.5"; 
            break;
        case 'below_12_months':
            $sql .= " WHERE a.age < 1";
            break;
        case 'below_5_years':
            $sql .= " WHERE a.age >= 1 AND a.age < 5"; 
            break;
        case 'above_5_years':
            $sql .= " WHERE a.age >= 5";
            break;
        default:
            break;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    if (isset($_POST['inquire'])) {
        $pet_id = $_POST['pet_id'];
        $status = 'pending';  
        
        $sql = "INSERT INTO inquiries (user_id, pet_id, status) VALUES (?, ?, ?)";
        $stmt_inquire = $conn->prepare($sql);
        $stmt_inquire->bind_param("iis", $user_id, $pet_id, $status);

        if ($stmt_inquire->execute()) {
            echo "Adoption inquiry sent! Status: Pending.";
        } else {
            echo "Error submitting inquiry: " . $stmt_inquire->error;
        }
        $stmt_inquire->close();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>PetPals | Adopt</title>
    <link rel="stylesheet" href="../css/adopt.css"> 
</head>
<body>
    <nav>
        <li><a href="index.php">Home</a></li>
        <li><a href="?action=logout">SignOut</a></li>
    </nav>
    <div class="container">
        <h1>ADOPT</h1>
        <div class="filter-container">
            <form method="GET" action="">
                <label for="age_filter">Filter by Age:</label>
                <select id="age_filter" name="age_filter">
                    <option value="">Select Age Range</option>
                    <option value="below_6_months" <?php echo $age_filter == 'below_6_months' ? 'selected' : ''; ?>>Below 6 months</option>
                    <option value="below_12_months" <?php echo $age_filter == 'below_12_months' ? 'selected' : ''; ?>>Below 12 months</option>
                    <option value="below_5_years" <?php echo $age_filter == 'below_5_years' ? 'selected' : ''; ?>>Below 5 years</option>
                    <option value="above_5_years" <?php echo $age_filter == 'above_5_years' ? 'selected' : ''; ?>>Above 5 years</option>
                </select>
                <input type="submit" value="Filter">
            </form>
        </div>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='description'>";
                echo "<div class='image'><img src='" . htmlspecialchars($row['image_url']) . "' alt='Pet Image' style='width:300px;height:200px;'></div>";
                echo "<div class='details'>";
                echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
                echo "<p><strong>Breed:</strong> " . htmlspecialchars($row['breed']) . "</p>";
                echo "<p><strong>Age:</strong> " . htmlspecialchars($row['age']) . " years</p>";
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
