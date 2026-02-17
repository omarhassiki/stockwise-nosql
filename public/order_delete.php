<?php
require_once __DIR__ . '/../src/db/mongo.php';

$id = $_GET['id'] ?? '';
if ($id) {
    $db = mongoDB();
    $db->orders->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
}
header("Location: /stockwise-nosql/public/orders.php");
exit;