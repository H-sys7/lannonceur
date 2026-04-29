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
$annonce_id = (int)($_POST['annonce_id'] ?? 0);
$titre = trim($_POST['titre'] ?? '');
$description = trim($_POST['description'] ?? '');
$prix = (float)($_POST['prix'] ?? 0);
$categorie = trim($_POST['categorie'] ?? '');
$contact = trim($_POST['contact'] ?? '');

// Validation
if (!$annonce_id || empty($titre) || empty($description) || $prix <= 0 || empty($categorie) || empty($contact)) {
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

// Vérifier que l'annonce appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT image_url FROM produits WHERE id = ? AND utilisateur_id = ?");
$stmt->execute([$annonce_id, $user_id]);
$annonce = $stmt->fetch();

if (!$annonce) {
    echo json_encode(['erreur' => 'Annonce non trouvée']);
    exit;
}

$image_filename = $annonce['image_url']; // Garder l'image actuelle par défaut

// Gestion nouvelle image (optionnel)
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
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

    // Générer un nom unique pour la nouvelle image
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_image_filename = 'annonce_' . $user_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $upload_path = $upload_dir . $new_image_filename;

    // Déplacer le fichier
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        echo json_encode(['erreur' => 'Erreur lors de la sauvegarde de l\'image']);
        exit;
    }

    // Supprimer l'ancienne image
    if (!empty($annonce['image_url'])) {
        $old_image_path = $upload_dir . $annonce['image_url'];
        if (file_exists($old_image_path)) {
            unlink($old_image_path);
        }
    }

    $image_filename = $new_image_filename;
}

// Mettre à jour l'annonce
try {
    $stmt = $pdo->prepare(
        "UPDATE produits SET titre = ?, description = ?, prix = ?, categorie = ?, image_url = ?, contact = ? WHERE id = ? AND utilisateur_id = ?"
    );
    
    $stmt->execute([$titre, $description, $prix, $categorie, $image_filename, $contact, $annonce_id, $user_id]);
    
    echo json_encode([
        'succes' => true,
        'id' => $annonce_id,
        'message' => 'Annonce modifiée avec succès'
    ]);
} catch (PDOException $e) {
    echo json_encode(['erreur' => 'Erreur lors de la modification de l\'annonce']);
}
exit;
