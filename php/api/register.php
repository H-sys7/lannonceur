<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require '../config/db.php';

$pseudo = trim($_POST['pseudonyme'] ?? '');
$email  = trim($_POST['email'] ?? '');
$mdp    = $_POST['mot_de_passe'] ?? '';

// Validation
if (empty($pseudo)) {
    echo json_encode(['erreur' => 'Le pseudonyme est requis']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['erreur' => 'Email invalide']);
    exit;
}
if (strlen($mdp) < 8) {
    echo json_encode(['erreur' => 'Le mot de passe doit contenir au moins 8 caractères']);
    exit;
}

$hash = password_hash($mdp, PASSWORD_BCRYPT);

$stmt = $pdo->prepare(
    "INSERT INTO utilisateurs (pseudonyme, email, mot_de_passe) VALUES (?, ?, ?)"
);

try {
    $stmt->execute([$pseudo, $email, $hash]);
    $user_id = $pdo->lastInsertId();

    $_SESSION['user_id']   = $user_id;
    $_SESSION['user_role'] = 'acheteur';

    echo json_encode(['succes' => true, 'id' => $user_id]);
} catch (PDOException $e) {
    echo json_encode(['erreur' => 'Email ou pseudonyme déjà utilisé']);
}
exit;