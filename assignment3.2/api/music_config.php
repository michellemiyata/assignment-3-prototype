<?php
header('Content-Type: application/json');

// Return the configuration for the focus mode music
echo json_encode([
    'videoId' => 'atka_0TUTLM', // Vance Joy - Missing Piece (Official Video)
    'autoPlay' => true,
    'volume' => 100
]);
?>