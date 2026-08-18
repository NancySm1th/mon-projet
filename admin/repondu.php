<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    http_response_code(403);
    echo "Accès refusé";
    exit();
}

require_once "../config/connexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Méthode non autorisée";
    exit();
}

$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

if ($id <= 0) {
    http_response_code(400);
    echo "Identifiant invalide";
    exit();
}

$requete = $connexion->prepare(
    "UPDATE messages SET STATUT = 'Répondu' WHERE IDENTIFIANT = ?"
);

if (!$requete) {
    http_response_code(500);
    echo "Erreur de préparation";
    exit();
}

$requete->bind_param("i", $id);

if ($requete->execute()) {
    echo "success";
} else {
    http_response_code(500);
    echo "Erreur lors de la mise à jour";
}

$requete->close();
$connexion->close();

?>