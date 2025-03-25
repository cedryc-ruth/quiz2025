<?php
/*
- 1. Créer le template de la page statique (HTML/CSS)
- 2. Afficher la question de façon dynamique (PHP)
- 3. Traiter la réponse envoyée par formulaire (PHP)
	- Si elle est correcte, afficher "Bravo!"
	- Sinon, afficher "Dommage..."
*/

//Déclaraiton des variables
$questionsReponses = [
	[
		"Quelle est la couleur du cheval blanc de Napoléon ?",
		"blanc",
	],
	[
		"Quelle est le nom du cours de PHP ?",
		"wssv",
	],
	[
		"Quelle est le nom de cette école ?",
		"epfc",
	],
	[
		"Quelle est le jour du cours ?",
		"mardi",
	],
];

$message = "Bienvenue dans notre quiz!";

$statut = null;	//Variable d'état (ternaire)
//Récupération des données envoyées par URL
if(isset($_GET['statut']) && $_GET['statut']=='reponse') {
	$statut = $_GET['statut'];
}

var_dump($statut);
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

//Traitement des commandes
if(isset($_GET['btSend']) && $statut=='reponse') {
	$reponseUtilisateur = trim($_GET['reponse']);
	
	if(!empty($reponseUtilisateur)) {
		if($reponseUtilisateur==$questionsReponses[$nroQuestion][1]) {
			if(sizeof($questionsReponses)!=$nroQuestion+1) {
				$message = 'Bravo! <a href="?nroQuestion='.($nroQuestion+1).'&statut=next">Question suivante</a>';
			} else {
				$message = "Félicitations! Votre score est de $score points.";
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