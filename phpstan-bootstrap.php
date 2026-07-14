<?php

define('BASE_PATH', __DIR__);

$cacheDir = BASE_PATH . '/bootstrap/cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}
$cacheFile = $cacheDir . '/config.php';
if (!file_exists($cacheFile)) {
    file_put_contents($cacheFile, '<?php return [];');
}
