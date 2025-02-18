<?php
require_once 'include/init.php';

// echo '<pre>'; print_r($_POST); echo '</pre>';

if (isset($_POST['add_cart'])) {
  $data = $connect_db->prepare("SELECT * FROM product WHERE id_product = :id");
  $data->bindValue(':id', $_POST['id_product'], PDO::PARAM_INT);
  $data->execute();

  $product = $data->fetch(PDO::FETCH_ASSOC);
  // echo '<pre>'; print_r($product); echo '</pre>';

  addProductToCart($product['id_product'], $product['title'], $product['picture'], $product['reference'], $_POST['quantity'], $product['price']);
  // echo '<pre>'; print_r($_SESSION); echo '</pre>';

  header('location:panier.php');
}

if (isset($_POST['payForCart'])) {
  echo "";
  $error = '';
  for ($i = 0; $i < count($_SESSION['cart']['id_product']); $i++) {
    $data = $connect_db->query("SELECT stock FROM product WHERE id_product = " . $_SESSION['cart']['id_product'][$i]);
    $product = $data->fetch(PDO::FETCH_ASSOC);
    echo '<pre>';
    print_r($product);
    echo '</pre>';

    //Si la quantite en stock en BDD est inferieur
    if ($product['stock'] < $_SESSION['cart']['quantity'][$i]) {

      $error .= '<div class="alert alert-danger text-center">Stock restant du produit ' . $_SESSION['cart']['title'][$i] . ' : <strong>' . $product['stock'] . '</strong></div>';

      $error .= '<div class="alert alert-warning text-center mt-2">Quantité commandée du produit ' .
        $_SESSION['cart']['title'][$i] . ' a ete reduite car notre stock est insuffisant</div>';

      if ($product['stock'] > 0) {

        $_SESSION['cart']['quantity'][$i] = $product['stock'];
        $error .= '<div class="alert alert-warning text-center mt-2">Quantité du produit ' .
          $_SESSION['cart']['title'][$i] . '  ete reduite car notre stock est insuffisant ' . $product['stock'] . '</div>';
      } else {
        $error .= '<div class="alert alert-warning text-center mt-2">Le produit ' .
          $_SESSION['cart']['title'][$i] . ' a supprime car nous sommes en rupture de stock . </div>';

        removeProductCart($_SESSION['cart']['id_product'][$i]);
        $i--; // on decremente la boucle apres la suppression, car array_splice() supprime l'article dans les tableaux et remontent les indices inferieurs vers les indices superieur, cela nous permet de ne pas oublie de controle un article qui aurait change d'indice


      }
    }
  }

  // requette d'insertion dans la table order
  if (empty($error)) {
    $data = $connect_db->exec("INSERT INTO `order` (user_id, rising, date, state) VALUES (" . $_SESSION['user']['id_user'] . ", " . totalAmount() . ", NOW(), 'treatment')");
    // On recupere le dernier id genere en BDD, l'id de la commande insere en BDD pour l'enregistrer dans la table order_product
    $idOrder = $connect_db->lastInsertId();
    // print_r($idOrder);

    for ($i = 0; $i < count($_SESSION['cart']['id_product']); $i++) {
      $data = $connect_db->exec("INSERT INTO `order_details` (order_id, product_id, quantity, price) 
      VALUES ($idOrder, " . $_SESSION['cart']['id_product'][$i] . ", " . $_SESSION['cart']['quantity'][$i] . ", " . $_SESSION['cart']['price'][$i] . ")");

      $data = $connect_db->exec("UPDATE product SET stock = stock - " . $_SESSION['cart']['quantity'][$i] . " WHERE id_product = " . $_SESSION['cart']['id_product'][$i]);
    }
    unset($_SESSION['cart']);
    $_SESSION['msValidateOrder' ]= "<div class='alert alert-success text-center'>La commande a ete prise en compte. Numero de commande <strong>FAMMS$idOrder</strong></div>";
  }
}
// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';
require_once 'include/header.php';
?>
<!-- inner page section -->
<section class="inner_page_head">
  <div class="container_fuild">
    <div class="row">
      <div class="col-md-12">
        <div class="full">
          <h3>Votre panier</h3>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end inner page section -->
<!-- product section -->
<section class="product_section layout_padding">
  <div class="container">
    <div class="heading_container heading_center">
      <h2>Valider vos <span>achats !</span></h2>
    </div>

    <?php 
    if (isset($error)) echo $error; 
    if (isset($_SESSION['msValidateOrder'])) echo $_SESSION['msValidateOrder'];
    unset($_SESSION['msValidateOrder']);
    ?>

    <div class="row">




      <table class="table table-borderless">
        <thead>
          <tr>
            <th>Titre</th>
            <th>Image</th>
            <th>Reference</th>
            <th>Quantite</th>
            <th>Prix</th>
            <th>Prix total</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($_SESSION['cart']['id_product'])): ?>
            <tr>
              <td colspan="6" class="text-center">Aucun article dans le panier</td>
            </tr>

            <?php else:

            for ($i = 0; $i < count($_SESSION['cart']['id_product']); $i++):
            ?>
              <tr>
                <td><?= ucfirst($_SESSION['cart']['title'][$i]); ?></td>
                <td><img src="<?= $_SESSION['cart']['picture'][$i] ?>" class="picture_product" alt="<?= $_SESSION['cart']['title'][$i] ?>"></td>

                <td><?= $_SESSION['cart']['reference'][$i]; ?></td>
                <td><?= $_SESSION['cart']['quantity'][$i]; ?></td>
                <td><?= $_SESSION['cart']['price'][$i]; ?>€</td>

                <td><strong><?= $_SESSION['cart']['quantity'][$i] * $_SESSION['cart']['price'][$i]; ?>€</strong></td>

                <td><a href="" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a></td>
              </tr>
            <?php endfor;
            ?>
            <tr>
              <th>MONTANT TOTAL</th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th><?= totalAmount(); ?>€</th>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if (!empty($_SESSION['cart']['id_product'])): ?>
      <div class="btn-box">
        <?php if (userConnected()): ?>
          <form action="" method="post">
            <input type="submit" name="payForCart" value="Procéder au paiement">
          </form>
        <?php else: ?>
          <p>Veuillez vous <a href="inscription.php">inscrire</a> ou vous <a href="connexion.php">identifier</a> pour valider le paiement</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <div class=" btn-box">
      <a href="product.php"> Continuer vos achats</a>
    </div>
  </div>
</section>

<?php
require_once 'include/footer.php';
?>