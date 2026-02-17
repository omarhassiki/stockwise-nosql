<?php
require_once __DIR__ . '/../src/db/mongo.php';
require_once __DIR__ . '/../src/views/layout.php';

$db = mongoDB();
$q = trim($_GET['q'] ?? '');

$filter = [];
if ($q !== '') {
    $filter = [
        '$or' => [
            ['customer_name' => new MongoDB\BSON\Regex(preg_quote($q), 'i')],
            ['status' => new MongoDB\BSON\Regex(preg_quote($q), 'i')],
            ['type' => new MongoDB\BSON\Regex(preg_quote($q), 'i')],
        ]
    ];
}

$cursor = $db->orders->find($filter, ['sort' => ['created_at' => -1]]);

$rows = "";
foreach ($cursor as $o) {
    $id = (string)$o['_id'];
    $type = (string)($o['type'] ?? '');
    $status = (string)($o['status'] ?? '');
    $customer = (string)($o['customer_name'] ?? '');
    $total = (string)($o['total'] ?? '');

    $rows .= "<tr>
        <td>".htmlspecialchars($type)."</td>
        <td>".htmlspecialchars($status)."</td>
        <td>".htmlspecialchars($customer)."</td>
        <td>".htmlspecialchars($total)."</td>
        <td>
          <a href='/stockwise-nosql/public/order_edit.php?id={$id}'>Modifier</a> |
          <a href='/stockwise-nosql/public/order_delete.php?id={$id}' onclick=\"return confirm('Supprimer cette commande ?');\">Supprimer</a>
        </td>
    </tr>";
}

$searchHtml = "
<form method='get' style='margin: 0 0 12px 0; display:flex; gap:8px; align-items:center; flex-wrap:wrap'>
  <input name='q' placeholder='Rechercher (client, status, type)…' value='".htmlspecialchars($q)."' style='padding:8px; min-width:260px'>
  <button type='submit'>Rechercher</button>
  <a href='/stockwise-nosql/public/orders.php'>Reset</a>
  <span style='margin-left:auto'><a href='/stockwise-nosql/public/order_new.php'>➕ Ajouter une commande</a></span>
</form>
";

$content = $searchHtml . "
  <h2>Liste des commandes</h2>
  <table border='1' cellpadding='8' cellspacing='0'>
    <tr><th>Type</th><th>Status</th><th>Client</th><th>Total</th><th>Actions</th></tr>
    {$rows}
  </table>
";

render_layout('Commandes', $content);