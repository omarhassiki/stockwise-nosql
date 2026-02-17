<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;

function mongoDB(): MongoDB\Database {
    $client = new Client("mongodb://localhost:27017");
    return $client->selectDatabase("stockwise");
}