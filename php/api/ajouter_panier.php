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
$produit_id = (int)($_POST['produit_id'] ?? 0);

if (!$produit_id) {
    echo json_encode(['erreur' => 'ID produit manquant']);
    exit;
}

// Vérifier que le produit existe
$stmt = $pdo->prepare("SELECT id FROM produits WHERE id = ? AND est_actif = 1");
$stmt->execute([$produit_id]);

if (!$stmt->fetch()) {
    echo json_encode(['erreur' => 'Produit non trouvé']);
    exit;
}

// Ajouter au panier (ou ignorer si déjà présent)
try {
    $stmt = $pdo->prepare(
        "INSERT INTO panier (utilisateur_id, produit_id) 
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE ajoute_le = CURRENT_TIMESTAMP"
    );
    
    $stmt->execute([$user_id, $produit_id]);
    
    echo json_encode([
        'succes' => true,
        'message' => 'Article ajouté au panier'
    ]);
} catch (PDOException $e) {
    echo json_encode(['erreur' => 'Erreur lors de l\'ajout au panier']);
}
exit;
