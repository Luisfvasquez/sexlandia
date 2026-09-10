<?php

/*
|--------------------------------------------------------------------------
| Configuración editable del sitio público (SEXLANDIA)
|--------------------------------------------------------------------------
|
| Todo el contenido "de marketing" del landing vive aquí para que puedas
| editarlo sin tocar las vistas. Textos, datos de contacto, ubicación,
| FAQ, testimonios y valores por defecto de SEO.
|
| Tras editar este archivo ejecuta:  php artisan config:clear
|
*/

return [

    // -------------------------------------------------------------------
    // Marca
    // -------------------------------------------------------------------
    'brand' => [
        'name' => 'SEXLANDIA',
        'legal_name' => 'SEXLANDIA Boutique C.A.', // TODO PRODUCCIÓN: razón social real (para JSON-LD Organization)
        'name_split' => ['SEX', 'Landia'], // se pinta "SEX" + "Landia" (serif)
        'tagline' => 'Bienestar sexual · Boutique',
        'claim' => 'Curiosidad sin pena. +18.',
        'eyebrow' => 'BIENESTAR SEXUAL · SEXLANDIA BOUTIQUE — SIN MOMENTOS INCÓMODOS',
        'city_note' => 'OBJETOS PARA SENTIR · CARACAS',
        // Descripción corta de la empresa (JSON-LD Organization / Store).
        'description' => 'Sex shop en Caracas especializado en juguetes íntimos, lubricantes y bienestar sexual, con marcas originales, empaque 100% discreto, asesoría sin pena y delivery en toda Venezuela.',
        'founding_date' => '2023', // TODO PRODUCCIÓN: año de apertura real
    ],

    // Activa la advertencia de contenido para adultos en la primera visita
    'age_gate' => true,

    // -------------------------------------------------------------------
    // URL pública canónica (sin barra final).
    // -------------------------------------------------------------------
    // Se usa para construir enlaces que salen del sitio (mensajes de WhatsApp,
    // etiquetas canónicas, JSON-LD, Open Graph). En producción define en el .env:
    //   SITE_URL=https://sexlandiaboutique.com
    // (si no, cae a APP_URL). Así los enlaces compartidos nunca muestran el
    // dominio de desarrollo (sexlandia.test).
    'url' => rtrim(env('SITE_URL', env('APP_URL', 'http://localhost')), '/'),

    // -------------------------------------------------------------------
    // Contacto
    // -------------------------------------------------------------------
    'contact' => [
        // Número en formato internacional SIN "+" ni espacios (para wa.me)
        'whatsapp' => '584120000000',
        'whatsapp_display' => '+58 412 000 0000',
        'email' => 'hola@sexlandia.com',
        'instagram' => 'https://www.instagram.com/sexlandiaboutique/',
        'instagram_handle' => '@sexlandiaboutique',
        /* 'tiktok' => 'https://tiktok.com/@sexlandia', */
    ],

    // -------------------------------------------------------------------
    // Ubicación / Tienda física
    // -------------------------------------------------------------------
    'location' => [
        // Nombre EXACTO de la ficha de Google Business Profile. Debe coincidir
        // carácter por carácter en el sitio, Google y cualquier directorio.
        'place_name' => 'Sexshop | Sexlandia boutique',
        'street' => 'Quinta Mary, Calle París',
        'sector' => 'Las Mercedes',
        'municipality' => 'Baruta',
        'address_lines' => ['Quinta Mary, Calle París', 'Las Mercedes', 'Caracas 1060, Miranda'],
        'city' => 'Caracas',
        'region' => 'Miranda',
        'country' => 'VE',
        'postal_code' => '1060',
        'latitude' => 10.481575269450095,
        'longitude' => -66.85814419176661,
        'price_range' => '$$',
        // Zonas que atiende (delivery / envíos). Alimenta `areaServed` en JSON-LD.
        'area_served' => ['Caracas', 'Las Mercedes', 'Chacao', 'Baruta', 'Distrito Capital', 'Miranda', 'La Guaira', 'Venezuela'],
        'currencies_accepted' => 'USD, VES',
        'payment_accepted' => 'Efectivo, Pago Móvil, Zelle, Transferencia bancaria, Punto de venta',
        // Mapa embebido (coordenadas exactas del local).
        'maps_embed_url' => 'https://www.google.com/maps?q=10.481575269450095,-66.85814419176661&z=17&output=embed',
        // Enlace a la ficha real de Google Maps (CID del negocio). Se usa como
        // `maps_link`, `hasMap` en JSON-LD y `sameAs` (vincula la entidad con su
        // ficha de Google → refuerza la señal local).
        'maps_link' => 'https://www.google.com/maps?cid=9859455897173022925',
        'map_place_url' => 'https://www.google.com/maps?cid=9859455897173022925',
        // Horario legible + estructurado para SEO (día: Mo-Sa, abre, cierra)
        'hours_text' => ['Lun — Sáb / 10:00 — 19:00', 'Envíos y delivery todos los días'],
        'opening_hours' => [
            ['days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'], 'opens' => '10:00', 'closes' => '19:00'],
        ],
    ],

    // -------------------------------------------------------------------
    // Hero
    // -------------------------------------------------------------------
    'hero' => [
        'title' => ['Siente', 'algo', 'DISTINTO.'],
        'text' => 'Objetos premium elegidos para el placer, la intimidad y la curiosidad. Descubre nuestra colección oficial disponible hoy en Caracas.',
        'cta' => 'VER LA COLECCIÓN',
    ],

    // -------------------------------------------------------------------
    // Nosotros
    // -------------------------------------------------------------------
    'about' => [
        'eyebrow' => '¿POR QUÉ SEXLANDIA?',
        'heading' => ['El placer debería', 'sentirse natural.'],
        'paragraphs' => [
            'Creemos que descubrir algo nuevo no debería sentirse incómodo ni intimidante.',
            'Por eso creamos un espacio en Caracas donde puedes mirar, informarte y elegir con tranquilidad y estilo.',
            'SEXLANDIA nació para hacer que comprar productos íntimos se sienta mucho más normal, divertido, cómodo y bonito.',
        ],
        'staccato' => ['Sin juicios.', 'Sin presión.', 'Solo curiosidad.'],
        'story_heading' => ['Una tienda', 'para gente', 'curiosa.'],
    ],

    // -------------------------------------------------------------------
    // Beneficios ("Bueno saberlo")
    // -------------------------------------------------------------------
    'benefits' => [
        ['01', 'EMPAQUE 100% DISCRETO', 'Cajas neutras y selladas. Tu privacidad es sagrada.'],
        ['02', 'STOCK REAL EN TIENDA', 'Sabes qué hay disponible antes de venir a visitarnos.'],
        ['03', 'CERO PREGUNTAS INCÓMODAS', 'Te explicamos todo con naturalidad y buena onda.'],
        ['04', 'MARCAS ORIGINALES', 'Satisfyer, CalExotics, Pure Instinct, ID Lubricants.'],
    ],

    // -------------------------------------------------------------------
    // Preguntas frecuentes
    // -------------------------------------------------------------------
    'faq' => [
        ['¿Todo es discreto y privado?', 'Sí, absolutamente. Cuidamos tu privacidad desde la primera consulta hasta el empaque exterior 100% neutro y sin marcas.'],
        ['¿Puedo revisar la disponibilidad real antes de ir a tienda?', 'Sí. El catálogo de SEXLANDIA refleja las existencias actuales de nuestra tienda física en Caracas.'],
        ['¿Puedo reservar mi pedido antes de visitarlos?', '¡Por supuesto! Puedes contactarnos directo por WhatsApp para apartar tu producto y retirarlo cómodamente.'],
        ['¿Ofrecen asesoría personalizada sin pena?', 'Totalmente. Nuestro equipo está para guiarte, responder dudas y ayudarte a elegir con total naturalidad y cero juicios.'],
        ['¿Hacen envíos y delivery?', 'Sí, contamos con servicio de delivery rápido y discreto en Caracas y envíos seguros a nivel nacional.'],
        ['¿Cuáles son los métodos de pago aceptados?', 'Aceptamos Pago Móvil, Zelle, transferencias bancarias, efectivo y punto de venta en tienda.'],
    ],

    // -------------------------------------------------------------------
    // Testimonios
    // -------------------------------------------------------------------
    'testimonials' => [
        ['quote' => 'Pensé que sería incómodo. Terminó siendo divertido y súper relajado.', 'author' => 'Cliente Sexlandia Caracas', 'product' => 'Succionador de clítoris'],
        ['quote' => 'Me explicaron todo con calma y el empaque fue totalmente discreto.', 'author' => 'Comprador verificado', 'product' => 'Lubricantes base agua'],
        ['quote' => 'Excelente calidad y asesoría. Los mejores juguetes en Caracas sin duda.', 'author' => 'Cliente frecuente', 'product' => 'Vibrador recargable'],
    ],

    // -------------------------------------------------------------------
    // Producto destacado (id de la BD). null = primer producto activo con foto.
    // -------------------------------------------------------------------
    'featured_product_id' => null,

    // -------------------------------------------------------------------
    // SEO por defecto
    // -------------------------------------------------------------------
    'seo' => [
        'title' => 'Sex Shop en Caracas | SEXLANDIA — Juguetes, Lubricantes y Bienestar Sexual',
        'title_suffix' => ' | SEXLANDIA',
        'description' => 'Sexshop en Caracas con catálogo real y stock verificado: succionadores, vibradores, lubricantes y accesorios de marcas originales. Empaque 100% discreto, asesoría sin pena, delivery en Caracas y envíos a toda Venezuela.',
        'keywords' => 'sexshop caracas, sex shop caracas, sex shop en caracas, tienda erotica caracas, juguetes sexuales caracas, juguetes sexuales venezuela, satisfyer caracas, lubricantes caracas, vibradores caracas, succionador de clitoris, bienestar sexual, sexshop delivery caracas',
        // Frase corta que se antepone (solo para lectores de pantalla y buscadores)
        // al H1 editorial de la portada, para que el H1 indexable sea relevante.
        'h1_prefix' => 'Sex shop en Caracas —',
        // Imagen para compartir (Open Graph / Twitter / JSON-LD). 1200×630 px.
        // Deja una imagen real en public/ y apunta aquí; si es null se usa el logo
        // y, en su defecto, la primera foto de producto.
        'og_image' => '/sexlandia/og-image.jpg', // TODO PRODUCCIÓN: subir public/sexlandia/og-image.jpg (1200×630)
        'logo' => '/sexlandia/logo.jpg',
        'author' => 'SEXLANDIA Boutique',
        'locale' => 'es_VE',
        'twitter_site' => '', // p.ej. '@sexlandiaboutique' si abren cuenta en X
        // Verificación de propiedad (pega aquí SOLO el valor del token, sin comillas
        // extra). Deja en '' si aún no lo tienes; la etiqueta no se pinta si va vacío.
        'google_site_verification' => env('GOOGLE_SITE_VERIFICATION', ''),
        'facebook_domain_verification' => env('FACEBOOK_DOMAIN_VERIFICATION', ''),
        // Geolocalización para meta tags "geo.*" (algunos motores locales las leen).
        'geo_region' => 'VE-M',       // ISO 3166-2 del estado Miranda (Las Mercedes, Baruta)
        'geo_placename' => 'Las Mercedes, Caracas',
    ],
];
