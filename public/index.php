<?php
require_once __DIR__ . '/../src/db/mongo.php';
require_once __DIR__ . '/../src/views/layout.php';

$db = mongoDB();

$products = $db->products->countDocuments();
$suppliers = $db->suppliers->countDocuments();
$orders = $db->orders->countDocuments();

$lowStock = $db->products->find(
  ['stock' => ['$lte' => 5]],
  ['sort' => ['stock' => 1], 'limit' => 6]
)->toArray();

$lowHtml = "";
if (count($lowStock) === 0) {
  $lowHtml = "<p><span class='badge ok'>Aucun stock faible ✅</span></p>";
} else {
  $lowHtml .= "<div class='sep'></div><h3>Alertes stock faible (≤ 5)</h3><ul>";
  foreach ($lowStock as $p) {
    $lowHtml .= "<li><b>".htmlspecialchars((string)$p['sku'])."</b> — "
      .htmlspecialchars((string)$p['name'])." : <span class='badge danger'>"
      .htmlspecialchars((string)$p['stock'])."</span></li>";
  }
  $lowHtml .= "</ul>";
}

$content = "
<div style='display:grid; gap:12px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));'>
  <div class='card'>
    <div class='small'>Produits</div>
    <div style='font-size:32px; font-weight:700; margin-top:6px;'>".htmlspecialchars((string)$products)."</div>
    <div class='sep'></div>
    <a class='btn' href='/stockwise-nosql/public/products.php'>Voir les produits →</a>
  </div>

  <div class='card'>
    <div class='small'>Fournisseurs</div>
    <div style='font-size:32px; font-weight:700; margin-top:6px;'>".htmlspecialchars((string)$suppliers)."</div>
    <div class='sep'></div>
    <a class='btn' href='/stockwise-nosql/public/suppliers.php'>Voir les fournisseurs →</a>
  </div>

  <div class='card'>
    <div class='small'>Commandes</div>
    <div style='font-size:32px; font-weight:700; margin-top:6px;'>".htmlspecialchars((string)$orders)."</div>
    <div class='sep'></div>
    <a class='btn' href='/stockwise-nosql/public/orders.php'>Voir les commandes →</a>
  </div>
</div>

{$lowHtml}
";

render_layout('Accueil', $content);