<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System - Registration</title>
    <link rel="stylesheet" href="register.css"> 
</head>
<body>
    <div id="headers">
        <h1>Online Voting System</h1>
        <hr>
        
    </div>
    <div id="bodys">
        <h2>Registration</h2>
        <form  method="POST">
            <input type="text" name="name" placeholder="Enter name" required>
            <input type="number" name="mobile" placeholder="Enter mobile" required><br><br>
            <input type="password" name="password" placeholder="password" required>
            <input type="password" name="Cpassword" placeholder="Confirm password" required><br><br>
            <input type="text" name="address" placeholder="Address" required><br><br>
           
           
           
            <label class="select1">Select your role:</label>
            <select name="role" class="select1" >
             <option value="1">Voter</option>
             <option value="2">Group</option>
            </select><br><br>
           
            <button>Register</button><br><br>
           Already a user?<a href="login1.php">Login here</a> <!-- corrected the text "Alrady user?" to "Already a user?" -->
         </form>
         </div>
         <?php
                if(isset($_POST["name"]) && isset($_POST["mobile"]) && isset($_POST["password"]) && isset($_POST["address"]) && isset($_POST["role"])){
                // Connection variables
                $host = 'localhost'; 
                $username = 'root';
                $password = '';
                $database = 'systemvoting';

                // Create connection
                $conn = new mysqli($host, $username, $password, $database);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                // Function to sanitize user input
                function sanitize_input($data) {
                    return htmlspecialchars(stripslashes(trim($data)));
                }

                // Check if form is submitted
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $name = sanitize_input($_POST["name"]);
                    $mobile = sanitize_input($_POST["mobile"]);
                    $password = sanitize_input($_POST["password"]);
                    $address = sanitize_input($_POST["address"]);
                    $role = sanitize_input($_POST["role"]);

                    // Check if mobile number already exists
                    $stmt = $conn->prepare("SELECT id FROM users WHERE mobile = ?");
                    $stmt->bind_param("s", $mobile);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows > 0) {
                        echo "<center><p style='color: red;'>This mobile number is already registered</p></center>";
                    } else {
                        // Hash the password before storing it
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        // Insert new user
                        $stmt = $conn->prepare("INSERT INTO users (name, mobile, password, address, role) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssss", $name, $mobile, $hashed_password, $address, $role);

                        if ($stmt->execute()) {
                            echo "<script>window.location.href = 'login1.php';</script>";
                        } else {
                            echo "Error: " . $stmt->error;
                        }
                    }

                    $stmt->close();
                }

                $conn->close();
                }
?>





</body>
</html>
