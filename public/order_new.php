<?php
require_once __DIR__ . '/../src/db/mongo.php';
require_once __DIR__ . '/../src/views/layout.php';

$db = mongoDB();
$error = "";

// on récupère produits + users pour les listes
$products = $db->products->find([], ['sort'=>['name'=>1]])->toArray();
$users = $db->users->find([], ['sort'=>['email'=>1]])->toArray();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'OUT';
    $status = $_POST['status'] ?? 'confirmed';
    $customer_name = trim($_POST['customer_name'] ?? '');
    $created_by = $_POST['created_by'] ?? '';
    $product_id = $_POST['product_id'] ?? '';
    $qty = (int)($_POST['qty'] ?? 1);

    if ($customer_name === '' || $created_by === '' || $product_id === '' || $qty <= 0) {
        $error = "Tous les champs sont obligatoires (quantité > 0).";
    } else {
        $p = $db->products->findOne(['_id' => new MongoDB\BSON\ObjectId($product_id)]);
        if (!$p) {
            $error = "Produit introuvable.";
        } else {
            $price = (float)($p['price'] ?? 0);
            $total = $price * $qty;

            $db->orders->insertOne([
                'type' => $type,
                'status' => $status,
                'created_by' => new MongoDB\BSON\ObjectId($created_by),
                'customer_name' => $customer_name,
                'items' => [[
                    'product_id' => new MongoDB\BSON\ObjectId($product_id),
                    'sku' => (string)($p['sku'] ?? ''),
                    'name' => (string)($p['name'] ?? ''),
                    'qty' => $qty,
                    'unit_price' => $price,
                    'line_total' => $total
                ]],
                'total' => $total,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);

            // optionnel: décrémenter stock si OUT
            if ($type === 'OUT') {
                $db->products->updateOne(
                    ['_id' => new MongoDB\BSON\ObjectId($product_id)],
                    ['$inc' => ['stock' => -$qty]]
                );
            }

            header("Location: /stockwise-nosql/public/orders.php");
            exit;
        }
    }
}

$optionsProducts = "";
foreach ($products as $p) {
    $optionsProducts .= "<option value='".(string)$p['_id']."'>".htmlspecialchars((string)$p['name'])." (".htmlspecialchars((string)$p['sku']).")</option>";
}

$optionsUsers = "";
foreach ($users as $u) {
    $optionsUsers .= "<option value='".(string)$u['_id']."'>".htmlspecialchars((string)$u['email'])."</option>";
}

$form = ($error ? "<p style='color:red'>".htmlspecialchars($error)."</p>" : "") . "
<form method='post' style='display:grid; gap:10px; max-width:600px'>
  <label>Type
    <select name='type'>
      <option value='OUT'>OUT (sortie)</option>
      <option value='IN'>IN (entrée)</option>
    </select>
  </label>

  <label>Status
    <select name='status'>
      <option value='confirmed'>confirmed</option>
      <option value='pending'>pending</option>
      <option value='cancelled'>cancelled</option>
    </select>
  </label>

  <label>Client <input name='customer_name' required placeholder='Client Test'></label>

  <label>Créé par (user)
    <select name='created_by' required>{$optionsUsers}</select>
  </label>

  <label>Produit
    <select name='product_id' required>{$optionsProducts}</select>
  </label>

  <label>Quantité <input name='qty' type='number' value='1' min='1'></label>

  <button type='submit'>Créer la commande</button>
  <a href='/stockwise-nosql/public/orders.php'>Annuler</a>
</form>
";

render_layout("Ajouter une commande", $form);