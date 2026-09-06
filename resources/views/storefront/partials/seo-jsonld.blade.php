@php
    $brand = config('site.brand.name');
    $c = config('site.contact');
    $loc = config('site.location');
    $home = route('storefront');

    $sameAs = array_values(array_filter([$c['instagram'] ?? null, $c['tiktok'] ?? null]));

    $ogImage = $siteSeoImage ?? (optional(optional(($products ?? collect())->first(fn ($p) => $p->images->count()))->images)->first()?->path);
    if ($ogImage && ! \Illuminate\Support\Str::startsWith($ogImage, ['http', '/'])) {
        $ogImage = url('/storage/' . $ogImage);
    } elseif ($ogImage && \Illuminate\Support\Str::startsWith($ogImage, '/')) {
        $ogImage = url($ogImage);
    }

    $store = [
        '@context' => 'https://schema.org',
        '@type' => 'Store',
        '@id' => $home . '#store',
        'name' => $brand,
        'description' => config('site.seo.description'),
        'url' => $home,
        'telephone' => '+' . preg_replace('/\D/', '', $c['whatsapp']),
        'email' => $c['email'] ?? null,
        'priceRange' => $loc['price_range'] ?? '$$',
        'image' => $ogImage ?: null,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => implode(', ', $loc['address_lines']),
            'addressLocality' => $loc['city'],
            'addressRegion' => $loc['region'] ?? null,
            'postalCode' => $loc['postal_code'] ?? null,
            'addressCountry' => $loc['country'] ?? 'VE',
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => $loc['latitude'] ?? null,
            'longitude' => $loc['longitude'] ?? null,
        ],
        'openingHoursSpecification' => collect($loc['opening_hours'] ?? [])->map(fn ($h) => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => $h['days'],
            'opens' => $h['opens'],
            'closes' => $h['closes'],
        ])->all(),
        'sameAs' => $sameAs,
    ];

    $website = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => $home . '#website',
        'url' => $home,
        'name' => $brand,
        'inLanguage' => 'es',
        'publisher' => ['@id' => $home . '#store'],
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => route('storefront.catalog') . '?search={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];

    $rate = $exchangeRate ? (float) str_replace(',', '.', $exchangeRate) : 1;
    if ($rate <= 0) { $rate = 1; }
    $itemList = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => 'Catálogo ' . $brand,
        'itemListElement' => ($products ?? collect())->take(30)->values()->map(function ($p, $i) use ($rate, $brand) {
            $img = $p->images->first()?->path;
            if ($img && ! \Illuminate\Support\Str::startsWith($img, ['http', '/'])) {
                $img = url('/storage/' . $img);
            }
            $inStock = ! $p->track_inventory || $p->allow_negative_stock || ($p->inventory && $p->inventory->stock > 0);
            return [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => array_filter([
                    '@type' => 'Product',
                    'name' => $p->name,
                    'description' => $p->description ?: ('Producto disponible en ' . $brand),
                    'brand' => $p->brand ? ['@type' => 'Brand', 'name' => $p->brand] : null,
                    'category' => $p->category->name ?? null,
                    'image' => $img ?: null,
                    'sku' => $p->sku ?: null,
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => number_format($p->display_price, 2, '.', ''),
                        'priceCurrency' => 'USD',
                        'availability' => $inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                        'url' => route('storefront.catalog'),
                    ],
                ]),
            ];
        })->all(),
    ];

    $faqPage = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect(config('site.faq'))->map(fn ($f) => [
            '@type' => 'Question',
            'name' => $f[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
        ])->all(),
    ];

    $graph = [$store, $website, $itemList, $faqPage];
    if (! empty($extraSchemas ?? [])) {
        $graph = array_merge($graph, $extraSchemas);
    }
@endphp
@push('jsonld')
    @foreach ($graph as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endforeach
@endpush
