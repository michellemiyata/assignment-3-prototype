<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch all transcripts for this user
$stmt = $pdo->prepare("
    SELECT t.id, t.transcript_text, t.created_at, s.title 
    FROM transcripts t 
    JOIN sessions s ON t.session_id = s.id 
    WHERE s.user_id = ? 
    ORDER BY t.created_at DESC
");
$stmt->execute([$userId]);
$transcripts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Notes - Smart Study</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>

<body>
    <div class="container">
        <header>
            <a href="dashboard.php" class="glass-button"><span class="material-icons-round"
                    style="vertical-align: middle;">arrow_back</span> Back</a>
            <h2>Export Notes</h2>
            <div style="width: 80px;"></div>
        </header>

        <div class="glass-panel" style="padding: 30px;">
            <?php if (count($transcripts) === 0): ?>
                <div style="text-align: center; padding: 40px;">
                    <span class="material-icons-round" style="font-size: 4rem; color: var(--text-muted);">note_add</span>
                    <h3>No transcripts found</h3>
                    <p class="text-muted">Go to the Transcribe page to create your first note.</p>
                    <a href="transcribe.php" class="glass-button primary" style="margin-top: 20px;">Start Transcribing</a>
                </div>
            <?php else: ?>
                <div style="display: grid; gap: 15px;">
                    <?php foreach ($transcripts as $item): ?>
                        <div class="glass-panel"
                            style="padding: 20px; display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.03);">
                            <div style="overflow: hidden; margin-right: 20px;">
                                <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($item['title']); ?></h4>
                                <p class="text-muted"
                                    style="font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars(substr($item['transcript_text'], 0, 100)) . '...'; ?>
                                </p>
                                <small
                                    style="color: var(--accent-color);"><?php echo date('M d, Y h:i A', strtotime($item['created_at'])); ?></small>
                            </div>

                            <div style="display: flex; gap: 10px; flex-shrink: 0;">
                                <button
                                    onclick="downloadTxt(<?php echo $item['id']; ?>, <?php echo htmlspecialchars(json_encode($item['transcript_text'])); ?>, '<?php echo $item['title']; ?>')"
                                    class="glass-button" title="Download TXT">
                                    <span class="material-icons-round">description</span> TXT
                                </button>
                                <button
                                    onclick="printPdf(<?php echo $item['id']; ?>, <?php echo htmlspecialchars(json_encode($item['transcript_text'])); ?>)"
                                    class="glass-button" title="Print / Save as PDF">
                                    <span class="material-icons-round">picture_as_pdf</span> PDF
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function logExport(id, type) {
            fetch('api/log_export.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `transcript_id=${id}&type=${type}`
            });
        }

        function downloadTxt(id, text, title) {
            logExport(id, 'txt');
            const element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', title.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.txt');
            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        }

        function printPdf(id, text) {
            logExport(id, 'pdf');
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Export PDF</title>');
            printWindow.document.write('<style>body{font-family: sans-serif; line-height: 1.6; padding: 40px;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h1>Transcript Export</h1>');
            printWindow.document.write('<hr>');
            printWindow.document.write('<p>' + text.replace(/\n/g, '<br>') + '</p>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>

</html>