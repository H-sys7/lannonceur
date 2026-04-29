<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require '../config/db.php';

// Vérifier connexion
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'Non authentifié']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$annonce_id = $data['id'] ?? null;

if (!$annonce_id) {
    echo json_encode(['erreur' => 'ID annonce manquant']);
    exit;
}

// Vérifier que l'annonce appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT image_url FROM produits WHERE id = ? AND utilisateur_id = ?");
$stmt->execute([$annonce_id, $_SESSION['user_id']]);
$annonce = $stmt->fetch();

if (!$annonce) {
    echo json_encode(['erreur' => 'Annonce non trouvée']);
    exit;
}

// Supprimer l'image
if (!empty($annonce['image_url'])) {
    $image_path = '../../uploads/' . $annonce['image_url'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// Supprimer l'annonce de la BD
$stmt = $pdo->prepare("DELETE FROM produits WHERE id = ? AND utilisateur_id = ?");
$stmt->execute([$annonce_id, $_SESSION['user_id']]);

echo json_encode(['succes' => true]);
exit;
