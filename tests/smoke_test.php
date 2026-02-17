<?php
require_once __DIR__ . '/../src/db/mongo.php';

function assert_true($cond, $msg) {
    if (!$cond) {
        fwrite(STDERR, "FAIL: $msg\n");
        exit(1);
    }
    echo "OK: $msg\n";
}

$db = mongoDB();

// ping
$ping = $db->command(['ping' => 1])->toArray();
assert_true(isset($ping[0]), "MongoDB ping");

// collections exist / respond
$countProducts = $db->products->countDocuments();
assert_true(is_int($countProducts), "products countDocuments returns int");

$countSuppliers = $db->suppliers->countDocuments();
assert_true(is_int($countSuppliers), "suppliers countDocuments returns int");

$countOrders = $db->orders->countDocuments();
assert_true(is_int($countOrders), "orders countDocuments returns int");

echo "ALL TESTS PASSED ✅\n";