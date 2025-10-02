<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dictionary";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user name from database
$user_username = $_SESSION['username'];
$sql = "SELECT username FROM user WHERE username = '$user_username'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $display_name = $row['username'];
} else {
    $display_name = 'Unknown';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dictionary</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #EEEEEE;
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        .header {
            background: #3396D3;
            color: white;
            padding: 60px 0;
            text-align: center;
            box-shadow: 0 4px 20px rgba(51, 150, 211, 0.3);
        }
        .header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .search-section {
            background: #FFF0CE;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(51, 150, 211, 0.3);
            padding: 50px;
            margin: -30px auto 50px;
            max-width: 800px;
            position: relative;
            z-index: 10;
        }
        .search-form {
            position: relative;
        }
        .search-input {
            border: none;
            border-radius: 50px;
            padding: 20px 30px;
            font-size: 1.2rem;
            box-shadow: 0 10px 30px rgba(51, 150, 211, 0.3);
            width: 100%;
            transition: all 0.3s ease;
            background: #FFF0CE;
            color: #333;
        }
        .search-input:focus {
            box-shadow: 0 15px 40px rgba(51, 150, 211, 0.5);
            outline: none;
        }
        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: #3396D3;
            color: white;
            border-radius: 50px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 10px 30px rgba(51, 150, 211, 0.6);
        }
        .results-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .result-card {
            background: #FFF0CE;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(51, 150, 211, 0.3);
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #3396D3;
            color: #333;
        }
        .result-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(51, 150, 211, 0.3);
        }
        .word-header {
            background: #EBCB90;
            color: #333;
            padding: 25px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .word-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .word-type {
            background: #3396D3;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white;
        }
        .definition-section {
            padding: 30px;
        }
        .definition-label {
            color: #3396D3;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .definition-label i {
            margin-right: 10px;
        }
        .definition-text {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #3396D3;
        }
        .synonym-link, .antonym-link {
            color: #3396D3;
            text-decoration: none;
        }
        .synonym-link:hover, .antonym-link:hover {
            text-decoration: underline;
        }
        .no-results {
            text-align: center;
            padding: 80px 20px;
            color: #6c757d;
        }
        .no-results i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .no-results h4 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        .footer {
            background: #3396D3;
            color: white;
            text-align: center;
            padding: 40px 0;
            font-size: 0.9rem;
        }
        /* Modal custom styles */
        .modal-content {
            background: #EBCB90;
            border-radius: 20px;
            border: 1px solid #3396D3;
            color: #333;
        }
        .modal-header {
            background: #3396D3;
            color: white;
            border-bottom: none;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }
        .modal-title {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
        }
        .modal-body {
            background: #EFF5D2;
            color: #556B2F;
            padding: 20px 30px;
            font-size: 1.1rem;
        }
        .modal-footer {
            background: #EBCB90;
            border-top: none;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }
        .modal-footer .btn-primary {
            background: #3396D3;
            border: 1px solid #FFF0CE;
            color: #333;
            transition: all 0.3s ease;
        }
        .modal-footer .btn-primary:hover {
            background: #FFF0CE;
            border-color: #3396D3;
            color: #3396D3;
        }
        .modal-footer .btn-secondary {
            background: #3396D3;
            border: 1px solid #3396D3;
            color: white;
            transition: all 0.3s ease;
        }
        .modal-footer .btn-secondary:hover {
            background: #3396D3;
            border-color: #FFF0CE;
            color: white;
        }
        #suggestions {
            background: #FFF0CE !important;
            border: 1px solid #3396D3 !important;
            box-shadow: 0 10px 30px rgba(51, 150, 211, 0.3) !important;
            color: #3396D3 !important;
        }
        .dropdown-item {
            color: #3396D3 !important;
        }
        .dropdown-item:hover {
            background: #3396D3 !important;
            color: white !important;
        }
        .dropdown-item.active {
            background: #3396D3 !important;
            color: white !important;
        }
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }
            .search-section {
                padding: 30px 20px;
                margin: -20px 15px 30px;
            }
            .word-title {
                font-size: 1.8rem;
            }
            .definition-section {
                padding: 20px;
            }
            .word-header {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
        }
        @media (max-width: 576px) {
            .header {
                padding: 40px 0;
            }
            .header h1 {
                font-size: 2rem;
            }
            .search-section {
                padding: 20px 15px;
                margin: -15px 10px 20px;
            }
            .word-title {
                font-size: 1.5rem;
            }
            .definition-section {
                padding: 15px;
            }
            .modal-dialog {
                max-width: 95vw;
            }
        }
    </style>
</head>
<body>
        <header class="header">
            <div class="container">
                <h1><i class="fas fa-book"></i> Dictionary</h1>
                <p>Search for words and their meanings</p>
<div class="d-flex justify-content-end align-items-center" style="position: absolute; top: 20px; right: 20px;">
    <span class="me-3">Welcome, <?php echo htmlspecialchars($display_name); ?></span>
    <a href="logout.php" class="btn btn-outline-light"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
            </div>
        </header>

    <main class="container">
        <section class="search-section">
            <form method="POST" class="search-form" id="searchForm">
                <input type="text" class="search-input" name="keyword" id="keyword" placeholder="Search for a word..." value="<?php echo isset($_POST['keyword']) ? htmlspecialchars($_POST['keyword']) : ''; ?>" required>
                <div id="suggestions" class="dropdown-menu w-100" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 0 0 10px 10px; max-height: 200px; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.1);"></div>
                <button type="button" id="arrowDownBtn" class="search-btn" title="Select next suggestion" style="right: 80px;">
                    <i class="fas fa-arrow-down"></i>
                </button>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </section>

        <section class="results-container">
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

            // Handle search
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['keyword'])) {
                $keyword = $conn->real_escape_string($_POST['keyword']);
                $sql = "SELECT word, wordtype, definition, synonyms, antonyms FROM english WHERE word = '$keyword' ORDER BY word LIMIT 50";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="result-card">';
                        echo '<div class="word-header">';
                        echo '<h2 class="word-title">' . htmlspecialchars(ucfirst($row['word'])) . '</h2>';
                        if (!empty($row['wordtype'])) {
                            echo '<span class="word-type">' . htmlspecialchars($row['wordtype']) . '</span>';
                        }
                        echo '</div>';
                        echo '<div class="definition-section">';
                        echo '<div class="definition-label"><i class="fas fa-info-circle"></i> Definition</div>';
                        echo '<p class="definition-text">' . htmlspecialchars($row['definition']) . '</p>';
                        if (!empty($row['synonyms'])) {
                            $synonyms = explode(',', $row['synonyms']);
                            echo '<div class="mt-3"><strong>Synonyms: </strong>';
                            foreach ($synonyms as $synonym) {
                                $synonym = trim($synonym);
                                echo '<a href="#" class="synonym-link me-2" data-word="' . htmlspecialchars($synonym) . '">' . htmlspecialchars($synonym) . '</a>';
                            }
                            echo '</div>';
                        }
                        if (!empty($row['antonyms'])) {
                            $antonyms = explode(',', $row['antonyms']);
                            echo '<div class="mt-3"><strong>Antonyms: </strong>';
                            foreach ($antonyms as $antonym) {
                                $antonym = trim($antonym);
                                echo '<a href="#" class="antonym-link me-2" data-word="' . htmlspecialchars($antonym) . '">' . htmlspecialchars($antonym) . '</a>';
                            }
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="no-results">';
                    echo '<i class="fas fa-search"></i>';
                    echo '<h4>No results found for "' . htmlspecialchars($keyword) . '"</h4>';
                    echo '<p>Try searching for another word or check your spelling.</p>';
                    echo '</div>';
                }
            }

            $conn->close();
            ?>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Dictionary. Powered by comprehensive word data.</p>
        </div>
    </footer>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Autocomplete suggestions
            const keywordInput = document.getElementById('keyword');
            const suggestionsDiv = document.getElementById('suggestions');
            const arrowDownBtn = document.getElementById('arrowDownBtn');
            let selectedIndex = -1;

            function clearActiveSuggestion() {
                const items = suggestionsDiv.querySelectorAll('.dropdown-item.active');
                items.forEach(item => item.classList.remove('active'));
            }

            function selectSuggestion(index) {
                const items = suggestionsDiv.querySelectorAll('.dropdown-item');
                if (items.length === 0) return;
                if (index < 0) index = items.length - 1;
                if (index >= items.length) index = 0;
                clearActiveSuggestion();
                items[index].classList.add('active');
                keywordInput.value = items[index].textContent;
                selectedIndex = index;
                items[index].scrollIntoView({ block: 'nearest' });
            }

            keywordInput.addEventListener('input', function() {
                selectedIndex = -1;
                const query = this.value.trim();
                if (query.length > 0) {
                    fetch(`suggestions.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            suggestionsDiv.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(word => {
                                    const item = document.createElement('a');
                                    item.className = 'dropdown-item';
                                    item.href = '#';
                                    item.textContent = word;
                                    item.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        keywordInput.value = word;
                                        suggestionsDiv.style.display = 'none';
                                        selectedIndex = -1;
                                        clearActiveSuggestion();
                                    });
                                    suggestionsDiv.appendChild(item);
                                });
                                suggestionsDiv.style.display = 'block';
                            } else {
                                suggestionsDiv.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching suggestions:', error);
                            suggestionsDiv.style.display = 'none';
                        });
                } else {
                    suggestionsDiv.style.display = 'none';
                }
            });

            keywordInput.addEventListener('keydown', function(event) {
                const items = suggestionsDiv.querySelectorAll('.dropdown-item');
                if (items.length === 0) return;
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    selectedIndex = (selectedIndex + 1) % items.length;
                    selectSuggestion(selectedIndex);
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    selectedIndex--;
                    if (selectedIndex < 0) selectedIndex = items.length - 1;
                    selectSuggestion(selectedIndex);
                } else if (event.key === 'Enter' && selectedIndex >= 0) {
                    event.preventDefault();
                    keywordInput.value = items[selectedIndex].textContent;
                    suggestionsDiv.style.display = 'none';
                    selectedIndex = -1;
                    clearActiveSuggestion();
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!keywordInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.style.display = 'none';
                    selectedIndex = -1;
                    clearActiveSuggestion();
                }
            });

            arrowDownBtn.addEventListener('click', function() {
                const items = suggestionsDiv.querySelectorAll('.dropdown-item');
                if (items.length === 0) return;
                selectedIndex++;
                if (selectedIndex >= items.length) {
                    selectedIndex = 0;
                }
                selectSuggestion(selectedIndex);
            });
        });
    </script>

    <!-- Word Meaning Modal -->
    <div class="modal fade" id="wordMeaningModal" tabindex="-1" aria-labelledby="wordMeaningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="wordMeaningModalLabel">Word Meaning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="wordMeaningContent">
                    <!-- Meaning content will be loaded here -->
                    <p>Loading...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to fetch and show word meaning in modal
            function showWordMeaning(word) {
                const modal = new bootstrap.Modal(document.getElementById('wordMeaningModal'));
                const contentDiv = document.getElementById('wordMeaningContent');
                contentDiv.innerHTML = '<p>Loading...</p>';
                modal.show();

                fetch(`word_details.php?word=${encodeURIComponent(word)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            contentDiv.innerHTML = '<p class="text-danger">' + data.error + '</p>';
                        } else {
                            let html = '<h3>' + data.word + '</h3>';
                            if (data.wordtype) {
                                html += '<p><strong>Type:</strong> ' + data.wordtype + '</p>';
                            }
                            if (data.definition) {
                                html += '<p><strong>Definition:</strong> ' + data.definition + '</p>';
                            }
                            if (data.synonyms) {
                                html += '<p><strong>Synonyms:</strong> ' + data.synonyms + '</p>';
                            }
                            if (data.antonyms) {
                                html += '<p><strong>Antonyms:</strong> ' + data.antonyms + '</p>';
                            }
                            contentDiv.innerHTML = html;
                        }
                    })
                    .catch(error => {
                        contentDiv.innerHTML = '<p class="text-danger">Error loading word details.</p>';
                    });
            }

            // Add click event listeners for synonym and antonym links
            document.querySelectorAll('.synonym-link, .antonym-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const word = this.getAttribute('data-word');
                    showWordMeaning(word);
                });
            });
        });
    </script>
</body>
</html>
