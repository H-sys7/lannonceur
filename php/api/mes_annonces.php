<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require '../config/db.php';

// Vérifier connexion
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

// Récupérer les annonces de l'utilisateur
$stmt = $pdo->prepare(
    "SELECT id, titre, description, prix, categorie, image_url, contact, date_creation 
     FROM produits 
     WHERE utilisateur_id = ? AND est_actif = 1
     ORDER BY date_creation DESC"
);

$stmt->execute([$_SESSION['user_id']]);
$annonces = $stmt->fetchAll();

echo json_encode($annonces);
exit;
