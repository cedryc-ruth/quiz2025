<?php


require('config.php');

//Accès à la source de données
//Se connecter au serveur de DB
$mysql = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

//Préparer la requête
$query = "SELECT login, created_at FROM users";

//Envoyer la requête et récupérer le résultat
$result = mysqli_query($mysql, $query);

//Extraire les données
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

//Libérer la mémoire
mysqli_free_result($result);

//Se déconnecter
mysqli_close($mysql);
?>
<body>

<?php foreach($users as $user) { ?>
    <p><?= $user['login'] ?></p>
<?php } ?>

</body>