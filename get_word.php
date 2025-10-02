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

$word = isset($_GET['word']) ? $conn->real_escape_string($_GET['word']) : '';

if (!empty($word)) {
    $sql = "SELECT word, wordtype, definition FROM english WHERE word = '$word'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Word not found']);
    }
} else {
    echo json_encode(['error' => 'No word provided']);
}

$conn->close();
?>
