<?php
require_once __DIR__ . '/../src/db/mongo.php';
require_once __DIR__ . '/../src/views/layout.php';

$db = mongoDB();
$error = "";

$id = $_GET['id'] ?? '';
if (!$id) {
    render_layout("Erreur", "<p>ID manquant.</p><a href='/stockwise-nosql/public/products.php'>Retour</a>");
    exit;
}

$product = $db->products->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
if (!$product) {
    render_layout("Erreur", "<p>Produit introuvable.</p><a href='/stockwise-nosql/public/products.php'>Retour</a>");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if ($sku === '' || $name === '') {
        $error = "SKU et Nom sont obligatoires.";
    } else {
        $db->products->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => [
                'sku' => $sku,
                'name' => $name,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]]
        );
        header("Location: /stockwise-nosql/public/products.php");
        exit;
    }
}

$form = ($error ? "<p style='color:red'>".htmlspecialchars($error)."</p>" : "") . "
<form method='post' style='display:grid; gap:10px; max-width:500px'>
  <label>SKU <input name='sku' required value='".htmlspecialchars((string)$product['sku'])."'></label>
  <label>Nom <input name='name' required value='".htmlspecialchars((string)$product['name'])."'></label>
  <label>Catégorie <input name='category' value='".htmlspecialchars((string)($product['category'] ?? ''))."'></label>
  <label>Prix <input name='price' type='number' step='0.01' value='".htmlspecialchars((string)($product['price'] ?? 0))."'></label>
  <label>Stock <input name='stock' type='number' value='".htmlspecialchars((string)($product['stock'] ?? 0))."'></label>
  <button type='submit'>Enregistrer</button>
  <a href='/stockwise-nosql/public/products.php'>Annuler</a>
</form>
";

render_layout("Modifier le produit", $form);