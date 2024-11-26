<?php

## Gerar JSON com os ícones do Material Icons a partir de um diretório com os ícones em SVG
function generate_icons_json($directory) {
    $types_map = [
        'materialicons' => 'normal',
        'materialiconsoutlined' => 'outline',
        'materialiconsround' => 'round',
        'materialiconssharp' => 'sharp',
        'materialiconstwotone' => 'two-tone',
    ];
    $icons = [];
    $types = glob($directory . '/*', GLOB_ONLYDIR);
    foreach ($types as $type) {
        $category_name = basename($type);
        $icons[$category_name] = [
            'normal' => [],
            'outline' => [],
            'sharp' => [],
            'round' => [],
            'two-tone' => [],
            'other' => [],
        ];

        $icons_directory = glob($type . '/*', GLOB_ONLYDIR);

        foreach ($icons_directory as $icon_dir) {
            $icon_name = basename($icon_dir);
            $icon_types = glob($icon_dir . '/*', GLOB_ONLYDIR);

            foreach ($icon_types as $icon_type) {
                $icon_type_name = basename($icon_type);
                $icon_type_name = $types_map[$icon_type_name] ?? 'other';

                $path_icon_file = glob($icon_type . '/*');
                $path_icon_file = $path_icon_file[1] ?? $path_icon_file[0];
                $svg_content = file_get_contents($path_icon_file);

                if (empty($svg_content)) {
                    continue;
                }

                $name = str_replace('_', ' ', $icon_name);
                $name = ucfirst($name);
                $icons[$category_name][$icon_type_name][$icon_name] = [
                    'name' => $name,
                    'svg_content' => $svg_content,
                ];
            }
        }

    }

    foreach ($icons as $category_name => $category) {
        if (is_dir(__DIR__ . '/icons') === false) {
            mkdir(__DIR__ . '/icons');
        }
        file_put_contents(__DIR__ . '/icons/' . 'icons-' . $category_name . '.json', json_encode($category, JSON_PRETTY_PRINT));
    }
}

$directory = __DIR__ . '/src';
echo "<pre>";
print_r(generate_icons_json($directory));
echo "<pre>";
exit();
