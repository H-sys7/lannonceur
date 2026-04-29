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
$titre = trim($_POST['titre'] ?? '');
$description = trim($_POST['description'] ?? '');
$prix = (float)($_POST['prix'] ?? 0);
$categorie = trim($_POST['categorie'] ?? '');
$contact = trim($_POST['contact'] ?? '');

// Validation
if (empty($titre) || empty($description) || $prix <= 0 || empty($categorie) || empty($contact)) {
    echo json_encode(['erreur' => 'Tous les champs sont requis']);
    exit;
}

if (!preg_match('/^\d{10}$/', $contact)) {
    echo json_encode(['erreur' => 'Numéro de téléphone invalide (10 chiffres)']);
    exit;
}

if (strlen($titre) > 255 || strlen($description) > 1000) {
    echo json_encode(['erreur' => 'Texte trop long']);
    exit;
}

// Gestion upload image
$image_filename = null;

if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
    echo json_encode(['erreur' => 'Image requise']);
    exit;
}

if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['erreur' => 'Erreur upload image']);
    exit;
}

// Vérifier le type MIME
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$file_type = mime_content_type($_FILES['image']['tmp_name']);

if (!in_array($file_type, $allowed_types)) {
    echo json_encode(['erreur' => 'Type d\'image non autorisé (JPG, PNG, GIF, WEBP)']);
    exit;
}

// Vérifier la taille (5 MB max)
if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
    echo json_encode(['erreur' => 'Image trop volumineuse (max 5 MB)']);
    exit;
}

// Créer le répertoire uploads s'il n'existe pas
$upload_dir = '../../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Générer un nom unique pour l'image
$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$image_filename = 'annonce_' . $user_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$upload_path = $upload_dir . $image_filename;

// Déplacer le fichier
if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
    echo json_encode(['erreur' => 'Erreur lors de la sauvegarde de l\'image']);
    exit;
}

// Insérer dans la BD
try {
    $stmt = $pdo->prepare(
        "INSERT INTO produits (utilisateur_id, titre, description, prix, categorie, image_url, contact) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    
    $stmt->execute([$user_id, $titre, $description, $prix, $categorie, $image_filename, $contact]);
    
    echo json_encode([
        'succes' => true,
        'id' => $pdo->lastInsertId(),
        'message' => 'Annonce créée avec succès'
    ]);
} catch (PDOException $e) {
    // Supprimer l'image en cas d'erreur
    unlink($upload_path);
    echo json_encode(['erreur' => 'Erreur lors de la création de l\'annonce']);
}
exit;