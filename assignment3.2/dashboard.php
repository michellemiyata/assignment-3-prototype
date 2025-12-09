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
    <title>Dashboard - StudyBuddy</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>

<body>
    <div class="container">
        <header>
            <div class="user-profile">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
                <div>
                    <h3>Welcome back,</h3>
                    <p style="color: var(--accent-color)"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                </div>
            </div>
            <a href="logout.php" class="glass-button">Logout</a>
        </header>

        <div class="dashboard-grid">
            <a href="transcribe.php" class="glass-panel feature-card">
                <span class="material-icons-round feature-icon">mic</span>
                <h3>Transcribe</h3>
                <p class="text-muted">Convert speech to text instantly</p>
            </a>

            <a href="dictionary.php" class="glass-panel feature-card">
                <span class="material-icons-round feature-icon">menu_book</span>
                <h3>Smart Dictionary</h3>
                <p class="text-muted">Look up definitions & examples</p>
            </a>

            <a href="focus.php" class="glass-panel feature-card">
                <span class="material-icons-round feature-icon">timer</span>
                <h3>Focus Mode</h3>
                <p class="text-muted">Distraction-free study timer</p>
            </a>

            <a href="read_aloud.php" class="glass-panel feature-card">
                <span class="material-icons-round feature-icon">record_voice_over</span>
                <h3>Read Aloud</h3>
                <p class="text-muted">Listen to your notes</p>
            </a>

            <a href="profile.php" class="glass-panel feature-card">
                <span class="material-icons-round feature-icon">person</span>
                <h3>Profile</h3>
                <p class="text-muted">Manage settings & preferences</p>
            </a>

            <a href="export.php" class="glass-panel feature-card">
                <span class="material-icons-round feature-icon">file_upload</span>
                <h3>Export</h3>
                <p class="text-muted">Save notes as PDF or TXT</p>
            </a>
        </div>
    </div>
</body>

</html>