<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['language'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing language parameter']);
    exit;
}

$language = $data['language'];
$userId = $_SESSION['user_id'];

try {
    // First, get existing preferences
    $stmt = $pdo->prepare("SELECT preferences FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();

    $preferences = [];
    if ($result && $result['preferences']) {
        $preferences = json_decode($result['preferences'], true);
        if (!is_array($preferences)) {
            $preferences = [];
        }
    }

    // Update language
    $preferences['language'] = $language;

    // Save back to database
    $stmt = $pdo->prepare("UPDATE users SET preferences = ? WHERE id = ?");
    $stmt->execute([json_encode($preferences), $userId]);

    echo json_encode(['success' => true, 'message' => 'Preferences updated']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
