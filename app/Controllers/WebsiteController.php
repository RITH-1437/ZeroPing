<?php

namespace App\Controllers;

use App\Core\View\Controller;
use App\Services\DocsService;

class WebsiteController extends Controller
{
    public function home(): string
    {
        return $this->view('site/home', [
            'title' => 'ZeroPing Framework',
            'active' => 'home',
            'stats' => [
                ['label' => 'CLI Commands', 'value' => '30+', 'icon' => '/assets/images/cli.png'],
                ['label' => 'Core Modules', 'value' => '18', 'icon' => '/assets/images/core.png'],
                ['label' => 'Average Setup Time', 'value' => '< 3 min', 'icon' => '/assets/images/time.png'],
                ['label' => 'Framework Version', 'value' => 'v' . \App\Core\Application\App::VERSION, 'icon' => '/assets/images/version.png'],
            ],
        ], 'site');
    }

    public function features(): string
    {
        return $this->view('site/features', [
            'title' => 'Features - ZeroPing',
            'active' => 'features',
        ], 'site');
    }

    public function documentation(): string
    {
        $docs = new DocsService();

        return $this->view('site/documentation-index', [
            'title' => 'Documentation - ZeroPing',
            'active' => 'documentation',
            'documents' => $docs->documents(),
        ], 'site');
    }

    public function installation(): string
    {
        return $this->view('site/installation', [
            'title' => 'Installation - ZeroPing',
            'active' => 'installation',
        ], 'site');
    }

    public function gettingStarted(): string
    {
        return $this->view('site/getting-started', [
            'title' => 'Getting Started - ZeroPing',
            'active' => 'getting-started',
        ], 'site');
    }

    public function api(): string
    {
        return $this->view('site/api', [
            'title' => 'API - ZeroPing',
            'active' => 'api',
        ], 'site');
    }

    public function roadmap(): string
    {
        return $this->view('site/roadmap', [
            'title' => 'Roadmap - ZeroPing',
            'active' => 'roadmap',
        ], 'site');
    }

    public function github(): string
    {
        return $this->view('site/github', [
            'title' => 'GitHub - ZeroPing',
            'active' => 'github',
            'repositoryUrl' => 'https://github.com/RITH-1437/ZeroPing',
        ], 'site');
    }

    public function docs(string $slug = 'introduction'): string
    {
        $docs = new DocsService();
        $doc = $docs->find($slug);

        if (!$doc) {
            http_response_code(404);
            return $this->view('errors/404', ['title' => 'Not Found', 'active' => 'documentation'], 'site');
        }

        $markdown = $docs->loadMarkdown($slug) ?? '# Missing document';
        $rendered = $docs->render($markdown);
        $neighbors = $docs->neighbors($slug);

        return $this->view('site/documentation', [
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
