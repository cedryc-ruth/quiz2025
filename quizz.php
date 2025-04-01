<?php
session_start();

//Déclaraiton des variables
$questionsReponses = file("questionsReponses.csv",FILE_IGNORE_NEW_LINES);
//var_dump($questionsReponses);

foreach($questionsReponses as &$questionReponse) {
	$questionReponse = explode("|",$questionReponse,2);		//var_dump($questionReponse);
}
unset($questionReponse);
//var_dump($questionsReponses);die;

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

				file_put_contents("palmares.csv",$data,FILE_APPEND);

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