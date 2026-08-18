<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/connexion.php";


/* ===============================
   MARQUER UN MESSAGE COMME LU
================================ */

if (isset($_GET["lire"])) {

    $id = intval($_GET["lire"]);

    $requete = $connexion->prepare(
        "UPDATE messages SET STATUT = 'Lu' WHERE IDENTIFIANT = ?"
    );

    $requete->bind_param("i", $id);
    $requete->execute();
    $requete->close();

    header("Location: messages.php");
    exit();
}


/* ===============================
   MARQUER UN MESSAGE COMME RÉPONDU
================================ */

if (isset($_GET["repondu"])) {

    $id = intval($_GET["repondu"]);

    $requete = $connexion->prepare(
        "UPDATE messages SET STATUT = 'Répondu' WHERE IDENTIFIANT = ?"
    );

    $requete->bind_param("i", $id);
    $requete->execute();
    $requete->close();

    header("Location: messages.php");
    exit();
}


/* ===============================
   SUPPRIMER UN MESSAGE
================================ */

if (isset($_GET["supprimer"])) {

    $id = intval($_GET["supprimer"]);

    $requete = $connexion->prepare(
        "DELETE FROM messages WHERE IDENTIFIANT = ?"
    );

    $requete->bind_param("i", $id);
    $requete->execute();
    $requete->close();

    header("Location: messages.php");
    exit();
}


/* ===============================
   RÉCUPÉRER LES MESSAGES
================================ */

$requete = "
    SELECT *
    FROM messages
    ORDER BY `DATE_D'ENVOIE` DESC, IDENTIFIANT DESC
";

$resultat = $connexion->query($requete);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Messages - ALLIANCE SUD-EST</title>

<script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>

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
}

.logo {
    font-size: 23px;
    font-weight: bold;
}

.header a {
    color: white;
    text-decoration: none;
}


/* ================= CONTAINER ================= */

.container {
    width: 94%;
    max-width: 1300px;
    margin: 35px auto;
}

.title {
    margin-bottom: 25px;
}

.title h1 {
    color: #0A3663;
    margin-bottom: 8px;
}

.title p {
    color: #777;
}


/* ================= MESSAGE ================= */

.message-card {
    background: white;
    border-radius: 12px;
    margin-bottom: 20px;
    padding: 22px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-left: 5px solid #0A3663;
}

.message-card.non-lu {
    border-left-color: #e67e22;
}

.message-card.repondu {
    border-left-color: #2D9CD8;
}


/* ================= INFORMATIONS ================= */

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 18px;
}

.client h2 {
    color: #0A3663;
    font-size: 20px;
    margin-bottom: 5px;
}

.client p {
    color: #666;
    font-size: 14px;
}


/* ================= STATUT ================= */

.statut {
    padding: 7px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.statut.non-lu {
    background: #fff0df;
    color: #d35400;
}

.statut.lu {
    background: #e3f7e9;
    color: #218c4a;
}

.statut.repondu {
    background: #e4f3fb;
    color: #1675a5;
}


/* ================= CONTENU ================= */

.informations {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 18px;
}

.info {
    background: #f7f9fc;
    padding: 12px;
    border-radius: 7px;
}

.info strong {
    display: block;
    color: #0A3663;
    font-size: 13px;
    margin-bottom: 5px;
}

.info span {
    color: #555;
    font-size: 14px;
}

.message-text {
    background: #f7f9fc;
    padding: 18px;
    border-radius: 8px;
    line-height: 1.6;
    margin-bottom: 20px;
}

.message-text strong {
    display: block;
    color: #0A3663;
    margin-bottom: 8px;
}


/* ================= BOUTONS ================= */

.actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    display: inline-block;
    padding: 10px 16px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-size: 13px;
    font-weight: bold;
    border: none;
    cursor: pointer;
    transition: 0.2s;
}

.btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}


/* Répondre */

.btn-repondre {
    background: #2D9CD8;
}


/* Lire */

.btn-lire {
    background: #0A3663;
}


/* Supprimer */

.btn-supprimer {
    background: #c0392b;
}


/* Retour */

.btn-retour {
    background: #555;
}


/* ================= AUCUN MESSAGE ================= */

.empty {
    background: white;
    padding: 50px;
    text-align: center;
    border-radius: 12px;
    color: #777;
}


/* ================= FENÊTRE RÉPONSE ================= */

.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.55);
    padding: 30px 15px;
    overflow-y: auto;
}

.modal-content {
    background: white;
    width: 100%;
    max-width: 650px;
    margin: 40px auto;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.25);
    position: relative;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-header h2 {
    color: #0A3663;
}

.close {
    font-size: 28px;
    cursor: pointer;
    color: #777;
    font-weight: bold;
}

.close:hover {
    color: #c0392b;
}


/* ================= FORMULAIRE RÉPONSE ================= */

.reply-group {
    margin-bottom: 15px;
}

.reply-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
    color: #0A3663;
}

.reply-group input,
.reply-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 7px;
    font-family: Arial, sans-serif;
    font-size: 14px;
}

.reply-group textarea {
    min-height: 180px;
    resize: vertical;
}

.reply-group input:focus,
.reply-group textarea:focus {
    outline: none;
    border-color: #2D9CD8;
}

.btn-envoyer {
    background: #0A3663;
    width: 100%;
    padding: 13px;
    font-size: 15px;
}


/* ================= MESSAGE DE RÉSULTAT ================= */

#reply-result {
    margin-top: 15px;
    padding: 12px;
    border-radius: 7px;
    display: none;
    text-align: center;
    font-weight: bold;
}

.success {
    background: #e3f7e9;
    color: #218c4a;
}

.error {
    background: #fdeaea;
    color: #c0392b;
}


/* ================= MOBILE ================= */

@media (max-width: 750px) {

    .header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }

    .message-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .informations {
        grid-template-columns: 1fr;
    }

    .actions {
        flex-direction: column;
    }

    .btn {
        text-align: center;
        width: 100%;
    }

    .modal-content {
        margin: 10px auto;
        padding: 20px;
    }

}

</style>

</head>


<body>


<header class="header">

    <div class="logo">
        ALLIANCE SUD-EST
    </div>

    <a href="dashboard.php">
        ← Tableau de bord
    </a>

</header>


<main class="container">


    <div class="title">

        <h1>Messages reçus</h1>

        <p>
            Consultez et gérez les demandes envoyées depuis votre site.
        </p>

    </div>


    <?php if ($resultat && $resultat->num_rows > 0): ?>


        <?php while ($message = $resultat->fetch_assoc()): ?>


            <div class="message-card 
                <?php echo ($message["STATUT"] === "Non lu") ? "non-lu" : ""; ?>
                <?php echo ($message["STATUT"] === "Répondu") ? "repondu" : ""; ?>">


                <div class="message-header">

                    <div class="client">

                        <h2>
                            <?php echo htmlspecialchars($message["NOM"]); ?>
                        </h2>

                        <p>
                            <?php echo htmlspecialchars($message["DATE_D'ENVOIE"]); ?>
                        </p>

                    </div>


                    <?php if ($message["STATUT"] === "Non lu"): ?>

                        <span class="statut non-lu">
                            ● Non lu
                        </span>

                    <?php elseif ($message["STATUT"] === "Répondu"): ?>

                        <span class="statut repondu">
                            ● Répondu
                        </span>

                    <?php else: ?>

                        <span class="statut lu">
                            ● Lu
                        </span>

                    <?php endif; ?>

                </div>


                <div class="informations">


                    <div class="info">

                        <strong>Email</strong>

                        <span>
                            <?php echo htmlspecialchars($message["EMAIL"]); ?>
                        </span>

                    </div>


                    <div class="info">

                        <strong>Téléphone</strong>

                        <span>
                            <?php echo htmlspecialchars($message["TELEPHONE"]); ?>
                        </span>

                    </div>


                    <div class="info">

                        <strong>Sujet</strong>

                        <span>
                            <?php echo htmlspecialchars($message["SUJET"]); ?>
                        </span>

                    </div>


                </div>


                <div class="message-text">

                    <strong>Message</strong>

                    <?php echo nl2br(htmlspecialchars($message["MESSAGE"])); ?>

                </div>


                <div class="actions">


                    <!-- RÉPONDRE -->

                    <button
                        type="button"
                        class="btn btn-repondre"
                        onclick="ouvrirReponse(
                            '<?php echo htmlspecialchars($message["EMAIL"], ENT_QUOTES); ?>',
                            '<?php echo htmlspecialchars($message["NOM"], ENT_QUOTES); ?>',
                            '<?php echo htmlspecialchars($message["SUJET"], ENT_QUOTES); ?>',
                            '<?php echo $message["IDENTIFIANT"]; ?>'
                        )"
                    >
                        ✉️ Répondre
                    </button>


                    <!-- MARQUER COMME LU -->

                    <?php if ($message["STATUT"] === "Non lu"): ?>

                        <a
                            href="messages.php?lire=<?php echo $message["IDENTIFIANT"]; ?>"
                            class="btn btn-lire"
                        >
                            ✓ Marquer comme lu
                        </a>

                    <?php endif; ?>


                    <!-- SUPPRIMER -->

                    <a
                        href="messages.php?supprimer=<?php echo $message["IDENTIFIANT"]; ?>"
                        class="btn btn-supprimer"
                        onclick="return confirm('Voulez-vous vraiment supprimer ce message ?');"
                    >
                        🗑 Supprimer
                    </a>


                </div>


            </div>


        <?php endwhile; ?>


    <?php else: ?>


        <div class="empty">

            <h2>Aucun message reçu</h2>

            <p>
                Les nouveaux messages envoyés depuis le formulaire
                apparaîtront ici.
            </p>

        </div>

    <?php endif; ?>


    <br>

    <a href="dashboard.php" class="btn btn-retour">
        ← Retour au dashboard
    </a>


</main>


<!-- ===============================
     FENÊTRE DE RÉPONSE
================================ -->

<div id="replyModal" class="modal">

    <div class="modal-content">


        <div class="modal-header">

            <h2>Répondre au visiteur</h2>

            <span class="close" onclick="fermerReponse()">
                ×
            </span>

        </div>


        <form id="reply-form">


            <div class="reply-group">

                <label>Destinataire</label>

                <input
                    type="email"
                    id="reply-email"
                    name="to_email"
                    readonly
                >

            </div>


            <div class="reply-group">

                <label>Nom du destinataire</label>

                <input
                    type="text"
                    id="reply-name"
                    name="to_name"
                    readonly
                >

            </div>


            <div class="reply-group">

                <label>Objet</label>

                <input
                    type="text"
                    id="reply-subject"
                    name="subject"
                    required
                >

            </div>


            <div class="reply-group">

                <label>Votre réponse</label>

                <textarea
                    id="reply-message"
                    name="message"
                    placeholder="Écrivez votre réponse ici..."
                    required
                ></textarea>

            </div>


            <button
                type="submit"
                class="btn btn-envoyer"
                id="send-reply"
            >
                ✉️ Envoyer la réponse
            </button>


            <div id="reply-result"></div>


        </form>

    </div>

</div>


<script>

/* ===============================
   INITIALISATION EMAILJS
================================ */

(function() {

    emailjs.init("97HiaqvNMCTI4f2E6");

})();


/* ===============================
   VARIABLE ID DU MESSAGE
================================ */

var messageId = null;


/* ===============================
   OUVRIR LA FENÊTRE
================================ */

function ouvrirReponse(email, nom, sujet, id) {

    messageId = id;

    document.getElementById("reply-email").value = email;

    document.getElementById("reply-name").value = nom;

    document.getElementById("reply-subject").value =
        "Re: " + sujet;

    document.getElementById("reply-message").value = "";

    document.getElementById("reply-result").style.display = "none";

    document.getElementById("replyModal").style.display = "block";

}


/* ===============================
   FERMER LA FENÊTRE
================================ */

function fermerReponse() {

    document.getElementById("replyModal").style.display = "none";

}


/* ===============================
   FERMER EN CLIQUANT À L'EXTÉRIEUR
================================ */

window.onclick = function(event) {

    var modal = document.getElementById("replyModal");

    if (event.target === modal) {

        fermerReponse();

    }

};


/* ===============================
   ENVOYER LA RÉPONSE
================================ */

document.getElementById("reply-form").addEventListener(
    "submit",
    function(e) {

        e.preventDefault();

        var bouton = document.getElementById("send-reply");

        var resultat = document.getElementById("reply-result");

        bouton.disabled = true;

        bouton.innerHTML = "⏳ Envoi en cours...";


        var email =
            document.getElementById("reply-email").value;

        var nom =
            document.getElementById("reply-name").value;

        var sujet =
            document.getElementById("reply-subject").value;

        var message =
            document.getElementById("reply-message").value;


        var templateParams = {

            to_email: email,

            to_name: nom,

            subject: sujet,

            message: message,

            from_name: "ALLIANCE SUD-EST"

        };


        /* ===============================
           ENVOI EMAILJS
        ================================ */

        emailjs.send(
            "service_yupcock",
            "template_gw91ctn",
            templateParams
        )

        .then(function() {

            resultat.className = "success";

            resultat.innerHTML =
                "✓ Votre réponse a été envoyée avec succès.";

            resultat.style.display = "block";


            /* ===============================
               ENREGISTRER LE STATUT RÉPONDU
            ================================ */

            if (messageId !== null) {

                window.location.href =
                    "messages.php?repondu=" + messageId;

            }

        })

        .catch(function(error) {

            resultat.className = "error";

            resultat.innerHTML =
                "✕ Erreur lors de l'envoi de la réponse.";

            resultat.style.display = "block";

            console.log(error);

            bouton.disabled = false;

            bouton.innerHTML =
                "✉️ Envoyer la réponse";

        });

    }

);

</script>


</body>

</html>