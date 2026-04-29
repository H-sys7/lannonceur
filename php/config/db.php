<?php
$host = 'localhost';
$db   = 'lannonceur';
$user = 'root';       // XAMPP par défaut
$pass = '';           // XAMPP par défaut

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode(['erreur' => 'Connexion impossible']));
}