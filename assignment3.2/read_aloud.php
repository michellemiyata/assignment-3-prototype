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
$defaultLanguage = $preferences['language'] ?? 'en-US';

// Map short codes to full locale codes if necessary
$langMap = [
    'en' => 'en-US',
    'es' => 'es-ES',
    'fr' => 'fr-FR',
    'ja' => 'ja-JP'
];
$speechLang = $langMap[$defaultLanguage] ?? 'en-US';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read Aloud - Smart Study</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>

<body>
    <div class="container">
        <header>
            <a href="dashboard.php" class="glass-button"><span class="material-icons-round"
                    style="vertical-align: middle;">arrow_back</span> Back</a>
            <h2>Read Aloud</h2>
            <div style="width: 80px;"></div>
        </header>

        <div class="center-screen" style="min-height: 60vh; align-items: flex-start;">
            <div class="glass-panel" style="width: 100%; max-width: 600px; padding: 30px;">
                <p class="text-muted" style="margin-bottom: 15px;">Paste text below to have it read aloud.</p>
                <textarea id="textToRead" class="glass-input" style="min-height: 200px; margin-bottom: 20px;"
                    placeholder="Enter text here..."></textarea>

                <div class="controls" style="justify-content: center;">
                    <button class="glass-button primary" onclick="speak()">
                        <span class="material-icons-round" style="vertical-align: middle;">play_arrow</span> Play
                    </button>
                    <button class="glass-button" onclick="stop()">
                        <span class="material-icons-round" style="vertical-align: middle;">stop</span> Stop
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const synth = window.speechSynthesis;
        const preferredLanguage = "<?php echo $speechLang; ?>";

        function speak() {
            if (synth.speaking) {
                console.error('speechSynthesis.speaking');
                return;
            }
            const text = document.getElementById('textToRead').value;
            if (text !== '') {
                const utterThis = new SpeechSynthesisUtterance(text);
                utterThis.lang = preferredLanguage;
                utterThis.onend = function (event) {
                    console.log('SpeechSynthesisUtterance.onend');
                }
                utterThis.onerror = function (event) {
                    console.error('SpeechSynthesisUtterance.onerror');
                }
                synth.speak(utterThis);
            }
        }

        function stop() {
            if (synth.speaking) {
                synth.cancel();
            }
        }
    </script>
</body>

</html>