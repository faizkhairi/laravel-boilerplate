<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;
use League\CommonMark\CommonMarkConverter;

class DocController extends Controller
{
    protected string $docsPath;

    public function __construct()
    {
        $this->docsPath = base_path('docs');
    }

    /**
     * List docs and show index.
     */
    public function index(): Response
    {
        $docs = $this->listDocs();
        $content = $this->getDocContent('index');
        return Inertia::render('Docs/Index', [
            'docs' => $docs,
            'slug' => 'index',
            'content' => $content ?? '',
        ]);
    }

    /**
     * Show a single doc by slug.
     */
    public function show(string $slug): Response
    {
        $docs = $this->listDocs();
        $content = $this->getDocContent($slug);
        if ($content === null) {
            abort(404);
        }
        return Inertia::render('Docs/Show', [
            'docs' => $docs,
            'slug' => $slug,
            'content' => $content,
        ]);
    }

    protected function listDocs(): array
    {
        $files = File::files($this->docsPath);
        $docs = [];
        foreach ($files as $file) {
            if (strtolower($file->getExtension()) === 'md') {
                $slug = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $title = ucfirst(str_replace('-', ' ', $slug));
                $docs[] = ['slug' => $slug, 'title' => $title];
            }
        }
        usort($docs, fn ($a, $b) => strcmp($a['slug'], $b['slug']));
        return $docs;
    }

    /**
     * Get doc content as HTML. Slug is validated (alphanumeric + hyphen only); content is from repo docs/*.md only.
     * Rendered with League CommonMark (HTML escaped). Safe for v-html when docs are not user-controlled.
     */
    protected function getDocContent(string $slug): ?string
    {
        if (! preg_match('/^[a-z0-9\-]+$/', $slug)) {
            return null;
        }
        $path = $this->docsPath.DIRECTORY_SEPARATOR.$slug.'.md';
        if (! File::isFile($path)) {
            return null;
        }
        $markdown = File::get($path);
        $converter = new CommonMarkConverter();
        return $converter->convert($markdown)->getContent();
    }
}
