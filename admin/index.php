<?php
require('../config.php');

session_start();

$message = '';

//Sécuriser l'accès
if(empty($_SESSION['connected']) || empty($_SESSION['status']) || $_SESSION['status']!='admin') {
    //Solution1: afficher un message d'erreur
/*    echo '<div style="background-color:red;color:white;"><p>Vous n\'avez pas le droit d\'accéder à cette page! ';
    echo '<a href="'.APP_URL.'/quizz.php">Retour au quiz</a>.</p></div>';
    die;
*/

    //Solution 2: redirection
    header('Location: '.APP_URL.'/quizz.php?erreur=401',null,302);
    exit;
}

//Traitement des dommandes
if(isset($_POST["btUpload"])) {
    if($_FILES['dataset']['size']<300000 
			&& $_FILES['dataset']['type']=='text/csv') {	//Validation
		$source = $_FILES['dataset']['tmp_name'];
		$destination = getcwd().'/tmp/'.basename($_FILES['dataset']['name']);

		if(move_uploaded_file($source,$destination)) {	//Déplacement
            $message = "Le fichier est valide, et a été téléchargé avec succès.";

            //Lire le fichier
            $tab = file($destination, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
            
            //Insérer chaque ligne dans la table quiz de la BD
                // Connexion au serveur de bases de données
            $mysql = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

                // Préparation de la requête
            $query = "INSERT INTO `quizz` (`question`, `reponse`) VALUES ";
            
            if(sizeof($tab)>0) {
                foreach($tab as $qr) {                  // ('Q1', 'R1'), ('Q2', 'R2')
                    $tabQR = explode("|",$qr);

                    // Nettoyer les données entrantes
                    $question = mysqli_real_escape_string($mysql, $tabQR[0]);
                    $reponse = mysqli_real_escape_string($mysql, $tabQR[1]);

                    $query .= "('$question', '$reponse'), ";
                }

                $query = substr($query,0,-2);           //var_dump($query);die;
            }

            // Envoi de la requête
            $result = mysqli_query($mysql, $query);

            // Vérification du résultat
            if($result && mysqli_affected_rows($mysql)>0) {
                $message = "Insertion réussie.";
            } else {
                $message = "Une erreur est survenue lors de l'insertion.";
            }

            // Déconnexion du serveur de bases de données
            mysqli_close($mysql);
        } else {
            $message = "Erreur lors du téléchargement !";
        }
	}
;
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Administration</title>
</head>
<body>
<nav>
    <ul>
        <li><a href="../quizz.php">Retour au quiz</a></li>
        <li><a href="profil.php">Profil</a></li>
    <?php if($_SESSION['status']=='admin') { ?>
        <li><a href="users.php">Utilisateurs</a></li>
    <?php } ?>
    </ul>
</nav>
<h1>Administration</h1>
<h2>Ajouter des questions au quiz</h2>

<form enctype="multipart/form-data" method="post" action="<?= $_SERVER["PHP_SELF"] ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="30000">

    <input type="file" name="dataset">
    <button name="btUpload">Charger</button>
</form>
<div><?= $message ?></div>
</body>
</html>