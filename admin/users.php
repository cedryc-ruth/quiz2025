<?php
//Reprendre la session
session_start();

$message = '';

//Inclure les données de configuration (constantes de base de données)
require('../config.php');

//Sécuriser l'accès à la page
if(!isset($_SESSION['status']) || $_SESSION['status']!='admin') {
    header('Location: '.APP_URL.'/index.php');
    exit;
}

//Modifier le statut du user sélectionné
if(isset($_POST['btUpgradeStatus']) || isset($_POST['btDowngradeStatus'])) {
    // Connexion au serveur de bases de données
    $mysql = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

    // Préparation de la requête: nettoyer les données entrantes
    $userId = mysqli_real_escape_string($mysql, $_POST['userId']);

    if(isset($_POST['btUpgradeStatus'])) {
        $query = "UPDATE users SET status='admin' WHERE id=$userId";
    } else {
        $query = "UPDATE users SET status='member' WHERE id=$userId";
    }

    // Envoi de la requête
    $result = mysqli_query($mysql, $query);

    // Vérification du résultat
    if($result && mysqli_affected_rows($mysql)>0) {
        $message = "Modification réussie.";
    } else {
        $message = "Une erreur est survenue lors de la modification.";
    }

    // Déconnexion du serveur de bases de données
    mysqli_close($mysql);

}

//Récupérer la liste des utilisateurs depuis la base de données
$users = [];    //Valeur par défaut

//Accès à la source de données
//Se connecter au serveur de DB
$mysql = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

//Préparer la requête
$query = "SELECT id, login, created_at, status FROM users";

//Envoyer la requête et récupérer le résultat
$result = mysqli_query($mysql, $query);

//Extraire les données
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
//var_dump($users);die;

//Libérer la mémoire
mysqli_free_result($result);

//Se déconnecter
mysqli_close($mysql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
</head>
<body>
<h1>Gestion des utilisateurs</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Login</th>
        <th>Date inscription</th>
        <th>Statut</th>
        <th>Action</th>
    </tr>
<?php foreach($users as $user) { ?>
    <tr>
        <td><?= $user['id'] ?></td>
        <td><?= $user['login'] ?></td>
        <td><?= $user['created_at'] ?></td>
        <td><?= $user['status'] ?></td>
        <td>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <input type="hidden" name="userId" value="<?= $user['id'] ?>">

            <?php if($user['status']!='admin') { ?>
                <button name="btUpgradeStatus">Elever le statut</button>
            <?php } else { ?>
                <button name="btDowngradeStatus">Baisser le statut</button>
            <?php } ?>
            </form>
        </td>
    </tr>
<?php } ?>
</table>
<div><?= $message ?></div>
</body>
</html>