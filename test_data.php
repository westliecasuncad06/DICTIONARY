<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dictionary";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert test admin if not exists
$admin_username = "admin";
$admin_email = "admin@example.com";
$admin_pass_hash = md5("admin123");

$stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $admin_username, $admin_email, $admin_pass_hash);
    if ($stmt->execute()) {
        echo "Test admin inserted: username=admin, email=admin@example.com, password=admin123<br>";
    } else {
        echo "Error inserting admin: " . $stmt->error . "<br>";
    }
} else {
    echo "Test admin already exists.<br>";
}
$stmt->close();

// Insert test user if not exists
$user_username = "testuser";
$user_email = "user@example.com";
$user_pass_hash = md5("user123");

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_username, $user_email, $user_pass_hash);
    if ($stmt->execute()) {
        echo "Test user inserted: username=testuser, email=user@example.com, password=user123<br>";
    } else {
        echo "Error inserting user: " . $stmt->error . "<br>";
    }
} else {
    echo "Test user already exists.<br>";
}
$stmt->close();

$conn->close();
echo "Test data insertion complete.";
?>
