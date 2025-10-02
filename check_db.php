<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dictionary";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Tables in database:<br>";
    while($row = $result->fetch_assoc()) {
        echo $row["Tables_in_dictionary"] . "<br>";
    }
} else {
    echo "No tables found.";
}

$conn->close();
?>
