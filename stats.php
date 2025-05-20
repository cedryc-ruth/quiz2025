<?php
//Sécuriser l'accès au script
if(empty($_COOKIE['cptBonnesReponses']) || $_COOKIE['cptBonnesReponses']<3) {
    header('Location: index.php', null, 302);
    exit;
}

require('config.php');

//Accès à la source de données
//Se connecter au serveur de DB
$mysql = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

//Préparer la requête
$query = "SELECT question,id FROM quizz";

//Envoyer la requête et récupérer le résultat
$result = mysqli_query($mysql, $query);

//Extraire les données
$questionsReponses = mysqli_fetch_all($result, MYSQLI_NUM);
//var_dump($questionsReponses);die;

//Libérer la mémoire
mysqli_free_result($result);

//Se déconnecter
mysqli_close($mysql);

//Lire le fichier des statistiques stats.csv
if(($fp = fopen('stats.csv','r')) !== false) {
    while(($ligne = fgetcsv($fp,1000,";")) !== false) {
        //var_dump($ligne);die;
        $stats[] = $ligne;
    }
    //Supprimer la ligne d'en-tête (1re ligne)
    array_shift($stats);    //var_dump($stats);die;

    fclose($fp);
}    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz : Statistiques</title>
</head>
<body>
    <h1>Quiz - Statistiques</h1>

    <nav>
        <a href="index.php">Accueil</a>
    </nav>

    <p>Le quiz contient <?= sizeof($questionsReponses) ?> questions.</p>

    <h2>Questions</h2>
    <form>
    <?php foreach($questionsReponses as $questionsReponses) { ?>
        <div>
            <input type="checkbox" name="questionId" id="questionId<?= $questionsReponses[1] ?>" value="<?= $questionsReponses[1] ?>">
            <label for="questionId<?= $questionsReponses[1] ?>"><?= $questionsReponses[0] ?></label>
            <ul>
                <li><?= $stats[$questionsReponses[1]-1][1] ?> essais</li>
                <li><?= $stats[$questionsReponses[1]-1][2] ?> erreurs</li>
            </ul>
        </div>
    <?php } ?>
    </form>
</body>
</html>