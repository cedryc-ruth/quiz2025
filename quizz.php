<?php
session_start();

require('config.php');

//Accès à la source de données
//Se connecter au serveur de DB
$mysql = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

//Préparer la requête
$query = "SELECT question,reponse FROM quizz";

//Envoyer la requête et récupérer le résultat
$result = mysqli_query($mysql, $query);

//Extraire les données
$questionsReponses = mysqli_fetch_all($result, MYSQLI_NUM);
//var_dump($questionsReponses);die;

//Libérer la mémoire
mysqli_free_result($result);

//Se déconnecter
mysqli_close($mysql);

//Déclaration des variables
$message = "Bienvenue dans notre quiz!";

$statut = null;	//Variable d'état (ternaire)
//Récupération des données envoyées par URL
if(isset($_GET['statut']) && $_GET['statut']=='reponse') {
	$statut = $_GET['statut'];
}

$nroQuestion = 0;
//Récupération des données envoyées par URL
if(isset($_GET['nroQuestion'])) {
	$nroQuestion = $_GET['nroQuestion'];
}

$score = 0;
//Récupération du score sauvé par cookie
if(isset($_COOKIE['score'])) {
	$score = $_COOKIE['score'];
}

$erreurLogin = "";
if(!empty($_COOKIE['erreurLogin'])) {
	$erreurLogin = $_COOKIE['erreurLogin'];
}

$userId = "NULL";
if(isset($_SESSION['userId'])) {
	$userId = $_SESSION['userId'];
}

//Traitement des commandes
if(isset($_GET['btSend']) && $statut=='reponse') {
	$reponseUtilisateur = trim($_GET['reponse']);
	
	if(!empty($reponseUtilisateur)) {	//var_dump($questionsReponses[$nroQuestion][1]);die;
		if($reponseUtilisateur==$questionsReponses[$nroQuestion][1]) {
			if(sizeof($questionsReponses)!=$nroQuestion+1) {
				$message = 'Bravo! <a href="?nroQuestion='.($nroQuestion+1).'&statut=next">Question suivante</a>';
			} else {
				$message = "Félicitations! Votre score est de $score points.";

				//Sauvegarde du score dans un fichier
				$data = [
					$_SESSION['login'] ?? "Anonyme",
					"|",
					$score,
					"|",
					date('d-m-Y',time()),
					"\n"
				];

				//Sauvegarde du score dans la source des données ----
				//file_put_contents("palmares.csv",$data,FILE_APPEND);

				//Se connecter
				$mysql = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

				//Préparer la requête
				$query = "INSERT INTO `user_quiz` (`id`, `user_id`, `score`, `date`) 
					VALUES (NULL, $userId, $score, '".date('Y-m-d H:i:s.v')."');";
				//var_dump($query);die;

				//Envoyer la requête
				$result = mysqli_query($mysql, $query);

				//Traiter le résultat
				if($result && mysqli_affected_rows($mysql)>0) {
					$message = "Sauvegarde du score réussie.";
				} else {
					$message = "Une erreur est survenue lors de la sauvegarde du score!";
				}

				//Se déconnecter
				mysqli_close($mysql);
				//-----

				//Réinitialiser le score
				setcookie("score", 0, time()+(60*60*24));
			}
			
			$score += 2;		//Ajouter 2 points au score
			$statut = 'correct';	//Changer l'état de l'application
		} else {
			$message = "Dommage...";
			$score--;			//Retirer 1 point au score
			$statut = 'incorrect';	//Changer l'état de l'application
		}
		
		//Sauvegarder le score dans un cookie
		setcookie("score", $score, time()+(60*60*24));
	} else {
		$message = "Veuillez entrer une réponse dans le formulaire.";
	}
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Quiz</title>
</head>
<body>
<?php if(empty($_SESSION['connected'])) { ?>
<form action="login.php" method="post">
	<div>
		<label for="login">Login:</label>
		<input type="text" name="login" id="login">
	</div>
	<div>
		<label for="pwd">Mot de passe:</label>
		<input type="password" name="pwd" id="pwd">
	</div>
	<button name="btLogin">Se connecter</button>
</form>
<p><?= $erreurLogin ?></p>
<?php } else { ?>
<form action="login.php" method="post">
	<button name="btLogout">Se déconnecter</button>
</form>
<?php } ?>

<h1>Quiz</h1>
<?php if($statut!='correct') { ?>
<p><?= $questionsReponses[$nroQuestion][0] ?></p>

<form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">
	<fieldset>
		<div>
			<label for="reponse">Réponse:</label>
			<input type="text" name="reponse" id="reponse">	
		</div>
		<input type="hidden" name="nroQuestion" id="nroQuestion" value="<?= $nroQuestion ?>">
		<input type="hidden" name="statut" id="statut" value="reponse">
		<!-- <input type="submit" value="Envoyer"> -->
		<button name="btSend">Envoyer</button>
	</fieldset>
</form>
<?php } ?>

<div id="message"><?= $message; ?></div>
</body>
</html>