<?php

require_once __DIR__ . '/../app/core/Autoloader.php';

Autoloader::register();

try {

    $db = Database::connect();

    echo "========================================\n";
    echo "     ZeroPing Migration Runner\n";
    echo "========================================\n\n";

    echo "✅ Connected to database.\n\n";

    /*
    |--------------------------------------------------------------------------
    | Create migrations table
    |--------------------------------------------------------------------------
    */

    $db->exec("
        CREATE TABLE IF NOT EXISTS migrations (

            id INT AUTO_INCREMENT PRIMARY KEY,

            migration VARCHAR(255) NOT NULL UNIQUE,

            batch INT NOT NULL,

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

        )
    ");

    /*
    |--------------------------------------------------------------------------
    | Read migration files
    |--------------------------------------------------------------------------
    */

    $migrationPath = __DIR__ . '/../database/migrations';

    $files = glob($migrationPath . '/*.php');

    if (empty($files)) {
        exit("❌ No migration files found.\n");
    }

    /*
    |--------------------------------------------------------------------------
    | Load executed migrations
    |--------------------------------------------------------------------------
    */

    $stmt = $db->query("SELECT migration FROM migrations");

    $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

    /*
    |--------------------------------------------------------------------------
    | Get next batch number
    |--------------------------------------------------------------------------
    */

    $stmt = $db->query("SELECT MAX(batch) FROM migrations");

    $batch = (int) $stmt->fetchColumn() + 1;

    /*
    |--------------------------------------------------------------------------
    | Run migrations
    |--------------------------------------------------------------------------
    */

    foreach ($files as $file) {

        $migration = basename($file);

        if (in_array($migration, $executed)) {

            echo "⏩ Skipping: {$migration}\n";

            continue;
        }

        $sql = require $file;

        echo "🚀 Running: {$migration}... ";

        $db->exec($sql);

        $insert = $db->prepare("
            INSERT INTO migrations (migration, batch)
            VALUES (?, ?)
        ");

        $insert->execute([$migration, $batch]);

        echo "Done ✅\n";
    }

    echo "\n🎉 All migrations completed successfully.\n";

} catch (PDOException $e) {

    echo "\n❌ Migration failed!\n";
    echo $e->getMessage() . PHP_EOL;

} catch (Exception $e) {

    echo "\n❌ Error!\n";
    echo $e->getMessage() . PHP_EOL;
}