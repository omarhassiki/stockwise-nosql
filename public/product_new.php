<?php
require_once __DIR__ . '/../src/db/mongo.php';
require_once __DIR__ . '/../src/views/layout.php';

$db = mongoDB();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if ($sku === '' || $name === '') {
        $error = "SKU et Nom sont obligatoires.";
    } else {
        $db->products->insertOne([
            'sku' => $sku,
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'stock' => $stock,
            'tags' => [],
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]);
        header("Location: /stockwise-nosql/public/products.php");
        exit;
    }
}

$form = ($error ? "<p style='color:red'>".htmlspecialchars($error)."</p>" : "") . "
<form method='post' style='display:grid; gap:10px; max-width:500px'>
  <label>SKU <input name='sku' required></label>
  <label>Nom <input name='name' required></label>
  <label>Catégorie <input name='category'></label>
  <label>Prix <input name='price' type='number' step='0.01' value='0'></label>
  <label>Stock <input name='stock' type='number' value='0'></label>
  <button type='submit'>Créer</button>
  <a href='/stockwise-nosql/public/products.php'>Annuler</a>
</form>
";

render_layout("Ajouter un produit", $form);