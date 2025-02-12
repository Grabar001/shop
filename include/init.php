<?php

// -------- CONNECTION BIO     
$connect_db = new PDO('mysql:host=localhost;port=8889;dbname=shop', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
]);


// -------- SESSION
session_start();

// -------- CHEMIN
define('RACINE_SITE', $_SERVER['DOCUMENT_ROOT'] . '/');
// echo '<pre>'; print_r(RACINE_SITE); echo '</pre>';
// Cette constante retourne le chemin physique du dossier htdocs sur le serveur, de notre dossier 'shop' sur le serveur.
//Lors de lenregistrement d'image/photos, nous aurons besoin du chemin complet dossier images pour enregistrer la photo dans le bon dossier.
// echo RACINE_SITE . 'shop/assets/images/product.jpg';

define('URL', 'http://localhost:8889/shop/');
// <img src="html://localhost:8889/shop/assets/images/product.jpg">
// <img src="URL . assets/images/product.jpg" alt="">
// <img src=URL . assets/images/product.jpg alt="">
// Cette constante servira à enregistrer l'URL d'une image/photo dans la BDD, pour l'afficher dynamiquement sur le site.

// -------- VARIABLES
$content = '';

// -------- FAILLES XSS

foreach($_POST as $key => $value) {
    $_POST[$key] = htmlentities(addslashes(trim($value)));
}

foreach($_GET as $key => $value) {
    $_GET[$key] = htmlentities(addslashes(trim($value)));
}

// -------- INCLUSIONS function
require_once ('functions.php');
