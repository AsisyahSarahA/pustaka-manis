<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icon extends Component
{
    protected static array $cache = [];

    public function __construct(
        public string $name,
        public string $class = 'h-5 w-5',
        public bool $stroke = true,
    ) {}

    public function render(): View|Closure|string
    {
        $svg = static::load($this->name);

        if ($svg === null) {
            return '';
        }

        $size = $this->class !== '' ? ' class="' . e($this->class) . '"' : '';
        $strokeAttrs = $this->stroke ? ' stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"' : '';

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"' . $strokeAttrs . $size . '>' . $svg . '</svg>';
    }

    protected static function load(string $name): ?string
    {
        if (array_key_exists($name, static::$cache)) {
            return static::$cache[$name];
        }

        $path = base_path('node_modules/lucide-static/icons/' . $name . '.svg');

        if (!is_file($path)) {
            static::$cache[$name] = null;

            return null;
        }

        $svg = (string) file_get_contents($path);

        // Ekstrak isi di dalam <svg ...>...</svg>
        if (preg_match('/<svg[^>]*>(.*?)<\/svg>/s', $svg, $matches)) {
            static::$cache[$name] = $matches[1];

            return $matches[1];
        }

        static::$cache[$name] = null;

        return null;
    }
}