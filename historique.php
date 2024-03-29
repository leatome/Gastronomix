<?php
    require 'Function.php';
    session_start();

    if (isset($_GET['pseudo'])) {
        $pseudo = $_GET['pseudo'];
        $_SESSION['pseudo_user'] = $pseudo;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Historique</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="Function.js"></script>
    </head>
    <body>
        <?php
            if (isset($_SESSION['pseudo_user'])) {
                if($_SESSION['pseudo_user'] == "admin" || $_SESSION['pseudo_user'] == "Admin") {
                    MenuDeroulantAdmin($pseudo);
                }else {
                    MenuDeroulantCompte($pseudo);
                }
                
                MenuDeroulantConnecter($pseudo);
                RechercheAvanceeConnecter($pseudo);
            } else {
                MenuDeroulantDeconnecter();
                RechercheAvancee();

                echo '<a href="http://localhost/gastronomix/connexion.php"><button id="CompteButton" class="Button">Connexion</button></a>';
            }
        ?>

        <button id="ThemeButton" class="Button" onclick="ChangeBackgroundColor()">🌓</button>

        <h1>GastronoMix</h1>

        <?php
            if (isset($_SESSION['pseudo_user'])) {
                if($_SESSION['pseudo_user'] == "admin" || $_SESSION['pseudo_user'] == "Admin") {
                    MenuDeroulantAdmin($pseudo);
                }else {
                    MenuDeroulantCompte($pseudo);
                }
            } else {
                echo '<a href="http://localhost/gastronomix/connexion.php"><button id="CompteButton" class="Button">Connexion</button></a>';
            }
        ?>

        <br><br>

        <h2>Avis</h2>

        <?php
            if (isset($_SESSION['pseudo_user'])) {
                $pseudo = $_SESSION['pseudo_user'];

                $mysqli = ConnectionDatabase();

                // Récupérer les avis de l'utilisateur pour une recette donnée
                $query = "SELECT h.id_recette, r.titre, r.image_recette, h.avis_historique
                            FROM historique h
                            JOIN recette r ON r.id_recette = h.id_recette
                            JOIN user u ON u.id_user = h.id_user
                            WHERE u.pseudo_user = '$pseudo';";
                //la requete sql est stockée dans une variable $query
                $result = $mysqli->query($query);
                //on fait une condition pour vérifier si la requete est bien exécutée et si le nombre de lignes est supérieur à 0
                if ($result && $result->num_rows > 0) {
                    //assigner les valeurs de la requete dans un tableau associatif
                    
                    echo '<div class="container">';
                    while ($row = $result->fetch_assoc()) {
                        //les valeurs sont stockées dans des variables
                        $id_recette = $row['id_recette'];
                        $titre = $row['titre'];
                        $image_recette = $row['image_recette'];
                        $avis_historique = $row['avis_historique'];
                        $newtitre = str_replace("'", "_", $titre);
                        
                        echo '<div class="recette zoom">';
                        // Image cliquable
                        echo '<a href="http://localhost/gastronomix/recette.php?pseudo=' . $pseudo . '&recherche=' . $newtitre . '">';
                        //echo '<img src="' . $image_recette . '" alt="Avis: ' . $avis_historique . '/5"><br>';
                        //echo '<img src="' . $image_recette . '" alt="Image de la recette"><br>';
                        //echo '<button id="avis" class="Button">' . $avis_historique . '/5</button>';
                        echo '<div style="position: relative; display: inline-block;">';
                        echo '<img src="' . $image_recette . '" alt="Image de la recette">';
                        echo '<button id="avis" class="Button" style="position: relative; bottom: 55px; left: 26%; width: 50px; height: -50%; font-size: 15px; transform: translate(50%, 50%);">' . $avis_historique . '/5</button>';
                        echo '</div>';
                        echo '</a>';
                        echo '<div class="nom-recette">';
                        // Titre cliquable
                        echo '<a href="http://localhost/gastronomix/recette.php?pseudo=' . $pseudo . '&recherche=' . $newtitre . '">' . $titre . '</a><br>';
                        echo '</div>';
                        echo '</div>';  
                    }
                } else { 
                    echo "<p>Aucun avis trouvé dans l'historique.</p>";
                }
                echo '</div>';
                echo '</div>';

                $mysqli->close();
            } else {
                echo "<p>Veuillez vous connecter pour accéder à votre historique d'avis.</p>";
            }
        ?>
    </body>
</html>
