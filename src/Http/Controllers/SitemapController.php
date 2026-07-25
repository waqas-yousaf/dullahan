<?php

namespace WaqasYousaf\Dulluhan\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use WaqasYousaf\Dulluhan\Models\Post;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        abort_unless(config('dulluhan.sitemap.enabled', true), 404);

        $urls = Post::query()
            ->published()
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (Post $post) => $this->urlNode($post))
            ->implode('');

        $imageNamespace = config('dulluhan.sitemap.include_images', true)
            ? ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"'
            : '';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . $imageNamespace . '>'
            . $urls
            . '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    private function urlNode(Post $post): string
    {
        $loc = $post->publicUrl();
        $lastmod = ($post->updated_at ?? $post->published_at ?? now())->toAtomString();
        $changefreq = config('dulluhan.sitemap.changefreq', 'weekly');
        $priority = config('dulluhan.sitemap.priority', '0.7');
        $image = $this->imageNode($post);

        return '<url>'
            . '<loc>' . e($loc) . '</loc>'
            . '<lastmod>' . e($lastmod) . '</lastmod>'
            . '<changefreq>' . e($changefreq) . '</changefreq>'
            . '<priority>' . e($priority) . '</priority>'
            . $image
            . '</url>';
    }

    private function imageNode(Post $post): string
    {
        if (! config('dulluhan.sitemap.include_images', true)) {
            return '';
        }

        $image = $post->og_image ?: $post->featured_image;

        if (! $image) {
            return '';
        }

        return '<image:image>'
            . '<image:loc>' . e($image) . '</image:loc>'
            . '<image:title>' . e($post->title) . '</image:title>'
            . '</image:image>';
    }
}
