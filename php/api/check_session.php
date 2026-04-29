<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require '../config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['connected' => false]);
    exit;
}

// Récupérer les infos utilisateur
$stmt = $pdo->prepare("SELECT id, pseudonyme, email, date_inscription FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user) {
    echo json_encode([
        'connected' => true,
        'user_id' => $user['id'],
        'pseudo' => $user['pseudonyme'],
        'email' => $user['email'],
        'date' => substr($user['date_inscription'], 0, 10)
    ]);
} else {
    echo json_encode(['connected' => false]);
}
exit;
