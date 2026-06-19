<?php

declare(strict_types=1);
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$routes = collect(app('router')->getRoutes())
    ->filter(static function ($route): bool {
        $uri = $route->uri();

        return str_starts_with($uri, 'api/')
            && ! str_contains($route->getActionName(), 'Illuminate\\');
    })
    ->map(static function ($route): array {
        $action = $route->getActionName();
        $module = 'app';
        if (preg_match('/Modules\\\\([^\\\\]+)\\\\/', $action, $m)) {
            $module = $m[1];
        } elseif (preg_match('/App\\\\Http\\\\Controllers\\\\Api/', $action)) {
            $module = 'Api';
        }

        $middleware = collect($route->gatherMiddleware())
            ->map(static fn ($m) => is_string($m) ? $m : (is_object($m) ? $m::class : (string) $m))
            ->values()
            ->all();

        return [
            'methods' => $route->methods(),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'middleware' => $middleware,
            'action' => $action,
            'module' => $module,
        ];
    })
    ->sortBy(static fn (array $r) => $r['uri'].implode(',', $r['methods']))
    ->values()
    ->all();

$byModule = [];
foreach ($routes as $route) {
    $byModule[$route['module']] ??= [];
    $byModule[$route['module']][] = $route;
}

$output = [
    'generated_at' => gmdate('c'),
    'source' => 'scripts/extract-api-routes.php',
    'route_count' => count($routes),
    'routes' => $routes,
    'routes_by_module' => $byModule,
];

$jsonPath = __DIR__.'/../docs/catalogs/api-routes-catalog.json';
file_put_contents($jsonPath, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");

echo "Wrote {$jsonPath}\n";
echo 'API routes: '.count($routes)."\n";
