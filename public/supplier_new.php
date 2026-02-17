<?php
require_once __DIR__ . '/../src/db/mongo.php';
require_once __DIR__ . '/../src/views/layout.php';

$db = mongoDB();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $lat = (float)($_POST['lat'] ?? 0);
    $lng = (float)($_POST['lng'] ?? 0);

    if ($name === '' || $email === '') {
        $error = "Nom et Email sont obligatoires.";
    } else {
        $db->suppliers->insertOne([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'location' => [
                'type' => 'Point',
                'coordinates' => [$lng, $lat] // [longitude, latitude]
            ],
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);
        header("Location: /stockwise-nosql/public/suppliers.php");
        exit;
    }
}

$form = ($error ? "<p style='color:red'>".htmlspecialchars($error)."</p>" : "") . "
<form method='post' style='display:grid; gap:10px; max-width:520px'>
  <label>Nom <input name='name' required></label>
  <label>Email <input name='email' required></label>
  <label>Téléphone <input name='phone'></label>

  <h3 style='margin:10px 0 0 0'>Coordonnées (optionnel)</h3>
  <div style='display:flex; gap:10px'>
    <label>Latitude <input name='lat' type='number' step='0.0001' value='48.8566'></label>
    <label>Longitude <input name='lng' type='number' step='0.0001' value='2.3522'></label>
  </div>

  <button type='submit'>Créer</button>
  <a href='/stockwise-nosql/public/suppliers.php'>Annuler</a>
</form>
";

render_layout("Ajouter un fournisseur", $form);