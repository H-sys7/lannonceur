<?php
require '../config/db.php';

// Paramètres de filtre (GET)
$categorie = $_GET['categorie'] ?? null;
$prix_max  = isset($_GET['prix_max']) ? floatval($_GET['prix_max']) : null;
$recherche = $_GET['q'] ?? null;

$sql    = "SELECT p.*, u.pseudonyme AS vendeur
           FROM produits p
           JOIN utilisateurs u ON p.utilisateur_id = u.id
           WHERE p.est_actif = 1";
$params = [];

if ($categorie) {
    $sql .= " AND p.categorie = ?";
    $params[] = $categorie;
}
if ($prix_max) {
    $sql .= " AND p.prix <= ?";
    $params[] = $prix_max;
}
if ($recherche) {
    $sql .= " AND (p.titre LIKE ? OR p.description LIKE ?)";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

$sql .= " ORDER BY p.date_creation DESC LIMIT 50";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

header('Content-Type: application/json');
echo json_encode($stmt->fetchAll());