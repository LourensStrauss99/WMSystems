<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== Debug Route Registration ===\n\n";

echo "Current domain: " . request()->getHost() . "\n";
echo "Central domains: " . json_encode(config('tenancy.central_domains')) . "\n";
echo "Is central domain: " . (in_array(request()->getHost(), config('tenancy.central_domains')) ? 'Yes' : 'No') . "\n";

// Check if tenancy middleware is active
echo "Current middleware: " . json_encode(app('router')->getCurrentRoute()?->middleware() ?? []) . "\n";

// Check specific routes
$routes = Route::getRoutes();
$rootRoutes = [];

foreach ($routes as $route) {
    if ($route->uri() === '/') {
        $rootRoutes[] = [
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'middleware' => $route->middleware(),
            'domain' => $route->domain()
        ];
    }
}

echo "\nRoot routes found:\n";
print_r($rootRoutes);
