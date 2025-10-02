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

$query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if (!empty($query)) {
    $sql = "SELECT word FROM english WHERE word LIKE '$query%' LIMIT 10";
    $result = $conn->query($sql);

    $suggestions = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $suggestions[] = $row['word'];
        }
    }

    echo json_encode($suggestions);
} else {
    echo json_encode([]);
}

$conn->close();
?>
