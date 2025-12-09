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
$recognitionLang = $langMap[$defaultLanguage] ?? 'en-US';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcribe - Smart Study</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>

<body>
    <div class="container">
        <header>
            <a href="dashboard.php" class="glass-button"><span class="material-icons-round"
                    style="vertical-align: middle;">arrow_back</span> Back</a>
            <h2>Lecture Transcription</h2>
            <div style="width: 80px;"></div> <!-- Spacer for centering -->
        </header>

        <div class="transcribe-container">
            <div class="glass-panel" style="padding: 20px;">
                <textarea id="transcriptText" class="glass-input transcript-area"
                    placeholder="Transcription will appear here..."></textarea>

                <div class="controls">
                    <button id="startBtn" class="glass-button primary" onclick="startDictation()">
                        <span class="material-icons-round" style="vertical-align: middle;">mic</span> Start Recording
                    </button>
                    <button id="stopBtn" class="glass-button" onclick="stopDictation()" disabled>
                        <span class="material-icons-round" style="vertical-align: middle;">stop</span> Stop
                    </button>
                    <button class="glass-button" onclick="saveTranscript()">
                        <span class="material-icons-round" style="vertical-align: middle;">save</span> Save Note
                    </button>
                    <button class="glass-button" onclick="downloadTxt()">
                        <span class="material-icons-round" style="vertical-align: middle;">download</span> Export TXT
                    </button>
                </div>
                <p id="status" class="text-muted" style="margin-top: 10px;">Ready to record.</p>
            </div>

            <div class="glass-panel" style="padding: 20px;">
                <h3>Recent Transcripts</h3>
                <div id="recentList" style="margin-top: 15px;">
                    <!-- Populated by JS/PHP -->
                    <p class="text-muted">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let recognition;

        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.continuous = true;
            recognition.interimResults = true;
            recognition.lang = '<?php echo $recognitionLang; ?>';

            recognition.onstart = function () {
                document.getElementById('status').innerText = "Listening...";
                document.getElementById('startBtn').classList.add('recording');
                document.getElementById('startBtn').disabled = true;
                document.getElementById('stopBtn').disabled = false;
            };

            recognition.onerror = function (event) {
                document.getElementById('status').innerText = "Error: " + event.error;
                stopDictation();
            };

            recognition.onend = function () {
                document.getElementById('status').innerText = "Stopped.";
                document.getElementById('startBtn').classList.remove('recording');
                document.getElementById('startBtn').disabled = false;
                document.getElementById('stopBtn').disabled = true;
            };

            recognition.onresult = function (event) {
                let finalTranscript = '';
                let interimTranscript = '';

                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        finalTranscript += event.results[i][0].transcript;
                    } else {
                        interimTranscript += event.results[i][0].transcript;
                    }
                }

                const textarea = document.getElementById('transcriptText');
                // Append only final results to avoid overwriting manual edits too aggressively
                // In a real app, logic would be more complex to handle cursor position
                if (finalTranscript) {
                    textarea.value += finalTranscript + ' ';
                }
            };
        } else {
            document.getElementById('status').innerText = "Web Speech API not supported in this browser.";
            document.getElementById('startBtn').disabled = true;
        }

        function startDictation() {
            if (recognition) recognition.start();
        }

        function stopDictation() {
            if (recognition) recognition.stop();
        }

        function saveTranscript() {
            const text = document.getElementById('transcriptText').value;
            if (!text) return alert("Nothing to save!");

            fetch('api/save_transcript.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'text=' + encodeURIComponent(text)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Transcript saved!');
                        loadRecents();
                    } else {
                        alert('Error saving: ' + data.message);
                    }
                });
        }

        function downloadTxt() {
            const text = document.getElementById('transcriptText').value;
            const element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', 'transcript_' + new Date().toISOString() + '.txt');
            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        }

        function loadRecents() {
            fetch('api/get_transcripts.php')
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('recentList');
                    list.innerHTML = '';
                    if (data.length === 0) {
                        list.innerHTML = '<p class="text-muted">No saved transcripts yet.</p>';
                        return;
                    }
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.style.marginBottom = '10px';
                        div.style.padding = '10px';
                        div.style.background = 'rgba(255,255,255,0.05)';
                        div.style.borderRadius = '8px';
                        div.innerHTML = `
                        <div style="font-weight:bold; font-size:0.9rem;">${item.created_at}</div>
                        <div style="font-size:0.8rem; color:#aaa; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${item.transcript_text}</div>
                    `;
                        list.appendChild(div);
                    });
                });
        }

        // Load recents on start
        loadRecents();
    </script>
</body>

</html>