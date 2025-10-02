<?php
// Database setup script for Dictionary System

$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP password
$dbname = "dictionary";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db($dbname);

// Create english table
$sql = "DROP TABLE IF EXISTS english";
$conn->query($sql);
$sql = "CREATE TABLE english (
    id INT AUTO_INCREMENT PRIMARY KEY,
    word VARCHAR(255) NOT NULL,
    wordtype VARCHAR(50),
    definition TEXT,
    synonyms TEXT,
    antonyms TEXT,
    UNIQUE KEY unique_word (word)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'english' created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}



// Import data from oedict.sql, parsing meaning into wordtype and definition
$oedict_sql = file_get_contents('oedict.sql');
$lines = explode("\n", $oedict_sql);
$inserts = [];
foreach ($lines as $line) {
    if (preg_match("/INSERT INTO oedict \(word, meaning\) VALUES \('([^']*)', '([^']*)'\);/", $line, $matches)) {
        $word = $conn->real_escape_string($matches[1]);
        $meaning = $matches[2];
        // Parse wordtype and definition
        if (preg_match('/^([a-z]+\.\s?)(.*)$/', $meaning, $def_matches)) {
            $wordtype = $conn->real_escape_string(trim($def_matches[1]));
            $definition = $conn->real_escape_string(trim($def_matches[2]));
        } else {
            $wordtype = '';
            $definition = $conn->real_escape_string($meaning);
        }
        $inserts[] = "('$word', '$wordtype', '$definition')";
    }
}
if (!empty($inserts)) {
    $insert_sql = "INSERT IGNORE INTO english (word, wordtype, definition) VALUES " . implode(', ', $inserts);
    if ($conn->query($insert_sql) === TRUE) {
        echo "Data imported successfully<br>";
    } else {
        echo "Error importing data: " . $conn->error . "<br>";
    }
}

$conn->close();
echo "Database setup complete.";
?>
