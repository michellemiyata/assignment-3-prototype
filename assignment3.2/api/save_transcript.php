<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['text'] ?? '';
    $userId = $_SESSION['user_id'];

    if (empty($text)) {
        echo json_encode(['success' => false, 'message' => 'Empty text']);
        exit;
    }

    try {
        // Create a session first
        $stmt = $pdo->prepare("INSERT INTO sessions (user_id, title) VALUES (?, ?)");
        $stmt->execute([$userId, 'Transcript ' . date('Y-m-d H:i')]);
        $sessionId = $pdo->lastInsertId();

        // Save transcript
        $stmt = $pdo->prepare("INSERT INTO transcripts (session_id, transcript_text) VALUES (?, ?)");
        $stmt->execute([$sessionId, $text]);

        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
