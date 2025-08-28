Route::get('/inventory/generate-code/{departmentPrefix}', [App\Http\Controllers\InventoryController::class, 'generateCode']);
<?php
use Illuminate\Support\Facades\Route;

Route::get('/purchase-orders/{id}/details', 'App\\Http\\Controllers\\Api\\PurchaseOrderController@details');
