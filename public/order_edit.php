<?php
require_once __DIR__ . '/../src/db/mongo.php';
require_once __DIR__ . '/../src/views/layout.php';

$db = mongoDB();
$error = "";

$id = $_GET['id'] ?? '';
if (!$id) { render_layout("Erreur", "<p>ID manquant.</p>"); exit; }

$order = $db->orders->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
if (!$order) { render_layout("Erreur", "<p>Commande introuvable.</p>"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'OUT';
    $status = $_POST['status'] ?? 'confirmed';

    $db->orders->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($id)],
        ['$set' => ['type'=>$type, 'status'=>$status]]
    );

    header("Location: /stockwise-nosql/public/orders.php");
    exit;
}

$form = "
<form method='post' style='display:grid; gap:10px; max-width:520px'>
  <label>Type
    <select name='type'>
      <option value='OUT' ".(((string)$order['type']==='OUT')?'selected':'').">OUT</option>
      <option value='IN' ".(((string)$order['type']==='IN')?'selected':'').">IN</option>
    </select>
  </label>

  <label>Status
    <select name='status'>
      <option value='confirmed' ".(((string)$order['status']==='confirmed')?'selected':'').">confirmed</option>
      <option value='pending' ".(((string)$order['status']==='pending')?'selected':'').">pending</option>
      <option value='cancelled' ".(((string)$order['status']==='cancelled')?'selected':'').">cancelled</option>
    </select>
  </label>

  <button type='submit'>Enregistrer</button>
  <a href='/stockwise-nosql/public/orders.php'>Annuler</a>
</form>
";

$details = "<p><b>Client :</b> ".htmlspecialchars((string)($order['customer_name'] ?? ''))."</p>
<p><b>Total :</b> ".htmlspecialchars((string)($order['total'] ?? ''))."</p>";

render_layout("Modifier la commande", $details . $form);