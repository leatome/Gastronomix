<?php
    require 'Function.php';
    
    if (isset($_GET['pseudo'])) {
    $pseudo = $_GET['pseudo'];
    $_SESSION['pseudo_user'] = $pseudo;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>GastronoMix</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="Function.js"></script>
    </head>
    <body>
        <?php
            if (isset($_SESSION['pseudo_user'])) {
                if($_SESSION['pseudo_user'] == "admin" || $_SESSION['pseudo_user'] == "Admin") {
                    MenuDeroulantAdmin($pseudo);
                    MenuDeroulantConnecter($pseudo);
                    RechercheAvanceeConnecter($pseudo);
                } else {
                    echo "Tu n'es pas autoriser à accéder à cette page !";
                    header("Refresh: 2; url=http://localhost/gastronomix/Accueil.php");
                }
            } else {
                echo "Tu n'es pas autoriser à accéder à cette page !";
                header("Refresh: 2; url=http://localhost/gastronomix/Accueil.php");
            }
        ?>

        <button id="ThemeButton" class="Button" onclick="ChangeBackgroundColor()">🌓</button>

        <h1>GastronoMix</h1>

        <h2>Confirmation de l'ajout d'une nouvelle recette</h2>

        <?php
            $mysqli = ConnectionDatabase();

            //recupere les valeurs du formulaire

            $titre = $_POST['titre'];
            $source = $_POST['source'];
            $image_recette = $_POST['image_recette'];
            $nb_personne = $_POST['nb_personne'];
            $temps_prep_recette = $_POST['temps_prep_recette'];
            $temps_total_recette = $_POST['temps_total_recette'];
            $difficulte = $_POST['difficulte'];
            $quantite_ingredient = $_POST['quantite_ingredient'];
            $etapes = $_POST['etapes'];

            $regroupement_etapes = [];
            $regroupement_quantite = [];
            $regroupement_ingredient = [];
            $regroupement_unite = [];

            $query_verif_titre = "SELECT * FROM recette WHERE titre = \"$titre\";";
            $result_verif_titre = $mysqli->query($query_verif_titre);

            //recupere les resultats sous forme de tableau
            $result_verif_titre = $result_verif_titre->fetch_assoc();

            if($result_verif_titre == false){
                // Ne prend pas en compte les éléments vide du tableau pour les etapes
                foreach ($etapes as $etape) {
                    if ($etape == "") {
                        continue;
                    } else {
                        array_push($regroupement_etapes, $etape);
                    }
                }

                //recupere l'id de la recette
                $query_id_titre = "SELECT * FROM recette WHERE titre = '$titre';";

                $result_id_titre = $mysqli->query($query_id_titre);
                $result_id_recette = $result_id_titre->fetch_assoc();
                $id_recette = $result_id_recette['id_recette'];

                
                // Insert les données dans la database

                $query_ajout_recette = "UPDATE recette SET titre = \"$titre\", source = \"$source\", image_recette = \"$image_recette\", nb_personne = \"$nb_personne\", temps_prep_recette = \"$temps_prep_recette\", temps_total_recette = \"$temps_total_recette\", difficulte = \"$difficulte\"
                                        WHERE id_recette = \"$id_recette\";";

                $result_ajout_recette = $mysqli->prepare($query_ajout_recette);
                
                //insert les etapes dans la database
                foreach($regroupement_etapes as $key => $etape) {
                    $id_etape = $key + 1;
                    $query_ajout_etape = "UPDATE etape SET id_etape = \"$id_etape\", texte_etape = \"$etape\" 
                                        WHERE id_recette = \"$id_recette\";"; 
                    
                    $result_ajout_etape = $mysqli->query($query_ajout_etape);
                }


                //recupere les quantites de la recette sans les modifications
                $query_ancient_quantite = "SELECT * FROM quantite WHERE id_recette = \"$id_recette\" ;";
                $result_ancient_quantite = $mysqli->query($query_ingredient);

                $result_ancient_quantite = $result_ancient_quantite->fetch_assoc();
                $ancient_quantite = $result_ancient_quantite['quantite'];

                var_dump($ancient_quantite);

                foreach ($quantite_ingredient as $key => $quantite) {
                    // Séparer les chaînes de caractere par la virgule
                    $tab_quantite = explode(',', $quantite);

                    if ($quantite == "") {
                        continue;
                    } else if($tab_quantite[0] == 0) {
                        $tab_quantite[0] = "";
                    } else if($tab_quantite[1] == 0) {
                        $tab_quantite[1] = "";
                    } else if($tab_quantite[2] == 0) {
                        $tab_quantite[2] = "";
                    } else if($tab_quantite[3] == 0) {
                        $tab_quantite[3] = "";
                    }

                    $quantite = $tab_quantite[0];
                    $ingredient = $tab_quantite[1];
                    $unite = $tab_quantite[2];
                    $tag = $tab_quantite[3];

                    //verifie si l'unite existe deja dans la database
                    $query_unite = "SELECT * FROM unite WHERE libelle_unite = \"$unite\";";
                    $result_unite = $mysqli->query($query_unite);

                    $result_unite = $result_unite->fetch_assoc();
                    
                    //si l'unite n'existe pas, on l'ajoute
                    if($result_unite == false && $unite != "") {
                        $query_ajout_unite = "INSERT INTO unite(libelle_unite)
                                            VALUES(\"$unite\");";
        
                        $result_ajout_unite = $mysqli->query($query_ajout_unite);

                        $query_id_unite = "SELECT id_unite FROM unite where libelle_unite = \"$unite\";";
                        $result_id_unite = $mysqli->query($query_unite);

                        $result_unite = $result_id_unite->fetch_assoc();
                    } 

                    $id_unite = $result_unite['id_unite'];
                    

                    //verifie si l'ingredient existe deja dans la database
                    $query_ingredient = "SELECT * FROM ingredient WHERE nom_ingredient = \"$ingredient\" AND id_unite = \"$id_unite\" ;";
                    $result_ingredient = $mysqli->query($query_ingredient);

                    $result_ingredient = $result_ingredient->fetch_assoc();

                    //si l'ingredients n'existe pas, on l'ajoute
                    if($result_ingredient == false) {
                        $query_ingredient = "INSERT INTO ingredient(nom_ingredient, ingredients_recherche, id_unite)
                                            VALUES(\"$ingredient\", \"$tag\",\"$id_unite\");";

                        $result_ingredient = $mysqli->query($query_ingredient);

                        $query_ingredient = "SELECT * FROM ingredient WHERE nom_ingredient = \"$ingredient\" AND id_unite = \"$id_unite\" ;";
                        $result_ingredient = $mysqli->query($query_ingredient);

                        $result_ingredient = $result_ingredient->fetch_assoc();
                    }
                    
                    //recupere l'id_recette
                    $id_ingredient = $result_ingredient['id_ingredient'];


                    //ajout de la quantite
                    $query_ajout_quantite = "UPDATE quantite SET id_ingredient = \"$id_ingredient\", quantite = \"$quantite\"
                                            WHERE id_recette = \"$id_recette\" AND id_ingredient = \"$ancient_quantite[$key]\";";

                    $result_ajout_quantite = $mysqli->query($query_ajout_quantite);
                }
            } else {
                echo "Titre déjà existant !";
                header("Location: Modification_Recette.php?pseudo='$pseudo'");
            }

            if ($result_ajout_recette->execute()) {
                echo "Modification avec succès !";
            } else {
                echo "Erreur dans la modification de la recette " ;
                header("Location: Modification_Recette.php?pseudo='$pseudo'&erreur");
            }

            $mysqli->close();
        ?>
    </body>
</html>
