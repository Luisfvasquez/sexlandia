<?php

$dir = __DIR__.'/app/Models';
$files = glob($dir.'/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);

    // 1. Refactor $casts to casts() method
    if (preg_match('/protected\s+\$casts\s*=\s*(\[.*?\]);/s', $content, $matches)) {
        $castsArray = $matches[1];
        $replacement = "protected function casts(): array\n    {\n        return ".$castsArray.";\n    }";
        $content = str_replace($matches[0], $replacement, $content);
    }

    // 2. Add return types to relationships
    // First, make sure imports are there. We will just use fully qualified names or standard names,
    // but Laravel models usually have the import or we can add it. Actually, it's safer to use the return type
    // if the import is present, but let's just add the imports if missing.

    $relations = [
        'hasMany' => 'Illuminate\Database\Eloquent\Relations\HasMany',
        'belongsTo' => 'Illuminate\Database\Eloquent\Relations\BelongsTo',
        'hasOne' => 'Illuminate\Database\Eloquent\Relations\HasOne',
        'belongsToMany' => 'Illuminate\Database\Eloquent\Relations\BelongsToMany',
        'morphMany' => 'Illuminate\Database\Eloquent\Relations\MorphMany',
        'morphTo' => 'Illuminate\Database\Eloquent\Relations\MorphTo',
        'hasManyThrough' => 'Illuminate\Database\Eloquent\Relations\HasManyThrough',
    ];

    $imports = [];
    foreach ($relations as $method => $class) {
        // Find public functions that return this relation
        $pattern = '/public\s+function\s+([a-zA-Z0-9_]+)\s*\(\)\s*\{[^\}]*?return\s+\$this->'.$method.'\(/s';
        if (preg_match_all($pattern, $content, $matches)) {
            $imports[] = $class;
        }
    }

    $imports = array_unique($imports);

    // Insert imports if not exists
    foreach ($imports as $import) {
        if (strpos($content, "use $import;") === false) {
            // insert after namespace
            $content = preg_replace('/(namespace App\\\\Models;)/', "$1\nuse $import;", $content);
        }
    }

    // Add return types
    foreach ($relations as $method => $class) {
        $shortName = basename(str_replace('\\', '/', $class));
        $pattern = '/(public\s+function\s+[a-zA-Z0-9_]+\s*\(\))(\s*\{[^\}]*?return\s+\$this->'.$method.'\()/s';
        $content = preg_replace($pattern, "$1: $shortName$2", $content);
    }

    // There might be some edge cases where `{` is on the next line or multiple lines.
    // Let's refine the pattern
    foreach ($relations as $method => $class) {
        $shortName = basename(str_replace('\\', '/', $class));

        $lines = explode("\n", $content);
        $inFunc = false;
        $funcLine = -1;
        $hasReturnType = false;

        for ($i = 0; $i < count($lines); $i++) {
            if (preg_match('/public\s+function\s+([a-zA-Z0-9_]+)\s*\(\)\s*(:.*)?(\s*\{)?$/', trim($lines[$i]), $m)) {
                $inFunc = true;
                $funcLine = $i;
                $hasReturnType = isset($m[2]) && ! empty(trim($m[2]));
            }
            if ($inFunc) {
                if (preg_match('/return\s+\$this->'.$method.'\(/', $lines[$i])) {
                    if (! $hasReturnType) {
                        // find the function declaration and append : type
                        $lines[$funcLine] = preg_replace('/(public\s+function\s+[a-zA-Z0-9_]+\s*\(\))/', "$1: $shortName", $lines[$funcLine]);
                    }
                    $inFunc = false; // reset
                }
                if (strpos($lines[$i], '}') === 0 || preg_match('/^\s+\}$/', $lines[$i])) {
                    $inFunc = false;
                }
            }
        }
        $content = implode("\n", $lines);
    }

    // run another pass to be absolutely sure the lines approach worked
    // (We replaced content using lines)

    file_put_contents($file, $content);
}
echo 'Models refactored.';
