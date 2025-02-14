<?php
require_once 'include/init.php';

// Exo 1 : Afficher les produits stockes en BDD
$statement = $connect_db->query("SELECT * FROM product");
$product = $statement->fetchAll();
// 1 selectionner l'ensemble de la table prouct
// 2 Excecuter une methode ( fetch / fetchAll )  pour rendre le resultat exploitable sous forme d'Array
// 3 Traitement pour l'affichage ( boucle )
// 4 Prevoir un lien qui redirige vers la page fiche_produit.php pour chaque produit, avec envoi de l'id_produit dans l'url
require_once 'include/header.php';
?>
  <!-- inner page section -->
  <section class="inner_page_head">
    <div class="container_fuild">
      <div class="row">
        <div class="col-md-12">
          <div class="full">
            <h3>Grille de produits</h3>
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
        <h2>Nos <span>produits</span></h2>
      </div>
      <div class="row">
        <?php foreach ($produits as $produit) : ?>
          <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box">
              <div class="option_container">
                <div class="options">
                  <a href="" class="option1"><?= htmlspecialchars($produit['nom']); ?></a>
                  <a href="" class="option2">Acheter maintenant</a>
                </div>
              </div>
              <div class="img-box">
                <img src="<?= htmlspecialchars($produit['image']); ?>" alt="" />
              </div>
              <div class="detail-box">
                <h5><?= htmlspecialchars($produit['nom']); ?></h5>
                <h6><?= number_format($produit['prix'], 2, ',', ' ') ?>€</h6>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="btn-box">
        <a href=""> Voir tous les produits </a>
      </div>
    </div>
  </section>
  <!-- end product section -->
<?php
require_once 'include/footer.php';
?>