<?php
header('Content-Type: application/json; charset=utf-8');
require '../config/db.php';

// Récupérer toutes les annonces actives (avec pagination optionnelle)
$page = (int)($_GET['page'] ?? 1);
$limit = 12;
$offset = ($page - 1) * $limit;

$categorie = trim($_GET['cat'] ?? '');

$query = "SELECT id, titre, description, prix, categorie, image_url, contact, utilisateur_id, date_creation FROM produits WHERE est_actif = 1";
$params = [];

if (!empty($categorie)) {
    $query .= " AND categorie = ?";
    $params[] = $categorie;
}

$query .= " ORDER BY date_creation DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$annonces = $stmt->fetchAll();

// Compter le total
$count_query = "SELECT COUNT(*) as total FROM produits WHERE est_actif = 1";
$count_params = [];

if (!empty($categorie)) {
    $count_query .= " AND categorie = ?";
    $count_params[] = $categorie;
}

$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($count_params);
$total = $count_stmt->fetch()['total'];

echo json_encode([
    'annonces' => $annonces,
    'total' => $total,
    'page' => $page,
    'per_page' => $limit,
    'total_pages' => ceil($total / $limit)
]);
exit;
