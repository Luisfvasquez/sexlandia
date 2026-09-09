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
        'name_split' => ['SEX', 'Landia'], // se pinta "SEX" + "Landia" (serif)
        'tagline' => 'Bienestar sexual · Boutique',
        'claim' => 'Curiosidad sin pena. +18.',
        'eyebrow' => 'BIENESTAR SEXUAL · SEXLANDIA BOUTIQUE — SIN MOMENTOS INCÓMODOS',
        'city_note' => 'OBJETOS PARA SENTIR · CARACAS',
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
        'address_lines' => ['Caracas, Venezuela'],
        'city' => 'Caracas',
        'region' => 'Distrito Capital',
        'country' => 'VE',
        'postal_code' => '1010',
        'latitude' => 10.48161802960652,
        'longitude' => -66.85832813655286,
        'price_range' => '$$',
        // URL "embed" de Google Maps (Compartir → Insertar un mapa → copiar src del iframe)
        'maps_embed_url' => 'https://www.google.com/maps?q=Caracas,Venezuela&output=embed',
        'maps_link' => 'https://maps.google.com/?q=Caracas,Venezuela',
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
        'title' => 'SEXLANDIA · Sex Shop en Caracas | Juguetes, Lubricantes y Bienestar Sexual',
        'title_suffix' => ' | SEXLANDIA',
        'description' => 'Sex shop en Caracas con catálogo real y stock verificado: succionadores, vibradores, lubricantes y accesorios de marcas originales. Empaque 100% discreto, asesoría sin pena, delivery y envíos a toda Venezuela.',
        'keywords' => 'sex shop caracas, juguetes sexuales venezuela, satisfyer caracas, lubricantes, vibradores, succionador de clitoris, bienestar sexual, tienda erotica caracas, sexshop delivery',
        'og_image' => null, // ruta absoluta o null → usa la primera foto de producto
        'author' => 'SEXLANDIA Boutique',
        'locale' => 'es_VE',
    ],
];
