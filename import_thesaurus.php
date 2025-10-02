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

// Truncate the table to clear existing data
$conn->query("TRUNCATE TABLE english");

// Open the thesaurus file
$file = fopen("thesaurus-master/en_thesaurus.jsonl", "r");
if (!$file) {
    die("Unable to open file");
}

$words = [];
while (($line = fgets($file)) !== false) {
    $data = json_decode(trim($line), true);
    if ($data) {
        $word = $data['word'];
        if (!isset($words[$word])) {
            $words[$word] = [
                'wordtype' => $data['pos'],
                'definition' => implode('; ', $data['desc']),
                'synonyms' => [],
                'antonyms' => []
            ];
        }
        $words[$word]['synonyms'] = array_unique(array_merge($words[$word]['synonyms'], $data['synonyms']));
        if (isset($data['antonyms'])) {
            $words[$word]['antonyms'] = array_unique(array_merge($words[$word]['antonyms'], $data['antonyms']));
        }
    }
}

fclose($file);

// Prepare the insert statement
$stmt = $conn->prepare("INSERT INTO english (word, wordtype, definition, synonyms, antonyms) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$count = 0;
foreach ($words as $word => $data) {
    $wordtype = $data['wordtype'];
    $definition = $data['definition'];
    $synonyms = implode(', ', $data['synonyms']);
    $antonyms = implode(', ', $data['antonyms']);

    $stmt->bind_param("sssss", $word, $wordtype, $definition, $synonyms, $antonyms);
    if (!$stmt->execute()) {
        echo "Error inserting: " . $stmt->error . " for word: $word\n";
    }
    $count++;
    if ($count % 10000 == 0) {
        echo "Inserted $count records\n";
    }
}

$stmt->close();
$conn->close();

echo "Database updated successfully. Total inserted: $count";
?>
