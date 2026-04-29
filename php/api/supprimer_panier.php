<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require '../config/db.php';

// Vérifier connexion
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'Non authentifié']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$produit_id = $data['produit_id'] ?? null;

if (!$produit_id) {
    echo json_encode(['erreur' => 'ID produit manquant']);
    exit;
}

// Supprimer l'article du panier
try {
    $stmt = $pdo->prepare("DELETE FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
    $stmt->execute([$user_id, $produit_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['succes' => true, 'message' => 'Article supprimé du panier']);
    } else {
        echo json_encode(['erreur' => 'Article non trouvé dans le panier']);
    }
} catch (PDOException $e) {
    echo json_encode(['erreur' => 'Erreur lors de la suppression']);
}
exit;
