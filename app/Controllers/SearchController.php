<?php

namespace App\Controllers;

use App\Services\SearchIndex;

class SearchController
{
    public function search(): void
    {
        $query = $_GET['q'] ?? '';

        $query = trim($query);

        if ($query === '') {
            header('Content-Type: application/json');
            echo json_encode(['results' => [], 'query' => '']);
            return;
        }

        $searchIndex = new SearchIndex();
        $results = $searchIndex->search($query);

        header('Content-Type: application/json');
        echo json_encode([
            'results' => $results,
            'query' => $query,
            'count' => count($results),
        ]);
    }

    public function build(): void
    {
        $searchIndex = new SearchIndex();
        $searchIndex->build();

        echo "Search index built successfully.";
    }
}
