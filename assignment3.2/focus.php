<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Focus Mode - Smart Study</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>

<body>
    <div class="container non-focus-elements">
        <header>
            <a href="dashboard.php" class="glass-button"><span class="material-icons-round"
                    style="vertical-align: middle;">arrow_back</span> Back</a>
            <h2>Focus Mode</h2>
            <div style="width: 80px;"></div>
        </header>
    </div>

    <div class="focus-timer-container"
        style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; margin-top: 50px; min-height: 60vh;">
        <div class="timer-circle" id="timerDisplay">
            00:30
        </div>

        <div class="controls" style="justify-content: center;">
            <button class="glass-button primary" onclick="startTimer()">Start</button>
            <button class="glass-button" onclick="pauseTimer()">Pause</button>
            <button class="glass-button" onclick="resetTimer()">Reset</button>
        </div>

        <div style="margin-top: 30px;" class="non-focus-elements">
            <button class="glass-button" onclick="toggleFocusMode()"
                style="border-color: var(--accent-color); color: var(--accent-color);">
                <span class="material-icons-round" style="vertical-align: middle;">fullscreen</span> Enter Immersive
                Mode
            </button>
        </div>

        <div style="margin-top: 30px; display: none;" id="exitFocusBtn">
            <button class="glass-button" onclick="toggleFocusMode()">
                <span class="material-icons-round" style="vertical-align: middle;">fullscreen_exit</span> Exit Immersive
                Mode
            </button>
        </div>
    </div>

    <!-- Session Complete Modal with YouTube Player -->
    <div id="completionModal" class="modal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center; flex-direction: column;">
        <div class="glass-panel" style="padding: 40px; text-align: center; max-width: 600px; width: 90%;">
            <h2 style="margin-bottom: 20px;">Focus Session Complete!</h2>
            <p style="margin-bottom: 30px;">Great job! Enjoy this break.</p>
            <div id="player"></div>
            <button class="glass-button primary" onclick="closeCompletionModal()" style="margin-top: 20px;">Close & Stop
                Music</button>
        </div>
    </div>

    <script>
        let timeLeft = 30;
        let timerId = null;
        const display = document.getElementById('timerDisplay');
        let player;
        let musicConfig = null;

        // Fetch music config
        fetch('api/music_config.php')
            .then(response => response.json())
            .then(data => {
                musicConfig = data;
                loadYouTubeAPI();
            })
            .catch(err => console.error('Error loading music config:', err));

        function loadYouTubeAPI() {
            const tag = document.createElement('script');
            tag.src = "https://www.youtube.com/iframe_api";
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        }

        function onYouTubeIframeAPIReady() {
            if (!musicConfig) return;
            player = new YT.Player('player', {
                height: '315',
                width: '100%',
                videoId: musicConfig.videoId,
                events: {
                    'onReady': onPlayerReady,
                }
            });
        }

        function onPlayerReady(event) {
            // Player is ready
        }

        function updateDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            display.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        function startTimer() {
            if (timerId) return;
            timerId = setInterval(() => {
                timeLeft--;
                updateDisplay();
                if (timeLeft <= 0) {
                    clearInterval(timerId);
                    timerId = null;
                    showCompletionModal();
                }
            }, 1000);
        }

        function showCompletionModal() {
            const modal = document.getElementById('completionModal');
            modal.style.display = 'flex';
            if (player && player.playVideo) {
                player.playVideo();
            }
        }

        function closeCompletionModal() {
            const modal = document.getElementById('completionModal');
            modal.style.display = 'none';
            if (player && player.stopVideo) {
                player.stopVideo();
            }
            resetTimer();
        }

        function pauseTimer() {
            clearInterval(timerId);
            timerId = null;
        }

        function resetTimer() {
            pauseTimer();
            timeLeft = 30;
            updateDisplay();
        }

        function toggleFocusMode() {
            document.body.classList.toggle('focus-mode');
            const exitBtn = document.getElementById('exitFocusBtn');
            if (document.body.classList.contains('focus-mode')) {
                exitBtn.style.display = 'block';
            } else {
                exitBtn.style.display = 'none';
            }
        }
    </script>
</body>

</html>