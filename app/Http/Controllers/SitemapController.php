<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Genera el sitemap.xml del sitio público.
     */
    public function __invoke(): Response
    {
        $urls = [
            ['loc' => route('storefront'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('nosotros'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => route('contacto'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('storefront.catalog'), 'priority' => '0.9', 'changefreq' => 'daily'],
        ];

        $products = Product::where('status', 'active')->latest('updated_at')->get(['slug', 'updated_at']);

        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('storefront.product', $product->slug),
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ];
        }

        $lastmod = $products->first()?->updated_at?->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $url) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.e($url['loc']).'</loc>'."\n";
            if ($lastmod) {
                $xml .= '    <lastmod>'.$lastmod.'</lastmod>'."\n";
            }
            $xml .= '    <changefreq>'.$url['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$url['priority'].'</priority>'."\n";
            $xml .= '  </url>'."\n";
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
