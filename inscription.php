<?php
require_once 'include/init.php';




if(userConnected()) {
  header('location:profil.php');
}


// 1. Contrôler que l'on réceptionne bien toutes les données saisies dans le formulaire en PHP
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';

if (isset($_POST['submit']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
  //2. Contrôler la disponibilite de l'email (select + rowCount)
  $data = $connect_db->prepare("SELECT * FROM user WHERE email = :email");
  $data->bindValue(':email', $_POST['email']);
  $data->execute();

  // echo$data->rowCount();
  // Si la condition IF retourne TRUE, l'email est existant en BDD, on entre dans le IF
  if ($data->rowCount()) {
    $errorEmail = '<small class="text-color-danger">Un compte est déjà existant à cette adresse email.</small>';
  } elseif (empty($_POST['email'])) {
    $errorEmail = '<small class="text-color-danger">Veuillez renseigner votre adresse email.</small>';
  } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { // Инвертируем условие
    $errorEmail = '<small class="text-color-danger">Veuillez renseigner une adresse email valide. (ex: exemple@gmail.com)</small>';
  }

  $password_regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";
  echo preg_match($password_regex, 'secret') . '<br>'; // returns 0
  echo preg_match($password_regex, '-Secr3t.'); // returns 1

  if (empty($_POST['password'])) {
    $errorPassword = '<small class="text-color-danger">Merci de saisir un mot de passe.</small>';
} elseif (!isset($_POST['password']) || !preg_match($password_regex, $_POST['password'])) { 
    $errorPassword = '<small class="text-color-danger">8 caractères minimum, une majuscule, une minuscule, un chiffre, un caractère spécial (#?!@$%^&*-).</small>';
} elseif ($_POST['password'] !== $_POST['repeat_password']) {
  $errorPassword = '<small class="text-color-danger">Les mots de passe ne correspondent pas.</small>';
} 

// Exo: Si l'utilisateur a correctement rempli le formulaire, executer la requete d'insertion en BDD (prepare + bindValue + execute), on redige l'internaute vers la page connexion.php

if (!isset($errorEmail) && !isset($errorPassword)) {
  //Le mot de passe n'est jamais conserve en clair dans la base de donnes
  //password_hush permet de generer une clé de hachage pour le mot de passe
  $data = $connect_db->prepare("INSERT INTO user (password, firstName, lastName, email, city, zipcode, address, roles) 
VALUES (:password, :firstName, :lastName, :email, :city, :zipcode, :address, :roles)");

$data->bindValue(':roles', 'user', PDO::PARAM_STR);
  $data->bindValue(':firstName', $_POST['firstName'], PDO::PARAM_STR);
  $data->bindValue(':lastName', $_POST['lastName'], PDO::PARAM_STR);
  $data->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
  $data->bindValue(':city', $_POST['city'], PDO::PARAM_STR);
  $data->bindValue(':zipcode', $_POST['zipcode'], PDO::PARAM_STR);
  $data->bindValue(':address', $_POST['address'], PDO::PARAM_STR);
  $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$data->bindValue(':password', $hashed_password, PDO::PARAM_STR);
  $data->execute();

  // On stock dans le fichier de session de l'utilisateur, le fichier de session est stocke cote serveur et accessible via la superglobale $°SESSION et accesible sur n'importe quelle page du site, on stock ici un message (message flash) dans le fichier de session de l'utilisateur
  $_SESSION['msgRegisterValidate'] = '<div class="bg-success p-3 text-white text-center">Votre inscription est valide. Vous pouvez des a present vous connecter</div>';

  header('location:connexion.php');
  exit();
}
}

require_once 'include/header.php';
?>


<!-- inner page section -->
<section class="inner_page_head">
  <div class="container_fuild">
    <div class="row">
      <div class="col-md-12">
        <div class="full">
          <h3>Créer votre compte</h3>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end inner page section -->
<!-- why section -->
<section class="why_section layout_padding">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 offset-lg-2">
        <div class="full">
          <form action="inscription.php" method="post">
            <fieldset>
              <input
                type="text"
                placeholder="Enter votre prénom"
                name="firstName" />
              <input
                type="text"
                placeholder="Enter votre nom"
                name="lastName" />
              <?php if (isset($errorEmail)) echo $errorEmail; ?>
              <input
                type="text"
                placeholder="Entrez votre adresse e-mail"
                name="email"
                class="<?php if (isset($errorEmail)) echo 'border-danger'; ?>"
                value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" />
              <input
                type="text"
                placeholder="Entrer votre adresse"
                name="address" />
              <input
                type="text"
                placeholder="Entrer votre ville"
                name="city" />
              <input
                type="text"
                placeholder="Entrer votre code postal"
                name="zipcode" />
              <?php if (isset($errorPassword)) echo $errorPassword; ?>
              <input
                type="text"
                placeholder="Enter votre mot de passe"
                name="password"
                class="<?php if (isset($errorPassword)) echo 'border-danger'; ?>" />
              <input
                type="text"
                placeholder="Répétez votre mot de passe"
                name="repeat_password" />
              <input type="submit" name="submit" value="Continuer" />
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end why section -->
<!-- arrival section -->
<!-- end arrival section -->
<?php
require_once 'include/footer.php';
?>