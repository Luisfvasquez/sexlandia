<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    /**
     * Genera el sitemap.xml del sitio público.
     *
     * Todas las URL se construyen sobre el dominio público declarado en
     * config/site.php ("url") para que coincidan exactamente con las etiquetas
     * <link rel="canonical"> de cada página.
     */
    public function __invoke(): Response
    {
        $base = rtrim(config('site.url'), '/');
        $now = now()->toAtomString();

        /** @var array<int, array{loc:string, lastmod:string, changefreq:string, priority:string, image?:string}> $urls */
        $urls = [
            ['loc' => $base.'/', 'lastmod' => $now, 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => $base.'/catalogo', 'lastmod' => $now, 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => $base.'/contacto', 'lastmod' => $now, 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => $base.'/nosotros', 'lastmod' => $now, 'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        // Páginas de categoría: landings valiosas para búsquedas del tipo
        // "vibradores en caracas", "lubricantes caracas", etc.
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->get();

        foreach ($categories as $category) {
            if ($category->products_count < 1) {
                continue;
            }

            $urls[] = [
                'loc' => $base.'/catalogo?category='.$category->id,
                'lastmod' => optional($category->updated_at)->toAtomString() ?? $now,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // Fichas de producto.
        $products = Product::query()
            ->where('status', 'active')
            ->with(['images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('id')])
            ->latest('updated_at')
            ->get(['id', 'slug', 'name', 'updated_at']);

        foreach ($products as $product) {
            $image = $product->images->first()?->path;

            if ($image && ! Str::startsWith($image, ['http://', 'https://', '/'])) {
                $image = $base.'/storage/'.$image;
            } elseif ($image && Str::startsWith($image, '/')) {
                $image = $base.$image;
            }

            $urls[] = array_filter([
                'loc' => $base.'/product/'.$product->slug,
                'lastmod' => optional($product->updated_at)->toAtomString() ?? $now,
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'image' => $image,
                'image_title' => $product->name,
            ]);
        }

        return response($this->render($urls), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * @param  array<int, array<string, string>>  $urls
     */
    private function render(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '
            .'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.e($url['loc']).'</loc>'."\n";
            $xml .= '    <lastmod>'.$url['lastmod'].'</lastmod>'."\n";
            $xml .= '    <changefreq>'.$url['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$url['priority'].'</priority>'."\n";

            if (! empty($url['image'])) {
                $xml .= '    <image:image>'."\n";
                $xml .= '      <image:loc>'.e($url['image']).'</image:loc>'."\n";
                if (! empty($url['image_title'])) {
                    $xml .= '      <image:title>'.e($url['image_title']).'</image:title>'."\n";
                }
                $xml .= '    </image:image>'."\n";
            }

            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return $xml;
    }
}
