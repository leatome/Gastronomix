<?php
    require 'Function.php';

    $mysqli = ConnectionDatabase();

    // Récupérer les données du formulaire
    $pseudo_user = $_POST['pseudo_user'];
    $mail_user = $_POST['mail_user'];
    $password_user = $_POST['password_user'];


    // Requête pour vérifier les informations de connexion dans la base de données
    $query = "SELECT * FROM user WHERE pseudo_user = '$pseudo_user' AND mail_user = '$mail_user' AND password_user = '$password_user'";
    $result_query = $mysqli->query($query);

    if ($result_query->num_rows > 0) {
        echo "<p>Connexion réussie.</p>";

        header("Location: Accueil.php?pseudo=$pseudo_user");
        exit();
    } else {
        echo "<p>Nom d'utilisateur ou mot de passe incorrect.</p>";

        header("Location: connexion.php?error=connexion");
        exit();
    }

    $mysqli->close();
?>