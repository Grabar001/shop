<?php
// ------ FONCTION UTILISATEUR AUTHENTIFIE
// Fonction permettant de savoir si l'utilisateur est authentifié sur le site
function userConnected(){
    // Si l'indice 'user' dans le fichier de session n'est pas défini, cela veut
    // dire que l'internaute n'est pas passé par la page connexion et n'est pas authentifié
    return !empty($_SESSION['user']);// on retourne true si l'indice 'user' est défini dans la session
}

// ------FONCTION ADMINISTRATEUR AUTHENTIFIE
function adminConnected(){
    // Si à l'indice 'roles' dans la session, la valeur est admin, 
    // cela veut dire que c'est un administrateur on retourne true
   
    return userConnected() && isset($_SESSION['user']['roles']) && $_SESSION['user']['roles'] === 'admin';

}