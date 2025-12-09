<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch user preferences
$stmt = $pdo->prepare("SELECT preferences FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_data = $stmt->fetch();
$preferences = json_decode($user_data['preferences'] ?? '{}', true);
$defaultLanguage = $preferences['language'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Dictionary - Smart Study</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>

<body>
    <div class="container">
        <header>
            <a href="dashboard.php" class="glass-button"><span class="material-icons-round"
                    style="vertical-align: middle;">arrow_back</span> Back</a>
            <h2>Smart Dictionary</h2>
            <div style="width: 80px;"></div>
        </header>

        <div class="center-screen" style="min-height: 60vh; align-items: flex-start;">
            <div class="glass-panel" style="width: 100%; max-width: 600px; padding: 30px;">
                <div class="form-group">
                    <label>Type to search instantly...</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="searchInput" class="glass-input" placeholder="Start typing a word..."
                            oninput="lookupWord()">
                    </div>
                </div>

                <div id="resultArea" class="dictionary-result glass-panel"
                    style="background: rgba(0,0,0,0.2); border: none;">
                    <h3 id="wordTitle" class="word-title"></h3>
                    <p id="wordDef" style="margin-bottom: 15px; font-size: 1.1rem;"></p>
                    <p id="wordExample" class="text-muted"
                        style="font-style: italic; border-left: 3px solid var(--accent-color); padding-left: 10px;"></p>
                </div>

                <div id="errorArea"
                    style="display:none; color: var(--danger-color); margin-top: 20px; text-align: center;">
                    Word not found.
                </div>
            </div>
        </div>
    </div>

    <script>
        let timeout = null;
        const preferredLanguage = "<?php echo $defaultLanguage; ?>";

        function lookupWord() {
            const word = document.getElementById('searchInput').value.trim();

            // Clear previous results if empty
            if (!word) {
                document.getElementById('resultArea').classList.remove('active');
                document.getElementById('errorArea').style.display = 'none';
                return;
            }

            // Debounce slightly to avoid too many requests
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetch('api/dictionary_lookup.php?word=' + encodeURIComponent(word) + '&lang=' + preferredLanguage)
                    .then(res => res.json())
                    .then(data => {
                        if (data.found) {
                            document.getElementById('resultArea').classList.add('active');
                            document.getElementById('errorArea').style.display = 'none';

                            document.getElementById('wordTitle').textContent = data.data.word;
                            document.getElementById('wordDef').textContent = data.data.definition;
                            document.getElementById('wordExample').textContent = '"' + data.data.example + '"';
                        } else {
                            document.getElementById('resultArea').classList.remove('active');
                            document.getElementById('errorArea').style.display = 'block';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                    });
            }, 300); // Wait 300ms after typing stops
        }
    </script>
</body>

</html>