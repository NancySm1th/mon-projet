<?php

session_start();

require_once "../config/connexion.php";

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom_utilisateur = trim($_POST["nom_utilisateur"]);
    $mot_de_passe = $_POST["mot_de_passe"];

    $requete = $connexion->prepare(
        "SELECT * FROM administrateurs WHERE NOM_UTILISATEUR = ?"
    );

    if ($requete) {

        $requete->bind_param("s", $nom_utilisateur);
        $requete->execute();

        $resultat = $requete->get_result();

        if ($resultat->num_rows === 1) {

            $admin = $resultat->fetch_assoc();

            if (password_verify($mot_de_passe, $admin["MOT_DE_PASSE"])) {

                $_SESSION["admin_id"] = $admin["IDENTIFIANT"];
                $_SESSION["admin_nom"] = $admin["NOM"];

                header("Location: dashboard.php");
                exit();

            } else {

                $erreur = "Nom d'utilisateur ou mot de passe incorrect.";

            }

        } else {

            $erreur = "Nom d'utilisateur ou mot de passe incorrect.";

        }

        $requete->close();

    } else {

        $erreur = "Une erreur est survenue lors de la connexion.";

    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Connexion administrateur - ALLIANCE SUD-EST</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {

    min-height: 100vh;

    font-family: Arial, sans-serif;

    background:
        linear-gradient(
            rgba(10, 54, 99, 0.90),
            rgba(10, 54, 99, 0.90)
        ),
        linear-gradient(135deg, #0A3663, #2D9CD8);

    display: flex;

    justify-content: center;

    align-items: center;

    padding: 20px;
}


/* ================= CONNEXION ================= */

.login-container {

    width: 100%;

    max-width: 430px;

}


/* ================= LOGO ================= */

.logo {

    text-align: center;

    color: white;

    margin-bottom: 25px;

}

.logo h1 {

    font-size: 28px;

    letter-spacing: 1px;

    margin-bottom: 7px;

}

.logo p {

    font-size: 14px;

    opacity: 0.85;

}


/* ================= CARTE ================= */

.login-box {

    background: white;

    padding: 40px;

    border-radius: 15px;

    box-shadow:
        0 15px 40px rgba(0, 0, 0, 0.25);

}


/* ================= TITRE ================= */

.login-box h2 {

    color: #0A3663;

    text-align: center;

    margin-bottom: 8px;

    font-size: 24px;

}

.subtitle {

    text-align: center;

    color: #777;

    font-size: 14px;

    margin-bottom: 30px;

}


/* ================= CHAMPS ================= */

.form-group {

    margin-bottom: 20px;

}

.form-group label {

    display: block;

    color: #333;

    font-size: 14px;

    font-weight: bold;

    margin-bottom: 7px;

}

.form-group input {

    width: 100%;

    padding: 13px 14px;

    border: 1px solid #d5dce5;

    border-radius: 7px;

    font-size: 15px;

    outline: none;

    transition: 0.3s;

}

.form-group input:focus {

    border-color: #2D9CD8;

    box-shadow: 0 0 0 3px rgba(45, 156, 216, 0.12);

}


/* ================= ERREUR ================= */

.error {

    background: #fdeaea;

    color: #c0392b;

    padding: 12px;

    border-radius: 7px;

    font-size: 14px;

    margin-bottom: 20px;

    text-align: center;

}


/* ================= BOUTON ================= */

.btn-login {

    width: 100%;

    padding: 14px;

    border: none;

    border-radius: 7px;

    background: #0A3663;

    color: white;

    font-size: 15px;

    font-weight: bold;

    cursor: pointer;

    transition: 0.3s;

}

.btn-login:hover {

    background: #14558f;

    transform: translateY(-1px);

}


/* ================= FOOTER ================= */

.login-footer {

    text-align: center;

    margin-top: 22px;

    color: #999;

    font-size: 12px;

}


/* ================= MOBILE ================= */

@media (max-width: 500px) {

    .login-box {

        padding: 30px 22px;

    }

    .logo h1 {

        font-size: 23px;

    }

}

</style>

</head>


<body>


<div class="login-container">


    <!-- LOGO -->

    <div class="logo">

        <h1>ALLIANCE SUD-EST</h1>

        <p>Espace d'administration</p>

    </div>


    <!-- FORMULAIRE -->

    <div class="login-box">

        <h2>Connexion</h2>

        <p class="subtitle">
            Connectez-vous à votre espace administrateur
        </p>


        <?php if ($erreur !== ""): ?>

            <div class="error">
                <?php echo htmlspecialchars($erreur); ?>
            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="form-group">

                <label for="nom_utilisateur">
                    Nom d'utilisateur
                </label>

                <input
                    type="text"
                    id="nom_utilisateur"
                    name="nom_utilisateur"
                    placeholder="Entrez votre identifiant"
                    required
                >

            </div>


            <div class="form-group">

                <label for="mot_de_passe">
                    Mot de passe
                </label>

                <input
                    type="password"
                    id="mot_de_passe"
                    name="mot_de_passe"
                    placeholder="Entrez votre mot de passe"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-login"
            >
                Se connecter
            </button>


        </form>


        <div class="login-footer">

            © 2026 ALLIANCE SUD-EST SARL

        </div>

    </div>

</div>


</body>

</html>