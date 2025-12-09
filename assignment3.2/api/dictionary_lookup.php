<?php
header('Content-Type: application/json');

$word = $_GET['word'] ?? '';
$lang = $_GET['lang'] ?? 'en';
$word = strtolower(trim($word));

if (empty($word)) {
    echo json_encode(['found' => false]);
    exit;
}

$jsonFile = '../data/dictionary.json';

if (!file_exists($jsonFile)) {
    echo json_encode(['found' => false, 'error' => 'Dictionary file missing']);
    exit;
}

$jsonData = file_get_contents($jsonFile);
$dictionary = json_decode($jsonData, true);

$foundItem = null;
foreach ($dictionary as $item) {
    if (strtolower($item['word']) === $word) {
        $foundItem = $item;
        break;
    }
}

if ($foundItem) {
    echo json_encode(['found' => true, 'data' => $foundItem]);
} else {
    // Fallback to External API (Free Dictionary API)
    $apiUrl = "https://api.dictionaryapi.dev/api/v2/entries/" . urlencode($lang) . "/" . urlencode($word);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev environments
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $apiData = json_decode($response, true);
        if (isset($apiData[0])) {
            $firstMeaning = $apiData[0]['meanings'][0]['definitions'][0] ?? null;

            if ($firstMeaning) {
                $foundItem = [
                    'word' => $apiData[0]['word'],
                    'definition' => $firstMeaning['definition'],
                    'example' => $firstMeaning['example'] ?? 'No example available.'
                ];

                // Save to vocabulary table if user is logged in
                session_start();
                if (isset($_SESSION['user_id'])) {
                    require_once '../includes/db.php';
                    try {
                        // Check if word already exists for this user to avoid duplicates
                        $stmt = $pdo->prepare("SELECT id FROM vocabulary WHERE user_id = ? AND word = ?");
                        $stmt->execute([$_SESSION['user_id'], $foundItem['word']]);

                        if (!$stmt->fetch()) {
                            $stmt = $pdo->prepare("INSERT INTO vocabulary (user_id, word, definition, example) VALUES (?, ?, ?, ?)");
                            $stmt->execute([
                                $_SESSION['user_id'],
                                $foundItem['word'],
                                $foundItem['definition'],
                                $foundItem['example']
                            ]);
                        }
                    } catch (Exception $e) {
                        // Ignore DB errors during lookup to keep UI fast
                    }
                }

                echo json_encode(['found' => true, 'data' => $foundItem]);
                exit;
            }
        }
    }

    echo json_encode(['found' => false]);
}
