<?php
session_start();
// Redirect to login page if the user is not logged in
if (!isset($_SESSION['userdata'])) {
    header("Location: login1.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "systemvoting";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If the user has voted, update the vote count and user's status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["vote"])) {
    $group_id = intval($_POST["vote"]);
    $user_id = $_SESSION['userdata']['id'];

    // Update vote count for the selected group
    $sql = "UPDATE users SET votes = votes + 1 WHERE id = $group_id";
    $conn->query($sql);

    // Update user's status to '1' (voted)
    $sql = "UPDATE users SET status = 1 WHERE id = $user_id";
    $conn->query($sql);

    // Update session data
    $_SESSION['userdata']['status'] = 1;
}

// Fetch group data (users with role = 2)
$sql = "SELECT id, name, votes FROM users WHERE role = 2";
$result = $conn->query($sql);

$groups = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $groups[] = $row;
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System - Dashboard</title>
    <link rel="stylesheet" href="dashboard.css"> 
</head>
<body>
    <div id="mainSection">
        <div id="headerSection">
            <button id="logoutbtn" onclick="logoutAndClose()">Logout <i class="bi bi-box-arrow-right"></i></button>
            <h1>Online Voting System</h1>
        </div>
        <hr>
        <div class="container">
        <div id="Profile">
            <h2>Welcome, <?php echo $_SESSION['userdata']['name']; ?>!</h2>
            <?php
            // Check if $_SESSION['userdata'] is set before accessing its elements
            if (isset($_SESSION['userdata'])) {
                echo "<b>Name:</b> " . $_SESSION['userdata']['name'] . "<br><br>";
                echo "<b>Mobile:</b> " . $_SESSION['userdata']['mobile'] . "<br><br>";
                echo "<b>Address:</b> " . $_SESSION['userdata']['address'] . "<br><br>";
                if ($_SESSION['userdata']['role'] == 1) {
                    echo "<b>Status:</b> " . ($_SESSION['userdata']['status'] == 1 ? "Voted" : "Not Voted") . "<br><br>";
                } else {
                    echo "<b>Status:</b> Group<br><br>";
                }
            } else {
                echo "<p>User data not found. Please log in.</p>";
            }
            ?>
        </div>
        <?php if ($_SESSION['userdata']['role'] == 1) { ?>
        <div id="Group">
            <h2>Groups</h2>
            <form method="post" action="">
                <?php
                if (!empty($groups)) {
                    foreach ($groups as $group) {
                        echo "<div class='group'>";
                        echo "<span class='group-name'>$group[name]</span>";
                        echo "<span class='group-votes'>Votes: $group[votes]</span>";
                        echo "<button type='submit' name='vote' value='$group[id]' " . ($_SESSION['userdata']['status'] == 1 ? "disabled" : "") . ">Vote</button>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No groups found.</p>";
                }
                ?>
            </form>
        </div>
        <?php } else { ?>
        <div id="Group">
            <h2>Groups to Vote</h2>
            <p><?php echo $_SESSION['userdata']['name']; ?></p>
            <p>Votes: <?php echo $groups[0]['votes']; ?></p>
        </div>
        <?php } ?>
        </div>
    </div>

    <script>
        function logoutAndClose() {
            fetch('logout.php', {
                method: 'POST'
            }).then(response => {
                if (response.ok) {
                    window.location.href = 'login1.php';
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
    <style>
        .group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            
        }
        .group-name {
            flex: 1;
        }
        .group-votes {
            flex: 1;
            text-align: center;
        }
        button {
            flex: 1;
            background-color:#adff2f;
    
        }
    </style>
</body>
</html>
