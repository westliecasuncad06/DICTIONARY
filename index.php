<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>English Dictionary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #EEEEEE;
            font-family: 'Inter', sans-serif;
            padding: 50px;
        }
        .container {
            background: #FFF0CE;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(51, 150, 211, 0.3);
            text-align: center;
        }
        h1 {
            color: #3396D3;
            margin-bottom: 30px;
        }
        .btn-custom {
            background: #3396D3;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-custom:hover {
            background: #EBCB90;
            color: #3396D3;
            text-decoration: none;
        }
        @media (max-width: 576px) {
            body {
                padding: 20px;
            }
            .container {
                padding: 30px;
            }
            h1 {
                font-size: 2rem;
                margin-bottom: 20px;
            }
            .btn-custom {
                padding: 8px 16px;
                margin: 5px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to English Dictionary</h1>
        <p>Explore words, definitions, and more.</p>
        <a href="search.php" class="btn-custom">Search Words</a>
        <a href="all_words.php" class="btn-custom">All Words</a>
        <a href="analyze.php" class="btn-custom">Analyze Text</a>
        <a href="suggestions.php" class="btn-custom">Suggestions</a>
        <a href="user.php" class="btn-custom">User Page</a>
        <a href="admin.php" class="btn-custom">Admin Page</a>
    </div>
</body>
</html>
