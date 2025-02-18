<?php
// ------ FONCTION UTILISATEUR AUTHENTIFIE
// Fonction permettant de savoir si l'utilisateur est authentifié sur le site
function userConnected()
{
    // Si l'indice 'user' dans le fichier de session n'est pas définit, cela veut dire que l'internaute n'est pas passé par la page connexion et n'est pas authentifié
    if (isset($_SESSION['user']))
        return true;
    else
        return false; // on retourne true si l'indice 'user' est définit dans la session
}

// ------FONCTION ADMINISTRATEUR AUTHENTIFIE
function adminConnected()
{
    // Si à l'indice 'roles' dans la session, la valeur est admin, cela veut dire que c'est un administrateur on retourne true

    if (userConnected() && $_SESSION['user']['roles'] == 'admin')
        return true;
    else
        return false; // on retourne false si dans la session le roles n'est pas 'admin'
}
//FONCTION CREATION DU PANIER SESSION
function createCart()
{
    // Si l'indece 'cart' n'est pas définit dans la session, cela veut dire que l'internaute n'a pas de panier, on le créer
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
        $_SESSION['cart']['id_product'] = [];
        $_SESSION['cart']['title'] = [];
        $_SESSION['cart']['picture'] = [];
        $_SESSION['cart']['reference'] = [];
        $_SESSION['cart']['quantity'] = [];
        $_SESSION['cart']['price'] = [];
    }
}
//Fonction ajouter un produit dans le panier
function addProductToCart($id_product, $title, $picture, $reference, $quantity, $price)
{
    createCart(); // on créer le panier

    // On controle si l'indice id_produit existe dans le panier, si c'est le cas on ne rajoute que la quantité
    $positionProduct = array_search($id_product, $_SESSION['cart']['id_product']);
    // var_dump($positionProduct);

    // Si la valeur de $positionProduit est différente de false, cela veut dire que l'id_produit est présent dans le panier
    if ($positionProduct !== false) {
        $_SESSION['cart']['quantity'][$positionProduct] += $quantity;
    } else {

        //Sinon on ajoute le produit dans le panier
        // lesd[ ] vide permettent de creer des indices numerique dans les tableaux Array
        $_SESSION['cart']['id_product'][] = $id_product;
        $_SESSION['cart']['title'][] = $title;
        $_SESSION['cart']['picture'][] = $picture;
        $_SESSION['cart']['reference'][] = $reference;
        $_SESSION['cart']['quantity'][] = $quantity;
        $_SESSION['cart']['price'][] = $price;
    }
}


//Fonction Supprimer artuicle panier
function removeProductCart($id_product)
{
    // Мы ищем, на каком индексе в массиве $_SESSION['cart']['id_product'] 
    // находится ID продукта, который нужно удалить из корзины.
    $positionProduct = array_search($id_product, $_SESSION['cart']['id_product']);
    // Отладка: выводит индекс найденного продукта (или false, если его нет).
    // var_dump($positionProduct);

    if ($positionProduct !== false) {
        // Предопределённая функция array_splice позволяет удалить элемент из массива по указанному индексу,
        // а также автоматически сдвигает индексы вниз: если я удаляю элемент с индексом [2] в массиве,
        // то элемент с индексом [3] сместится на место [2].
        array_splice($_SESSION['cart']['id_product'], $positionProduct, 1);
        array_splice($_SESSION['cart']['title'], $positionProduct, 1);
        array_splice($_SESSION['cart']['picture'], $positionProduct, 1);
        array_splice($_SESSION['cart']['reference'], $positionProduct, 1);
        array_splice($_SESSION['cart']['quantity'], $positionProduct, 1);
        array_splice($_SESSION['cart']['price'], $positionProduct, 1);
    }
}



//Fonction calculer le montant total du panier
function totalAmount()
{
    $total = 0;
    for ($i = 0; $i < count($_SESSION['cart']['id_product']); $i++) {
        $total += $_SESSION['cart']['quantity'][$i] * $_SESSION['cart']['price'][$i];
    }
    return round($total, 2);
}

// /Fonction Liens Actifs Nav 
function activeLink($url)
{
    if ($_SERVER['PHP_SELF'] == $url) {
        echo ' active';
    }
}
