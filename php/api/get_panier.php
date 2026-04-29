<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require '../config/db.php';

// Vérifier connexion
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les articles du panier avec les infos des produits
$stmt = $pdo->prepare(
    "SELECT p.id, p.titre, p.description, p.prix, p.categorie, p.image_url, p.contact, 
            p.utilisateur_id, u.pseudonyme
     FROM panier pa
     JOIN produits p ON pa.produit_id = p.id
     JOIN utilisateurs u ON p.utilisateur_id = u.id
     WHERE pa.utilisateur_id = ? AND p.est_actif = 1
     ORDER BY pa.ajoute_le DESC"
);

$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();

echo json_encode($articles);
exit;
