<?php
require_once __DIR__ . '/../src/db/mongo.php';
require_once __DIR__ . '/../src/views/layout.php';

$db = mongoDB();

$q = trim($_GET['q'] ?? '');

$filter = [];
if ($q !== '') {
    $filter = [
        '$or' => [
            ['name' => new MongoDB\BSON\Regex(preg_quote($q), 'i')],
            ['email' => new MongoDB\BSON\Regex(preg_quote($q), 'i')],
        ]
    ];
}

$cursor = $db->suppliers->find($filter, ['sort' => ['created_at' => -1]]);

$rows = "";
foreach ($cursor as $s) {
    $id = (string)$s['_id'];
    $name = (string)($s['name'] ?? '');
    $email = (string)($s['email'] ?? '');
    $phone = (string)($s['phone'] ?? '');
    $rows .= "<tr>
        <td>".htmlspecialchars($name)."</td>
        <td>".htmlspecialchars($email)."</td>
        <td>".htmlspecialchars($phone)."</td>
        <td>
          <a href='/stockwise-nosql/public/supplier_edit.php?id={$id}'>Modifier</a> |
          <a href='/stockwise-nosql/public/supplier_delete.php?id={$id}' onclick=\"return confirm('Supprimer ce fournisseur ?');\">Supprimer</a>
        </td>
    </tr>";
}

$searchHtml = "
<form method='get' style='margin: 0 0 12px 0; display:flex; gap:8px; align-items:center; flex-wrap:wrap'>
  <input name='q' placeholder='Rechercher (nom, email)…' value='".htmlspecialchars($q)."' style='padding:8px; min-width:260px'>
  <button type='submit'>Rechercher</button>
  <a href='/stockwise-nosql/public/suppliers.php'>Reset</a>
  <span style='margin-left:auto'><a href='/stockwise-nosql/public/supplier_new.php'>➕ Ajouter un fournisseur</a></span>
</form>
";

$content = $searchHtml . "
  <h2>Liste complète des fournisseurs</h2>
  <table border='1' cellpadding='8' cellspacing='0'>
    <tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Actions</th></tr>
    {$rows}
  </table>
";

render_layout('Fournisseurs', $content);