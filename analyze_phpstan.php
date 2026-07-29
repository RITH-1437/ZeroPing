<?php
$data = json_decode(file_get_contents('phpstan-errors.json'), true);
$errors = [];
foreach ($data['files'] ?? [] as $file => $fileErrors) {
    foreach ($fileErrors['errors'] ?? [] as $e) {
        $msg = $e['message'] ?? '';
        $id = $e['identifier'] ?? 'unknown';
        $key = $id . ' | ' . $msg;
        if (!isset($errors[$key])) {
            $errors[$key] = ['count' => 0, 'identifier' => $id, 'message' => $msg];
        }
        $errors[$key]['count']++;
    }
}
usort($errors, fn($a, $b) => $b['count'] - $a['count']);
foreach ($errors as $e) {
    printf("%4d  %s\n  %s\n\n", $e['count'], $e['identifier'], substr($e['message'], 0, 150));
}
echo "\nTotal unique error types: " . count($errors) . "\n";
echo "Total errors: " . array_sum(array_column($errors, 'count')) . "\n";
