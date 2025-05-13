<?php
session_start();

require('config.php');

//Déclaration des variables
$_SESSION['connected'] = false;

//Traitement des commandes
if(isset($_POST['btLogin'])) {  //var_dump('btLogin');
    if(!empty($_POST['login']) && !empty($_POST['pwd'])) {  //var_dump('log-pass');
        $login = $_POST['login'];
        $password = $_POST['pwd'];

        //Accès à la source de données
        //Se connecter au serveur de DB
        $mysql = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

        //Préparer la requête
            //Nettoyer la requête
        $login = mysqli_real_escape_string($mysql, $login);
        $query = "SELECT id, login, password, status FROM users WHERE login='$login'";

        //Envoyer la requête et récupérer le résultat
        $result = mysqli_query($mysql, $query);

        //Extraire les données
        $user = mysqli_fetch_all($result, MYSQLI_ASSOC);
        //var_dump($user);die;

        //Libérer la mémoire
        mysqli_free_result($result);

        //Se déconnecter
        mysqli_close($mysql);

        if(!empty($user)) {     //Login vérifié
            $user = $user[0];

            if(password_verify($password, $user['password'])) {  //Vérification du mot de passe
                $_SESSION['connected'] = true;
                $_SESSION['login'] = $user['login'];
                $_SESSION['userId'] = $user['id'];
                $_SESSION['status'] = $user['status'];
            } else {
                //Sauvegarder le message d'erreur (par cookie ou session)
                setcookie('erreurLogin','Erreur de connexion! Identifiants incorrects.',0);
            }
        } else {    //Aucun utilisateur ne correspond à ce login
             //Sauvegarder le message d'erreur (par cookie ou session)
             setcookie('erreurLogin','Erreur de connexion! Identifiants incorrects.',0);
        }
    } else {
        //Sauvegarder le message d'erreur (par cookie ou session)
        setcookie('erreurLogin','Erreur de connexion! Veuillez remplir tous les champs.',0);
        //$_SESSION['erreurLogin'] = 'Erreur de connexion! Veuillez remplir tous les champs.';
    }
} elseif(isset($_POST['btLogout'])) {  //var_dump('btLogout');
    //Se déconnecter => Détruire la session
    session_unset();
    session_destroy();
}

//Redirection
//echo '<a href="quizz.php?erreurLogin=Erreur de conexion!">Retour au quiz</a>';

header('Location: quizz.php',null,302);
exit;
?>
