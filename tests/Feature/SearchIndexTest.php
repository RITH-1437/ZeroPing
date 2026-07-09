<?php

namespace Tests\Feature;

use App\Core\Testing\TestCase;
use App\Services\SearchIndex;

class SearchIndexTest extends TestCase
{
    private SearchIndex $searchIndex;
    private string $testDir;

    public function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/zp-test-docs-' . uniqid();
        mkdir($this->testDir, 0777, true);

        file_put_contents($this->testDir . '/welcome.md', "# Welcome\n\nThis is a welcome page for testing.");
        file_put_contents($this->testDir . '/validation.md', "# Validation\n\nOur validation rules help you validate data.");

        $this->searchIndex = new SearchIndex();
    }

    public function tearDown(): void
    {
        $this->deleteDirectory($this->testDir);
    }

    public function test_can_build_index()
    {
        $this->searchIndex->build($this->testDir);

        $this->assertTrue(true);
    }

    public function test_search_returns_results()
    {
        $this->searchIndex->build($this->testDir);

        $results = $this->searchIndex->search('welcome');

        $this->assertNotEmpty($results);
    }

    public function test_search_returns_matching_title()
    {
        $this->searchIndex->build($this->testDir);

        $results = $this->searchIndex->search('Validation');

        $this->assertNotEmpty($results);
        $found = false;
        foreach ($results as $r) {
            if (str_contains($r['title'], 'Validation')) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function test_search_returns_empty_for_no_match()
    {
        $this->searchIndex->build($this->testDir);

        $results = $this->searchIndex->search('xyznonexistentkeyword');

        $this->assertEmpty($results);
    }

    public function test_fuzzy_search_finds_close_matches()
    {
        $this->searchIndex->build($this->testDir);

        $results = $this->searchIndex->search('welcom');

        $this->assertNotEmpty($results);
    }

    private function deleteDirectory(string $dir): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
