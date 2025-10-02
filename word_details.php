<?php
// word_details.php
// Returns JSON details for a given word

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dictionary";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$word = isset($_GET['word']) ? $conn->real_escape_string($_GET['word']) : '';

if (empty($word)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing word parameter']);
    exit();
}

$sql = "SELECT word, wordtype, definition, synonyms, antonyms FROM english WHERE word = '$word' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Word not found']);
}

$conn->close();
?>
