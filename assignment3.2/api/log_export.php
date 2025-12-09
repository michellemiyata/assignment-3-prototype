<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['transcript_id']) || !isset($_POST['type'])) {
    echo json_encode(['success' => false]);
    exit;
}

$transcriptId = $_POST['transcript_id'];
$type = $_POST['type'];

try {
    // Get session_id from transcript
    $stmt = $pdo->prepare("SELECT session_id FROM transcripts WHERE id = ?");
    $stmt->execute([$transcriptId]);
    $row = $stmt->fetch();

    if ($row) {
        $sessionId = $row['session_id'];
        $stmt = $pdo->prepare("INSERT INTO exports (session_id, export_type) VALUES (?, ?)");
        $stmt->execute([$sessionId, $type]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Transcript not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>