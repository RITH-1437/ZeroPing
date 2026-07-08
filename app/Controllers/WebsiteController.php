<?php

namespace App\Controllers;

use App\Core\View\Controller;
use App\Services\DocsService;

class WebsiteController extends Controller
{
    public function home(): void
    {
        $this->view('site/home', [
            'title' => 'ZeroPing Framework',
            'active' => 'home',
            'stats' => [
                ['label' => 'CLI Commands', 'value' => '30+'],
                ['label' => 'Core Modules', 'value' => '18'],
                ['label' => 'Average Setup Time', 'value' => '< 3 min'],
                ['label' => 'Framework Version', 'value' => 'v1.0.0'],
            ],
        ], 'site');
    }

    public function features(): void
    {
        $this->view('site/features', [
            'title' => 'Features - ZeroPing',
            'active' => 'features',
        ], 'site');
    }

    public function documentation(): void
    {
        $docs = new DocsService();

        $this->view('site/documentation-index', [
            'title' => 'Documentation - ZeroPing',
            'active' => 'documentation',
            'documents' => $docs->documents(),
        ], 'site');
    }

    public function installation(): void
    {
        $this->view('site/installation', [
            'title' => 'Installation - ZeroPing',
            'active' => 'installation',
        ], 'site');
    }

    public function gettingStarted(): void
    {
        $this->view('site/getting-started', [
            'title' => 'Getting Started - ZeroPing',
            'active' => 'getting-started',
        ], 'site');
    }

    public function api(): void
    {
        $this->view('site/api', [
            'title' => 'API - ZeroPing',
            'active' => 'api',
        ], 'site');
    }

    public function roadmap(): void
    {
        $this->view('site/roadmap', [
            'title' => 'Roadmap - ZeroPing',
            'active' => 'roadmap',
        ], 'site');
    }

    public function github(): void
    {
        $this->view('site/github', [
            'title' => 'GitHub - ZeroPing',
            'active' => 'github',
            'repositoryUrl' => 'https://github.com/RITH-1437/ZeroPing',
        ], 'site');
    }

    public function docs(string $slug = 'introduction'): void
    {
        $docs = new DocsService();
        $doc = $docs->find($slug);

        if (!$doc) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Not Found']);
            return;
        }

        $markdown = $docs->loadMarkdown($slug) ?? '# Missing document';
        $rendered = $docs->render($markdown);
        $neighbors = $docs->neighbors($slug);

        $this->view('site/documentation', [
            'title' => $doc['title'] . ' - ZeroPing Docs',
            'active' => 'documentation',
            'documents' => $docs->documents(),
            'currentDoc' => $doc,
            'docHtml' => $rendered['html'],
            'toc' => $rendered['toc'],
            'previous' => $neighbors['previous'],
            'next' => $neighbors['next'],
        ], 'site');
    }
}
