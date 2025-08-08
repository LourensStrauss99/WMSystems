<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debug Tenant Domain Access ===\n\n";

// Simulate a request to tenant domain
$_SERVER['HTTP_HOST'] = 'wmrs.workflow-management.test';
$_SERVER['SERVER_NAME'] = 'wmrs.workflow-management.test';

$request = Illuminate\Http\Request::createFromGlobals();
app()->instance('request', $request);

echo "Request host: " . $request->getHost() . "\n";
echo "Central domains: " . json_encode(config('tenancy.central_domains')) . "\n";
echo "Is central domain: " . (in_array($request->getHost(), config('tenancy.central_domains')) ? 'Yes' : 'No') . "\n";

// Check what routes are loaded
$routes = app('router')->getRoutes();
$rootRoutes = [];

foreach ($routes as $route) {
    if ($route->uri() === '/') {
        $rootRoutes[] = [
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'middleware' => $route->middleware(),
        ];
    }
}

echo "\nRoot routes for tenant domain:\n";
foreach ($rootRoutes as $route) {
    echo "- Name: {$route['name']}\n";
    echo "  Action: {$route['action']}\n";
    echo "  Middleware: " . implode(', ', $route['middleware']) . "\n\n";
}
