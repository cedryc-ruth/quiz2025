<?php
session_start();

$_SESSION['connected'] = false;

//Traitement des commandes
if(isset($_POST['btLogin'])) {  //var_dump('btLogin');
    if(!empty($_POST['login']) && !empty($_POST['pwd'])) {  //var_dump('log-pass');
        $login = $_POST['login'];
        $password = $_POST['pwd'];

        if($login=='toto' && $password=='epfc') {   //var_dump('identifiants ok');
            $_SESSION['connected'] = true;
        } else {
            //Sauvegarder le message d'erreur (par cookie ou session)
            setcookie('erreurLogin','Erreur de connexion! Identifiants incorrects.',0);
            //$_SESSION['erreurLogin'] = 'Erreur de connexion! Identifiants incorrects.';
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
//echo '<a href="quizz3.php?erreurLogin=Erreur de conexion!">Retour au quiz</a>';

header('Location: quizz3.php',null,302);
exit;
?>
