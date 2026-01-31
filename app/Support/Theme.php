<?php

namespace App\Support;

class Theme
{
    public static function schema(string $template): array
    {
        $path = config_path("invite_themes/{$template}.php");
        if (!file_exists($path)) return [];
        return require $path;
    }

    public static function defaults(string $template): array
    {
        $schema = self::schema($template);
        $out = [];
        foreach ($schema as $key => $meta) {
            $out[$key] = $meta['default'] ?? null;
        }
        return $out;
    }

    public static function resolve(string $template, ?array $savedTheme): array
    {
        return array_replace_recursive(self::defaults($template), $savedTheme ?? []);
    }

    public static function allowedKeys(string $template): array
    {
        return array_keys(self::schema($template));
    }
}
