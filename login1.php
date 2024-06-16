<?php
session_start();
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mobile"]) && isset($_POST["password"])) {
    //  Connect to the database
    $servername = "localhost"; // Change this to  database server
    $username = "root"; // Change this to  database username
    $password = ""; // Change this to  database password
    $dbname = "systemvoting"; // Change this to database name

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    function sanitize_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $mobile = sanitize_input($_POST['mobile']);
    $password = sanitize_input($_POST['password']);

    //  Check if the entered username exists in the database
    $sql = "SELECT * FROM users WHERE mobile = '$mobile'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // If the mobile exists, retrieve the corresponding password
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        // Compare the entered password with the password retrieved from the database
        if (password_verify($password, $stored_password)) {
            // Passwords match, login successful
            $_SESSION['userdata'] = array(
                'id' => $row['id'], // Assuming there's an id column
                'name' => $row['name'],
                'mobile' => $row['mobile'],
                'address' => $row['address'],
                'status' => $row['status'],
                'role' => $row['role']
            );

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Passwords don't match, display error message
            $error_message = "Invalid password or mobile number";
        }
    } else {
        // Username doesn't exist, display error message
        $error_message = "Invalid password or mobile number";
    }

    // Close the database connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div id="headers">
        <h1>Online Voting System</h1>
    </div>
    <hr>
    <div id="bodys">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Login</h2>
            <?php
            if (!empty($error_message)) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            ?>
            <input type="number" name="mobile" placeholder="Enter mobile" required><br><br>
            <input type="password" name="password" placeholder="Enter password" required><br><br>
            <select name="role">
                <option value="1">Voter</option>
                <option value="2">Group</option>
            </select><br><br>
            <button type="submit">Login</button><br><br>
            <p>New user? <a href="register1.php">Register here</a></p>
        </form>
    </div>
</body>
</html>
