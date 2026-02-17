<?php
require_once __DIR__ . '/../src/db/mongo.php';
require_once __DIR__ . '/../src/views/layout.php';

$db = mongoDB();

$q = trim($_GET['q'] ?? '');

$filter = [];
if ($q !== '') {
    $filter = [
        '$or' => [
            ['sku' => new MongoDB\BSON\Regex(preg_quote($q), 'i')],
            ['name' => new MongoDB\BSON\Regex(preg_quote($q), 'i')],
            ['category' => new MongoDB\BSON\Regex(preg_quote($q), 'i')],
        ]
    ];
}

$cursor = $db->products->find($filter, ['sort' => ['updated_at' => -1]]);

$rows = "";
foreach ($cursor as $p) {
    $id = (string)$p['_id'];
    $rows .= "<tr>";
    $rows .= "<td>" . htmlspecialchars((string)($p['sku'] ?? '')) . "</td>";
    $rows .= "<td>" . htmlspecialchars((string)($p['name'] ?? '')) . "</td>";
    $rows .= "<td>" . htmlspecialchars((string)($p['category'] ?? '')) . "</td>";
    $rows .= "<td>" . htmlspecialchars((string)($p['price'] ?? '')) . "</td>";
    $rows .= "<td>" . htmlspecialchars((string)($p['stock'] ?? '')) . "</td>";
    $rows .= "<td>
                <a href='/stockwise-nosql/public/product_edit.php?id={$id}'>Modifier</a>
                |
                <a href='/stockwise-nosql/public/product_delete.php?id={$id}' onclick=\"return confirm('Supprimer ce produit ?');\">Supprimer</a>
              </td>";
    $rows .= "</tr>";
}

$searchHtml = "
<form method='get' style='margin: 0 0 12px 0; display:flex; gap:8px; align-items:center; flex-wrap:wrap'>
  <input name='q' placeholder='Rechercher (sku, nom, catégorie)…' value='".htmlspecialchars($q)."' style='padding:8px; min-width:260px'>
  <button type='submit'>Rechercher</button>
  <a href='/stockwise-nosql/public/products.php'>Reset</a>
  <span style='margin-left:auto'><a href='/stockwise-nosql/public/product_new.php'>➕ Ajouter un produit</a></span>
</form>
";

$content = $searchHtml . "
  <h2>Liste complète des produits</h2>
  <table border='1' cellpadding='8' cellspacing='0'>
    <tr><th>SKU</th><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Actions</th></tr>
    {$rows}
  </table>
";

render_layout('Produits', $content);