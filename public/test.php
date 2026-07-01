<?php

require_once '../app/core/Database.php';

try {

    Database::connect();

    echo "✅ Connected to ZeroPing Database";

} catch (PDOException $e) {

    echo "❌ " . $e->getMessage();
}