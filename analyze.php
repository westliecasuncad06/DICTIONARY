<?php
// Script to analyze dictionary definitions and extract synonyms and antonyms

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dictionary";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to extract related words from definition
function extract_related($definition, $type) {
    $related = [];
    $patterns = [
        'synonyms' => '/synonym(?:s)? of ([^;.,]+)/i',
        'antonyms' => '/antonym(?:s)? of ([^;.,]+)/i',
        'opposite' => '/opposite (?:of )?([^;.,]+)/i',
        'see also' => '/see also ([^;.,]+)/i',
        'compare' => '/compare ([^;.,]+)/i'
    ];

    if (isset($patterns[$type])) {
        preg_match_all($patterns[$type], $definition, $matches);
        foreach ($matches[1] as $match) {
            $words = explode(',', $match);
            foreach ($words as $word) {
                $word = trim($word);
                if (!empty($word)) {
                    $related[] = $word;
                }
            }
        }
    }

    return array_unique($related);
}

// Select all words
$sql = "SELECT word, definition FROM english";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $word = $row['word'];
        $definition = $row['definition'];

        $synonyms = extract_related($definition, 'synonyms');
        $synonyms = array_merge($synonyms, extract_related($definition, 'see also'));
        $synonyms = array_merge($synonyms, extract_related($definition, 'compare'));

        $antonyms = extract_related($definition, 'antonyms');
        $antonyms = array_merge($antonyms, extract_related($definition, 'opposite'));

        // Update the database
        $syn_str = implode(', ', $synonyms);
        $ant_str = implode(', ', $antonyms);

        $update_sql = "UPDATE english SET synonyms = ?, antonyms = ? WHERE word = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sss", $syn_str, $ant_str, $word);
        $stmt->execute();
        $stmt->close();

        echo "Updated $word: Synonyms - $syn_str, Antonyms - $ant_str<br>";
    }
} else {
    echo "No words found.";
}

$conn->close();
echo "Analysis complete.";
?>
