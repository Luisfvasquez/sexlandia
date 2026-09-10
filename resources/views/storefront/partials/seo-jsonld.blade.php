@php
    /*
    |--------------------------------------------------------------------------
    | Datos estructurados (schema.org / JSON-LD)
    |--------------------------------------------------------------------------
    | Parámetros opcionales al incluir el parcial:
    |   $extraSchemas       array → nodos adicionales (Product, BreadcrumbList, …)
    |   $includeFaq         bool  → emite FAQPage (solo si el FAQ es visible en la página)
    |   $includeProductList bool  → emite ItemList (solo donde se listan productos)
    |   $siteSeoImage       string → imagen destacada explícita
    */
    $brand = config('site.brand.name');
    $c = config('site.contact');
    $loc = config('site.location');
    $seo = config('site.seo');
    $home = rtrim(config('site.url'), '/').'/';
    $includeFaq = $includeFaq ?? false;
    $includeProductList = $includeProductList ?? false;

    $abs = function (?string $path) use ($home) {
        if (! $path) {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return rtrim($home, '/').'/'.ltrim($path, '/');
    };

    $sameAs = array_values(array_filter([
        $c['instagram'] ?? null,
        $c['tiktok'] ?? null,
        $c['facebook'] ?? null,
        // Vincula la entidad con su ficha de Google Maps (señal local).
        $loc['map_place_url'] ?? null,
    ]));

    $logo = $abs($seo['logo'] ?? null);

    // og_image de config solo si el archivo local realmente existe (evita
    // referenciar una imagen que aún no se ha subido).
    $configOg = $seo['og_image'] ?? null;
    if ($configOg && ! \Illuminate\Support\Str::startsWith($configOg, ['http://', 'https://'])
        && ! file_exists(public_path(ltrim($configOg, '/')))) {
        $configOg = null;
    }

    // Imagen: explícita → og_image de config → primera foto de producto → logo.
    $ogImage = $siteSeoImage
        ?? $abs($configOg)
        ?? optional(optional(($products ?? collect())->first(fn ($p) => $p->images->count()))->images)->first()?->path;
    if ($ogImage && ! \Illuminate\Support\Str::startsWith($ogImage, ['http', '/'])) {
        $ogImage = $abs('storage/'.$ogImage);
    } elseif ($ogImage && \Illuminate\Support\Str::startsWith($ogImage, '/')) {
        $ogImage = url($ogImage);
    }
    $ogImage = $ogImage ?: $logo;

    $telephone = '+'.preg_replace('/\D/', '', $c['whatsapp']);

    $areaServed = collect($loc['area_served'] ?? [$loc['city']])->map(fn ($place) => [
        '@type' => \in_array($place, ['Venezuela', 'VE'], true) ? 'Country' : 'AdministrativeArea',
        'name' => $place,
    ])->all();

    // -----------------------------------------------------------------
    // Organization (identidad de marca; alimenta el panel de conocimiento)
    // -----------------------------------------------------------------
    $organization = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        '@id' => $home.'#organization',
        'name' => $brand,
        'legalName' => config('site.brand.legal_name') ?: $brand,
        'url' => $home,
        'email' => $c['email'] ?? null,
        'foundingDate' => config('site.brand.founding_date') ?: null,
        'description' => config('site.brand.description') ?: $seo['description'],
        'slogan' => config('site.brand.claim') ?: null,
        'logo' => $logo ? [
            '@type' => 'ImageObject',
            '@id' => $home.'#logo',
            'url' => $logo,
            'contentUrl' => $logo,
            'caption' => $brand,
        ] : null,
        'image' => $ogImage ? ['@id' => $home.'#logo'] : null,
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => $telephone,
            'contactType' => 'customer service',
            'areaServed' => 'VE',
            'availableLanguage' => ['es'],
        ],
        'areaServed' => $areaServed,
        'sameAs' => $sameAs,
    ]);

    // -----------------------------------------------------------------
    // Store (negocio local: la señal clave para "sexshop en caracas")
    // -----------------------------------------------------------------
    $store = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Store',
        '@id' => $home.'#store',
        'name' => $brand,
        'alternateName' => 'Sex Shop '.$loc['city'],
        'description' => $seo['description'],
        'url' => $home,
        'telephone' => $telephone,
        'email' => $c['email'] ?? null,
        'currenciesAccepted' => $loc['currencies_accepted'] ?? 'USD, VES',
        'paymentAccepted' => $loc['payment_accepted'] ?? 'Efectivo, Pago Móvil, Zelle, Transferencia',
        'priceRange' => $loc['price_range'] ?? '$$',
        'parentOrganization' => ['@id' => $home.'#organization'],
        'brand' => ['@id' => $home.'#organization'],
        'image' => $ogImage ?: null,
        'logo' => $logo ?: null,
        'hasMap' => $loc['map_place_url'] ?? $loc['maps_link'] ?? null,
        'knowsLanguage' => 'es',
        'address' => array_filter([
            '@type' => 'PostalAddress',
            // Debe coincidir con Google Business Profile: calle + sector en
            // streetAddress, "Caracas" como localidad, estado en addressRegion.
            'streetAddress' => trim(($loc['street'] ?? implode(', ', $loc['address_lines']))
                .(! empty($loc['sector']) ? ', '.$loc['sector'] : '')),
            'addressLocality' => $loc['city'],
            'addressRegion' => $loc['region'] ?? null,
            'postalCode' => $loc['postal_code'] ?? null,
            'addressCountry' => $loc['country'] ?? 'VE',
        ]),
        'geo' => array_filter([
            '@type' => 'GeoCoordinates',
            'latitude' => $loc['latitude'] ?? null,
            'longitude' => $loc['longitude'] ?? null,
        ]),
        'areaServed' => $areaServed,
        'openingHoursSpecification' => collect($loc['opening_hours'] ?? [])->map(fn ($h) => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => $h['days'],
            'opens' => $h['opens'],
            'closes' => $h['closes'],
        ])->all(),
        'sameAs' => $sameAs,
    ]);

    // -----------------------------------------------------------------
    // WebSite (+ SearchAction para la sitelinks searchbox)
    // -----------------------------------------------------------------
    $website = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => $home.'#website',
        'url' => $home,
        'name' => $brand,
        'description' => $seo['description'],
        'inLanguage' => 'es-VE',
        'publisher' => ['@id' => $home.'#organization'],
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => rtrim($home, '/').'/catalogo?search={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    $graph = [$organization, $store, $website];

    // -----------------------------------------------------------------
    // ItemList (solo cuando la página realmente lista productos)
    // -----------------------------------------------------------------
    $productList = ($products ?? collect());
    if ($includeProductList && $productList->isNotEmpty()) {
        $graph[] = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Catálogo '.$brand,
            'itemListElement' => $productList->take(30)->values()->map(function ($p, $i) use ($brand, $abs) {
                $img = $p->images->first()?->path;
                if ($img && ! \Illuminate\Support\Str::startsWith($img, ['http', '/'])) {
                    $img = $abs('storage/'.$img);
                }
                $inStock = ! $p->track_inventory || $p->allow_negative_stock || ($p->inventory && $p->inventory->stock > 0);

                return [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'item' => array_filter([
                        '@type' => 'Product',
                        'name' => $p->name,
                        'description' => $p->description ?: ('Producto disponible en '.$brand),
                        'brand' => $p->brand ? ['@type' => 'Brand', 'name' => $p->brand] : null,
                        'category' => $p->category->name ?? null,
                        'image' => $img ?: null,
                        'sku' => $p->sku ?: null,
                        'url' => $p->public_url,
                        'offers' => [
                            '@type' => 'Offer',
                            'price' => number_format($p->display_price, 2, '.', ''),
                            'priceCurrency' => 'USD',
                            'availability' => $inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                            'url' => $p->public_url,
                        ],
                    ]),
                ];
            })->all(),
        ];
    }

    // -----------------------------------------------------------------
    // FAQPage (solo si el bloque FAQ es visible en la página)
    // -----------------------------------------------------------------
    if ($includeFaq) {
        $graph[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect(config('site.faq'))->map(fn ($f) => [
                '@type' => 'Question',
                'name' => $f[0],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
            ])->all(),
        ];
    }

    if (! empty($extraSchemas ?? [])) {
        $graph = array_merge($graph, $extraSchemas);
    }
@endphp
@push('jsonld')
    @foreach ($graph as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endforeach
@endpush
