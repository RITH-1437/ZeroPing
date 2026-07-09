<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

use App\Core\Database\Database;

try {

    Database::connect();

    echo "✅ Connected to ZeroPing Database";

} catch (PDOException $e) {

    echo "❌ " . $e->getMessage();
}