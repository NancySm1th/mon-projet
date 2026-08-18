<?php

require_once "../config/connexion.php";

// Autoriser uniquement les requêtes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Méthode non autorisée.";
    exit();
}


// Récupération des données
$nom = isset($_POST["name"]) ? trim($_POST["name"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$telephone = isset($_POST["telephone"]) ? trim($_POST["telephone"]) : "";
$message = isset($_POST["message"]) ? trim($_POST["message"]) : "";


// Vérification des champs obligatoires
if ($nom === "" || $email === "" || $telephone === "" || $message === "") {
    http_response_code(400);
    echo "Veuillez remplir tous les champs.";
    exit();
}


// Vérification de l'adresse email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Adresse email invalide.";
    exit();
}


// Limitation de la longueur des données
if (strlen($nom) > 100) {
    http_response_code(400);
    echo "Le nom est trop long.";
    exit();
}

if (strlen($email) > 150) {
    http_response_code(400);
    echo "L'adresse email est trop longue.";
    exit();
}

if (strlen($telephone) > 30) {
    http_response_code(400);
    echo "Le numéro de téléphone est trop long.";
    exit();
}

if (strlen($message) > 5000) {
    http_response_code(400);
    echo "Le message est trop long.";
    exit();
}


// Informations automatiques
$sujet = "Message depuis le site ALLIANCE SUD-EST";
$date_envoi = date("Y-m-d");
$statut = "Non lu";


// Préparation de la requête
$requete = $connexion->prepare(
    "INSERT INTO messages
    (NOM, EMAIL, TELEPHONE, SUJET, MESSAGE, `DATE_D'ENVOIE`, STATUT)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
);


// Vérification de la requête
if (!$requete) {
    http_response_code(500);
    echo "Erreur lors de la préparation de la requête.";
    exit();
}


// Liaison des données
$requete->bind_param(
    "sssssss",
    $nom,
    $email,
    $telephone,
    $sujet,
    $message,
    $date_envoi,
    $statut
);


// Enregistrement
if ($requete->execute()) {

    echo "success";

} else {

    http_response_code(500);
    echo "Erreur lors de l'enregistrement du message.";

}


// Fermeture
$requete->close();
$connexion->close();

?>