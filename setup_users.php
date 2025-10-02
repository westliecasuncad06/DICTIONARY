<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dictionary";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Execute users.sql with INSERT IGNORE to avoid duplicate errors
$sql = file_get_contents('users.sql');
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (!empty($statement)) {
        // Replace INSERT INTO with INSERT IGNORE INTO
        $statement = preg_replace('/INSERT INTO/', 'INSERT IGNORE INTO', $statement, 1);
        if ($conn->query($statement) === TRUE) {
            echo "Executed: " . substr($statement, 0, 50) . "...<br>";
        } else {
            echo "Error: " . $conn->error . "<br>";
        }
    }
}

$conn->close();

echo "Setup complete.";
?>
