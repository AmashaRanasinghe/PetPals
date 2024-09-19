<?php
    session_start();
    
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $availability = $_POST['availability'];
    $skills = $_POST['skills'];
    $duration = $_POST['duration'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO volunteers (name, email, phone, availability, skills, duration, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $phone, $availability, $skills, $duration, $message);

    if ($stmt->execute()) {
        echo "Thank you for volunteering! Further information will be notified through an email.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>PetPals | Volunteering</title>
    <style>
        .container {
            display: flex;
            height: 100vh; 
        }
        .left {
            flex: 1;
            background-color: #FF941D; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }
        .right {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            flex-wrap: wrap;
            max-height: 90vh;
        }
        .form-container {
            width: 80%;
            padding: 20px;
            border-radius: 50px; 
            background-color: #fca534cb;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); 
        }
        input[type="text"], input[type="email"], textarea {
            border-radius: 15px;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background-color: #a36603;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 20px;
            cursor: pointer;
            float: right;
        }
        button:hover {
            background-color: #794602;
        }
        h2 {
            font-size: 35px;
            margin-bottom: 50px;
        }
        p {
            font-size: 25px;
            margin-bottom: 50px;
        }
        body {
            margin: auto;
            font-family: Arial, sans-serif;
        }
        img {
            width: 300px;
            height: 300px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="left">
            <img src="../imgs/logoo.jpg" alt="Pet Pals Logo">
            <h2>Volunteer for Our Pet Rescue Program</h2>
            <p>We are always looking for passionate individuals<br> to help us with our pet rescue efforts.<br>
                Please fill out the form below if you're interested in volunteering.</p>
        </div>
        <div class="right">
            <div class="form-container">
                <form action="volunteering.php" method="POST">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                    <br><br>

                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                    <br><br>

                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" required>
                    <br><br>

                    <label for="availability">Availability (e.g., weekdays, weekends)</label>
                    <input type="text" id="availability" name="availability">
                    <br><br>

                    <label for="skills">Do you have any relevant skills?</label>
                    <textarea id="skills" name="skills" placeholder="EX: knowledge of animal behavior, handling scared or aggressive animals, feeding and cleaning, etc."></textarea>
                    <br><br>

                    <label for="duration">Volunteering Period</label>
                    <input type="text" id="duration" name="duration">
                    <br><br>

                    <label for="message">Additional Information</label>
                    <textarea id="message" name="message"></textarea>
                    <br>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
