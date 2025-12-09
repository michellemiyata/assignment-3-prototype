<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Join sessions and transcripts to get user's data
    $stmt = $pdo->prepare("
        SELECT t.transcript_text, t.created_at 
        FROM transcripts t 
        JOIN sessions s ON t.session_id = s.id 
        WHERE s.user_id = ? 
        ORDER BY t.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $results = $stmt->fetchAll();

    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode([]);
}
