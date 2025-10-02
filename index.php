<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dictionary";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $conn->real_escape_string($_POST['username']);
    $input_password = $_POST['password']; // Assuming passwords are stored hashed, but no info given

    // Query to check user credentials in admin table first
    $sql = "SELECT username, password FROM admin WHERE username = '$input_username' OR email = '$input_username' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (md5($input_password) === $row['password']) {
            // Set session variables
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = 'admin';

            // Redirect to admin
            header("Location: admin.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        // Check user table
        $sql = "SELECT username, password FROM user WHERE username = '$input_username' OR email = '$input_username' LIMIT 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (md5($input_password) === $row['password']) {
                // Set session variables
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = 'user';

                // Redirect to user
                header("Location: user.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reciprocity Dictionary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #EEEEEE;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #FFF0CE;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(51, 150, 211, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            color: #3396D3;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .form-control {
            border-radius: 50px;
            padding: 15px 20px;
            font-size: 1.1rem;
            box-shadow: 0 10px 30px rgba(51, 150, 211, 0.3);
            border: none;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            box-shadow: 0 15px 40px rgba(51, 150, 211, 0.5);
            outline: none;
        }
        .btn-login {
            background: #3396D3;
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        .btn-login:hover {
            background: #EBCB90;
            color: #3396D3;
        }
        .error-message {
            color: #dc3545;
            margin-top: 15px;
            font-weight: 600;
        }
        @media (max-width: 576px) {
            .login-container {
                padding: 30px;
            }
            h1 {
                font-size: 2rem;
                margin-bottom: 20px;
            }
            .form-control {
                padding: 12px 18px;
                font-size: 1rem;
            }
            .btn-login {
                padding: 12px 25px;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Reciprocity Dictionary</h1>
        <form method="POST" action="index.php" novalidate>
            <input type="text" name="username" class="form-control mb-3" placeholder="Username or Email" required autofocus />
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required />
            <button type="submit" class="btn-login">Login</button>
        </form>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div class="mt-3">
            <p>Don't have an account? <a href="register.php" style="color: #3396D3; text-decoration: none;">Register here</a></p>
        </div>
    </div>
</body>
</html>
