<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Route Debug Test ===\n\n";

// Check what routes are registered
$routes = app('router')->getRoutes();
$allRoutes = [];

foreach ($routes as $route) {
    $allRoutes[] = [
        'method' => implode('|', $route->methods()),
        'uri' => $route->uri(),
        'name' => $route->getName(),
        'action' => $route->getActionName(),
        'middleware' => $route->middleware(),
    ];
}

// Find routes for root path
$rootRoutes = array_filter($allRoutes, function($route) {
    return $route['uri'] === '/';
});

echo "Root routes (/):\n";
foreach ($rootRoutes as $route) {
    echo "- Method: {$route['method']}\n";
    echo "  Name: {$route['name']}\n";
    echo "  Action: {$route['action']}\n";
    echo "  Middleware: " . implode(', ', $route['middleware']) . "\n\n";
}

// Check for login routes
$loginRoutes = array_filter($allRoutes, function($route) {
    return strpos($route['uri'], 'login') !== false;
});

echo "Login related routes:\n";
foreach ($loginRoutes as $route) {
    echo "- {$route['method']} {$route['uri']} -> {$route['action']}\n";
}

// Check current configuration
echo "\nConfiguration:\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "Central domains: " . json_encode(config('tenancy.central_domains')) . "\n";
echo "Environment: " . config('app.env') . "\n";
