<?php
require_once __DIR__ . '/../src/db/mongo.php';
require_once __DIR__ . '/../src/views/layout.php';

$db = mongoDB();
$error = "";

$id = $_GET['id'] ?? '';
if (!$id) { render_layout("Erreur", "<p>ID manquant.</p>"); exit; }

$supplier = $db->suppliers->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
if (!$supplier) { render_layout("Erreur", "<p>Fournisseur introuvable.</p>"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $lat = (float)($_POST['lat'] ?? 0);
    $lng = (float)($_POST['lng'] ?? 0);

    if ($name === '' || $email === '') {
        $error = "Nom et Email sont obligatoires.";
    } else {
        $db->suppliers->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$set' => [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'location' => ['type'=>'Point','coordinates'=>[$lng,$lat]]
            ]]
        );
        header("Location: /stockwise-nosql/public/suppliers.php");
        exit;
    }
}

$coords = $supplier['location']['coordinates'] ?? [0,0];
$lng0 = $coords[0] ?? 0;
$lat0 = $coords[1] ?? 0;

$form = ($error ? "<p style='color:red'>".htmlspecialchars($error)."</p>" : "") . "
<form method='post' style='display:grid; gap:10px; max-width:520px'>
  <label>Nom <input name='name' required value='".htmlspecialchars((string)$supplier['name'])."'></label>
  <label>Email <input name='email' required value='".htmlspecialchars((string)$supplier['email'])."'></label>
  <label>Téléphone <input name='phone' value='".htmlspecialchars((string)($supplier['phone'] ?? ''))."'></label>

  <h3 style='margin:10px 0 0 0'>Coordonnées</h3>
  <div style='display:flex; gap:10px'>
    <label>Latitude <input name='lat' type='number' step='0.0001' value='".htmlspecialchars((string)$lat0)."'></label>
    <label>Longitude <input name='lng' type='number' step='0.0001' value='".htmlspecialchars((string)$lng0)."'></label>
  </div>

  <button type='submit'>Enregistrer</button>
  <a href='/stockwise-nosql/public/suppliers.php'>Annuler</a>
</form>
";

render_layout("Modifier le fournisseur", $form);