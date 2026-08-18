<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/connexion.php";


/* ===============================
   STATISTIQUES DES MESSAGES
================================ */

// Nombre total de messages
$requete_total = "SELECT COUNT(*) AS total FROM messages";
$resultat_total = $connexion->query($requete_total);
$total = $resultat_total->fetch_assoc()["total"];


// Nombre de messages non lus
$requete_non_lus = "SELECT COUNT(*) AS non_lus FROM messages WHERE STATUT = 'Non lu'";
$resultat_non_lus = $connexion->query($requete_non_lus);
$non_lus = $resultat_non_lus->fetch_assoc()["non_lus"];


// Nombre de messages lus
$requete_lus = "SELECT COUNT(*) AS lus FROM messages WHERE STATUT = 'Lu'";
$resultat_lus = $connexion->query($requete_lus);
$lus = $resultat_lus->fetch_assoc()["lus"];


// Nombre de messages répondus
$requete_repondus = "SELECT COUNT(*) AS repondus FROM messages WHERE STATUT = 'Répondu'";
$resultat_repondus = $connexion->query($requete_repondus);
$repondus = $resultat_repondus->fetch_assoc()["repondus"];

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Tableau de bord - ALLIANCE SUD-EST</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f4f7fb;
    color: #333;
}


/* ================= HEADER ================= */

.header {
    background: linear-gradient(135deg, #0A3663, #14558f);
    color: white;
    padding: 20px 6%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 12px rgba(0,0,0,0.15);
}

.logo {
    font-size: 24px;
    font-weight: bold;
    letter-spacing: 1px;
}

.admin {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}


/* ================= CONTAINER ================= */

.container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
}


/* ================= BIENVENUE ================= */

.welcome {
    margin-bottom: 30px;
}

.welcome h2 {
    color: #0A3663;
    font-size: 28px;
    margin-bottom: 8px;
}

.welcome p {
    color: #777;
}


/* ================= CARTES ================= */

.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 35px;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
    transition: 0.3s;
    border-left: 5px solid #0A3663;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.card h3 {
    font-size: 15px;
    color: #777;
    margin-bottom: 15px;
}

.number {
    font-size: 40px;
    font-weight: bold;
    color: #0A3663;
}


/* Carte non lu */

.card.non-lu {
    border-left-color: #e67e22;
}

.card.non-lu .number {
    color: #d35400;
}


/* Carte lu */

.card.lu {
    border-left-color: #218c4a;
}

.card.lu .number {
    color: #218c4a;
}


/* Carte répondu */

.card.repondu {
    border-left-color: #2D9CD8;
}

.card.repondu .number {
    color: #2D9CD8;
}


/* ================= ACTIONS ================= */

.actions {
    background: white;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
}

.actions h2 {
    color: #0A3663;
    margin-bottom: 10px;
}

.actions p {
    color: #777;
    margin-bottom: 20px;
}


/* ================= BOUTONS ================= */

.btn {
    display: inline-block;
    padding: 13px 22px;
    margin-right: 10px;
    border-radius: 7px;
    text-decoration: none;
    color: white;
    background: #0A3663;
    font-weight: bold;
    transition: 0.3s;
}

.btn:hover {
    background: #14558f;
    transform: translateY(-2px);
}

.logout {
    background: #c0392b;
}

.logout:hover {
    background: #a93226;
}


/* ================= FOOTER ================= */

.footer {
    text-align: center;
    margin-top: 50px;
    padding: 20px;
    color: #888;
    font-size: 13px;
}


/* ================= MOBILE ================= */

@media (max-width: 950px) {

    .cards {
        grid-template-columns: repeat(2, 1fr);
    }

}


@media (max-width: 600px) {

    .header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }

    .cards {
        grid-template-columns: 1fr;
    }

    .welcome h2 {
        font-size: 23px;
    }

    .btn {
        display: block;
        text-align: center;
        margin: 10px 0;
    }

}

</style>

</head>


<body>


<!-- ================= HEADER ================= -->

<header class="header">

    <div class="logo">
        ALLIANCE SUD-EST
    </div>

    <div class="admin">
        👤 Administrateur connecté
    </div>

</header>


<!-- ================= CONTENU ================= -->

<main class="container">


    <!-- BIENVENUE -->

    <section class="welcome">

        <h2>
            Bienvenue,
            <?php echo htmlspecialchars($_SESSION["admin_nom"]); ?> !
        </h2>

        <p>
            Voici un aperçu des messages reçus depuis votre site.
        </p>

    </section>


    <!-- ================= STATISTIQUES ================= -->

    <section class="cards">


        <!-- TOTAL -->

        <div class="card">

            <h3>
                📩 Total des messages
            </h3>

            <div class="number">
                <?php echo $total; ?>
            </div>

        </div>


        <!-- NON LUS -->

        <div class="card non-lu">

            <h3>
                🔔 Messages non lus
            </h3>

            <div class="number">
                <?php echo $non_lus; ?>
            </div>

        </div>


        <!-- LUS -->

        <div class="card lu">

            <h3>
                ✅ Messages lus
            </h3>

            <div class="number">
                <?php echo $lus; ?>
            </div>

        </div>


        <!-- RÉPONDUS -->

        <div class="card repondu">

            <h3>
                💬 Messages répondus
            </h3>

            <div class="number">
                <?php echo $repondus; ?>
            </div>

        </div>


    </section>


    <!-- ================= GESTION ================= -->

    <section class="actions">

        <h2>
            Gestion des messages
        </h2>

        <p>
            Consultez les messages envoyés par les visiteurs du site
            et gérez leur statut.
        </p>


        <a href="messages.php" class="btn">
            📩 Voir les messages
        </a>


        <a href="logout.php" class="btn logout">
            🚪 Se déconnecter
        </a>

    </section>


</main>


<!-- ================= FOOTER ================= -->

<footer class="footer">

    © 2026 ALLIANCE SUD-EST SARL — Administration

</footer>


</body>

</html>