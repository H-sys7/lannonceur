<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require '../config/db.php';

$email = trim($_POST['email'] ?? '');
$mdp   = $_POST['mot_de_passe'] ?? '';

if (empty($email) || empty($mdp)) {
    echo json_encode(['erreur' => 'Email et mot de passe requis']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND est_actif = 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($mdp, $user['mot_de_passe'])) {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_role'] = $user['role'];

    echo json_encode(['succes' => true, 'pseudo' => $user['pseudonyme']]);
} else {
    echo json_encode(['erreur' => 'Identifiants incorrects']);
}
exit;