<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Détruire la session
session_destroy();

echo json_encode(['succes' => true]);
exit;
