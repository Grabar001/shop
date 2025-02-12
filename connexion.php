<?php
require_once 'include/init.php';

// echo '<pre>';print_r($_POST);echo '</pre>';

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
  session_start();
  session_unset();  // Удаляет все переменные сессии
  session_destroy();  // Полностью уничтожает сессию
  header('Location: connexion.php'); // Перенаправляем на страницу входа
  exit();
}
  if(userConnected()){
    header('location:index.php');
  }


if(isset($_POST['submit']) && $_SERVER['REQUEST_METHOD'] === 'POST'){
  $data = $connect_db->prepare("SELECT *FROM user WHERE email = :email");
  $data->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
  $data->execute();

  //Si la requete de selection retourne un resultat, cela veut que l'email est connu en BDD
  if($data->rowCount()){
    // echo 'email existant';

    //On recupere un Array contenant toutes les donnees de l'utilisateur qui a saisi le bon email
    $user = $data->fetch(PDO::FETCH_ASSOC);
    // echo '<pre>';print_r($user);echo '</pre>';

    //password_verfy() :  fonction predefinie permettant de comparer le mot de passe saisi dans le formnulaire a la cle de hachage du mot de pass dans La BDD, on entre dans la condition IF si les mots de passe corresondent
    if(password_verify($_POST['password'], $user['password'])){
      // echo "password valide";

      //On stock dans la session toutes les donnees de l'utilisateur correctement authentifier, ces donnes sont stockees cote serveur et accessibles sur n'importe quelle page du site
      foreach($user as $key => $value){
        // $_SESSION['user']['id_user'] = 2;
        // $_SESSION['user']['fistName'] = Grégory;
        $_SESSION['user'][$key] = $value;
    }
    
    // echo '<pre>'; print_r($_SESSION); echo '</pre>';
    header('location:index.php');
    }else{
      // echo "password error";
      $error ='<div class="background-danger p-3 mb-3 text-while text-center">Email ou mot de passe invalide</div>';
    }
  }else{
    // echo 'email inexistant';
    $error ='<div class="background-danger p-3 mb-3 text-while text-center">Email ou mot de passe invalide</div>';
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
            <h3>Identifiez-vous</h3>
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

        <?php if (isset($_SESSION['msgRegisterValidate'])) echo   $_SESSION['msgRegisterValidate']; ?>
        <?php if (isset($error)) echo $error; ?>
          <div class="full">
            <form method="post" action="">
              <fieldset>
                <input
                  type="text"
                  placeholder="Entrez votre adresse e-mail"
                  name="email"
                  class="<?php if (isset($error)) echo 'border-danger'; ?>"
                  value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>"
                  />
                <input
                  type="password"
                  placeholder="Enter votre mot de passe"
                  name="password"
                 />
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
  <!-- footer section -->
 
<?php
require_once 'include/footer.php';
// On suprime le message de validation de l'inscription dans la session, afin qu'il ne s'affiche plus
unset($_SESSION['msgRegisterValidate']);
?>