@props([
    'product' => null,
    'image' => null,
    'alt' => null,
    'class' => '',
    'size' => 'md',
    'eager' => false,
])
@php
    $model = $product && $product->relationLoaded('images') ? $product->images->first() : (optional($product)->images?->first());
    $src = null;

    if ($model) {
        $src = $model->variantUrl($size);
    } elseif ($image) {
        $p = \Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/']) ? $image : '/storage/' . $image;
        // Deriva la rendition si es una ruta de nuestro storage
        if ($size !== 'full' && \Illuminate\Support\Str::startsWith($p, '/storage/')) {
            $rel = \Illuminate\Support\Str::after($p, '/storage/');
            $variant = \App\Services\ProductImageService::variantPath($rel, $size);
            $p = \Illuminate\Support\Facades\Storage::disk('public')->exists($variant) ? '/storage/' . $variant : $p;
        }
        $src = $p;
    }

    $label = $alt
        ?? trim(
            ($product->name ?? 'Producto SEXLANDIA') .
            ($product && $product->category ? ' · ' . $product->category->name : '')
        );
    $dims = match ($size) {
        'thumb' => ['w' => 400, 'h' => 500],
        'full' => ['w' => 1600, 'h' => 2000],
        default => ['w' => 900, 'h' => 1125],
    };
@endphp
@if ($src)
    <img
        src="{{ $src }}"
        alt="{{ $label }}"
        {{ $attributes->merge(['class' => $class]) }}
        width="{{ $dims['w'] }}" height="{{ $dims['h'] }}"
        loading="{{ $eager ? 'eager' : 'lazy' }}"
        decoding="async"
        {{ $eager ? 'fetchpriority=high' : '' }}>
@else
    <span {{ $attributes->merge(['class' => 'ph-media ' . $class]) }} role="img" aria-label="{{ $label }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
    </span>
@endif
